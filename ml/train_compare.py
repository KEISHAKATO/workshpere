#!/usr/bin/env python3
import sys
import json
import math
from pathlib import Path
from typing import Optional, Tuple, Dict, Any, List

import pandas as pd
import numpy as np

from sklearn.model_selection import train_test_split
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import RandomForestClassifier
from sklearn.dummy import DummyClassifier
from sklearn.metrics import (
    accuracy_score, precision_score, recall_score, f1_score, roc_auc_score,
    classification_report
)


# 
# Path resolution
# 
def find_csv(cli_arg: Optional[str]) -> Path:
    """
    Resolve the dataset path. Preference:
    - CLI arg if given
    - Common known locations (relative to this file)
    """
    here = Path(__file__).resolve().parent
    candidates: List[Path] = []

    if cli_arg:
        candidates.append(Path(cli_arg))

    # Common locations when running from ml/
    candidates.append(here / "../storage/app/private/ml/dataset.csv")
    candidates.append(here / "../storage/app/ml/dataset.csv")
    candidates.append(here / "dataset.csv")  # if user copied it here
    # In case someone runs from project root
    candidates.append(Path("storage/app/private/ml/dataset.csv"))
    candidates.append(Path("storage/app/ml/dataset.csv"))

    for p in candidates:
        if p.expanduser().resolve().exists():
            return p.expanduser().resolve()

    # If none found, raise a helpful error
    raise FileNotFoundError(
        "Could not find dataset.csv. Tried:\n" + "\n".join(str(p.resolve()) for p in candidates)
    )


# 
# Feature engineering
# 
def safe_skills_list(x) -> List[str]:
    """
    Convert a CSV cell into a list of skills. Handles:
    - JSON-like arrays (already parsed by Pandas as string)
    - Comma separated strings
    - NaNs
    """
    if isinstance(x, list):
        return [s.strip() for s in x if isinstance(s, str)]
    if pd.isna(x):
        return []
    s = str(x).strip()
    if s.startswith("[") and s.endswith("]"):
        # Try to parse JSON-ish list
        try:
            # Remove quotes cautiously then split by comma as fallback
            # (we avoid json.loads to keep this robust to odd chars)
            s2 = s.strip("[]")
            parts = [p.strip().strip('"').strip("'") for p in s2.split(",")]
            return [p for p in parts if p]
        except Exception:
            pass
    # Otherwise comma-separated
    if "," in s:
        return [p.strip() for p in s.split(",") if p.strip()]
    return [s] if s else []


def overlap_count(a: List[str], b: List[str]) -> int:
    if not a or not b:
        return 0
    sa = set([s.lower() for s in a])
    sb = set([s.lower() for s in b])
    return len(sa.intersection(sb))


def build_features(df: pd.DataFrame) -> Tuple[pd.DataFrame, pd.Series]:
    # Required target
    if "label" not in df.columns:
        raise KeyError("Expected a 'label' column with 0/1 values.")

    # Experience/skills/location basics
    exp_col = "experience_years"
    if exp_col not in df.columns:
        # some earlier drafts used exp_years; try to alias
        if "exp_years" in df.columns:
            df[exp_col] = df["exp_years"]
        else:
            df[exp_col] = 0

    df[exp_col] = pd.to_numeric(df[exp_col], errors="coerce").fillna(0)

    # Parse skills columns
    df["skills_list"] = df.get("skills", "").apply(safe_skills_list)
    df["skills_count"] = df["skills_list"].apply(len)

    # Location flag
    df["location_flag"] = df.get("location_county").notna().astype(int)

    # Job-required skills & overlap
    df["job_req_list"] = df.get("job_required_skills", "").apply(safe_skills_list)
    df["skill_overlap"] = [
        overlap_count(a, b) for a, b in zip(df["skills_list"], df["job_req_list"])
    ]

    # Job type one-hot if available
    X = pd.DataFrame({
        "experience_years": df[exp_col],
        "skills_count": df["skills_count"],
        "location_flag": df["location_flag"],
        "skill_overlap": df["skill_overlap"],
    })

    if "job_type" in df.columns:
        onehot = pd.get_dummies(df["job_type"], prefix="jobtype", dummy_na=True)
        X = pd.concat([X, onehot], axis=1)

    y = df["label"].astype(int)
    return X, y


# 
# Model training helpers
# 
def can_stratify(y: pd.Series, test_size: float) -> bool:
    """
    We can stratify only if each class has >= 2 samples and the split can allocate both classes to train & test.
    """
    vc = y.value_counts()
    if vc.min() < 2:
        return False
    n = len(y)
    test_n = int(math.floor(n * test_size))
    train_n = n - test_n
    # both train and test need at least 1 sample per class
    return (test_n >= 2) and (train_n >= 2)


def scores_dict(y_true, y_pred, y_prob=None) -> Dict[str, Any]:
    out = {
        "accuracy": float(accuracy_score(y_true, y_pred)),
        "precision": float(precision_score(y_true, y_pred, zero_division=0)),
        "recall": float(recall_score(y_true, y_pred, zero_division=0)),
        "f1": float(f1_score(y_true, y_pred, zero_division=0)),
    }
    # ROC AUC only if both classes present and we have probabilities
    if y_prob is not None and len(np.unique(y_true)) == 2:
        try:
            out["roc_auc"] = float(roc_auc_score(y_true, y_prob))
        except Exception:
            out["roc_auc"] = None
    else:
        out["roc_auc"] = None
    return out


def pick_best(metrics_lr: Dict[str, Any], metrics_rf: Dict[str, Any]) -> str:
    """Choose a winner; prioritize F1, then accuracy, then roc_auc if available."""
    def key(m):
        return (m.get("f1", 0.0), m.get("accuracy", 0.0), m.get("roc_auc") or 0.0)

    return "logreg" if key(metrics_lr) >= key(metrics_rf) else "rf"


# 
# Main
# 
def main():
    # Resolve CSV
    cli_csv = sys.argv[1] if len(sys.argv) > 1 else None
    csv_path = find_csv(cli_csv)
    print(f"CSV: {csv_path}")

    df = pd.read_csv(csv_path)
    print("Columns:", list(df.columns))

    X, y = build_features(df)

    # Tiny dataset handling
    TEST_SIZE = 0.5 if len(y) < 40 else 0.25
    stratify_flag = can_stratify(y, TEST_SIZE)

    if len(y) < 4 or y.nunique() < 2:
        print("Dataset too small or single-class. Training DummyClassifier on all data; metrics skipped.")
        d = DummyClassifier(strategy="most_frequent")
        d.fit(X, y)
        # Save
        out_model = Path(__file__).resolve().parent / "model.joblib"
        import joblib
        joblib.dump(d, out_model)
        # Metrics file
        metrics_path = Path(__file__).resolve().parent / "metrics_compare.txt"
        metrics_path.write_text("Dummy model trained due to tiny/single-class dataset.\n")
        print(f"Saved model to: {out_model}")
        print(f"Saved metrics to: {metrics_path}")
        return

    # Split
    X_tr, X_te, y_tr, y_te = train_test_split(
        X, y,
        test_size=TEST_SIZE,
        random_state=42,
        stratify=y if stratify_flag else None
    )
    print(f"Samples: total={len(y)}, train={len(y_tr)}, test={len(y_te)}")
    print("Split mode:", "stratified" if stratify_flag else "non_stratified")

    # If the train set by chance has a single class, switch to Dummy
    if y_tr.nunique() < 2:
        print("Train set has a single class. Using DummyClassifier on ALL data; metrics skipped.")
        d = DummyClassifier(strategy="most_frequent")
        d.fit(X, y)
        out_model = Path(__file__).resolve().parent / "model.joblib"
        import joblib
        joblib.dump(d, out_model)
        metrics_path = Path(__file__).resolve().parent / "metrics_compare.txt"
        metrics_path.write_text("Dummy model trained due to single-class train split.\n")
        print(f"Saved model to: {out_model}")
        print(f"Saved metrics to: {metrics_path}")
        return

    # Models
    logreg = LogisticRegression(
        max_iter=2000,
        class_weight="balanced",
        n_jobs=None,  # (ignored by LogisticRegression liblinear/saga difference; safe)
        solver="saga"  # good with many one-hots; falls back if not supported
    )
    rf = RandomForestClassifier(
        n_estimators=250,
        max_depth=None,
        class_weight="balanced",
        random_state=42,
        n_jobs=-1
    )

    # Train
    logreg.fit(X_tr, y_tr)
    rf.fit(X_tr, y_tr)

    # Predict
    y_pred_lr = logreg.predict(X_te)
    y_prob_lr = None
    if hasattr(logreg, "predict_proba"):
        y_prob_lr = logreg.predict_proba(X_te)[:, 1]

    y_pred_rf = rf.predict(X_te)
    y_prob_rf = None
    if hasattr(rf, "predict_proba"):
        y_prob_rf = rf.predict_proba(X_te)[:, 1]

    # Metrics
    metrics_lr = scores_dict(y_te, y_pred_lr, y_prob_lr)
    metrics_rf = scores_dict(y_te, y_pred_rf, y_prob_rf)

    print("\n=== Logistic Regression ===")
    print(json.dumps(metrics_lr, indent=2))
    print("\n=== Random Forest ===")
    print(json.dumps(metrics_rf, indent=2))

    # Full classification report (test)
    print("\nClassification report (LogReg):\n")
    print(classification_report(y_te, y_pred_lr, digits=4))
    print("\nClassification report (RandomForest):\n")
    print(classification_report(y_te, y_pred_rf, digits=4))

    # Pick best & save
    winner = pick_best(metrics_lr, metrics_rf)
    best_model = logreg if winner == "logreg" else rf

    # Save adjacent to script
    out_model = Path(__file__).resolve().parent / "model.joblib"
    metrics_path = Path(__file__).resolve().parent / "metrics_compare.txt"
    report = {
        "winner": winner,
        "logreg": metrics_lr,
        "random_forest": metrics_rf,
        "features": list(X.columns),
        "rows": len(df),
        "csv": str(csv_path),
        "stratified": stratify_flag,
        "test_size": TEST_SIZE
    }

    import joblib
    joblib.dump(best_model, out_model)
    metrics_path.write_text(json.dumps(report, indent=2))

    print(f"\nSaved BEST model ({winner}) to: {out_model}")
    print(f"Saved metrics to: {metrics_path}")


if __name__ == "__main__":
    main()
