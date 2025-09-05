# ml/train_baseline.py
# Baseline matcher with tiny-dataset safeguards.
# - Loads CSV (default: ../storage/app/private/ml/dataset.csv)
# - Builds simple numeric features
# - Tries a train/test split; if train has a single class, falls back to DummyClassifier on ALL data
# - Prints accuracy/precision/recall + classification report when a real test set exists
# - Saves model to model.joblib and metrics to metrics.txt

import argparse
import json
import os
import sys
from typing import Tuple, Optional

import joblib
import numpy as np
import pandas as pd
from sklearn.dummy import DummyClassifier
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import (
    accuracy_score,
    precision_score,
    recall_score,
    classification_report,
)
from sklearn.model_selection import train_test_split

DEFAULT_CSV = os.path.join("..", "storage", "app", "private", "ml", "dataset.csv")
DEFAULT_MODEL_PATH = "model.joblib"
DEFAULT_METRICS_PATH = "metrics.txt"
RANDOM_STATE = 42
TEST_SIZE = 0.5  # with 2 rows, this yields 1/1 split; still might make train single-class

def coerce_binary_label(series: pd.Series) -> pd.Series:
    s = series.copy()
    if pd.api.types.is_numeric_dtype(s):
        uniq = set(pd.unique(s.dropna()))
        if uniq.issubset({0, 1}):
            return s.astype(int)

    mapping = {
        "accepted": 1, "accept": 1, "yes": 1, "true": 1, "hired": 1, "got_job": 1, "positive": 1,
        "rejected": 0, "reject": 0, "no": 0, "false": 0, "not_hired": 0, "negative": 0,
        "1": 1, "0": 0,
    }

    def map_val(x):
        if pd.isna(x):
            return np.nan
        if isinstance(x, (int, float)) and x in (0, 1):
            return int(x)
        sx = str(x).strip().lower()
        if sx in mapping:
            return mapping[sx]
        return str(x).strip()

    return s.apply(map_val)

def safe_train_test_split(
    X: pd.DataFrame,
    y: pd.Series,
    test_size: float,
    random_state: int = RANDOM_STATE,
) -> Tuple[pd.DataFrame, Optional[pd.DataFrame], pd.Series, Optional[pd.Series], str]:
    n = len(X)
    if n < 2:
        return X, None, y, None, "too_small"

    min_test = max(int(round(test_size * n)), 1)
    adj_test_size = min_test / n

    # If possible, try stratified (both classes ≥ 2). With tiny data this usually won't hold.
    try:
        if y.nunique() >= 2 and all(y.value_counts() >= 2):
            X_tr, X_te, y_tr, y_te = train_test_split(
                X, y, test_size=adj_test_size, random_state=random_state, stratify=y
            )
            return X_tr, X_te, y_tr, y_te, "stratified"
    except Exception:
        pass

    try:
        X_tr, X_te, y_tr, y_te = train_test_split(
            X, y, test_size=adj_test_size, random_state=random_state, stratify=None
        )
        return X_tr, X_te, y_tr, y_te, "non_stratified"
    except Exception:
        return X, None, y, None, "too_small"

def parse_skills(cell) -> list:
    if pd.isna(cell):
        return []
    if isinstance(cell, list):
        return [str(x).strip() for x in cell if str(x).strip()]
    s = str(cell).strip()
    if not s:
        return []
    if s.startswith("[") and s.endswith("]"):
        try:
            arr = json.loads(s)
            if isinstance(arr, list):
                return [str(x).strip() for x in arr if str(x).strip()]
        except Exception:
            pass
    return [t.strip() for t in s.split(",") if t.strip()]

def build_features(df: pd.DataFrame):
    need = ["experience_years", "skills", "location_county", "label"]
    missing = [c for c in need if c not in df.columns]
    if missing:
        raise KeyError(f"Missing required columns in dataset: {missing}")

    df["experience_years"] = pd.to_numeric(df["experience_years"], errors="coerce").fillna(0)
    df["skills_list"] = df["skills"].apply(parse_skills)
    df["skills_count"] = df["skills_list"].apply(len)
    df["location_flag"] = df["location_county"].notna().astype(int)

    y = coerce_binary_label(df["label"])
    X = df[["experience_years", "skills_count", "location_flag"]].copy()
    X = X.replace([np.inf, -np.inf], np.nan).fillna(0)
    return X, y

def load_data(csv_path: str) -> pd.DataFrame:
    if not os.path.exists(csv_path):
        print(f"ERROR: CSV not found at {csv_path}", file=sys.stderr)
        sys.exit(1)
    df = pd.read_csv(csv_path)
    if df.empty:
        print("ERROR: CSV has no rows.", file=sys.stderr)
        sys.exit(1)
    return df

def main():
    parser = argparse.ArgumentParser(description="Train baseline matcher")
    parser.add_argument("--csv", default=DEFAULT_CSV)
    parser.add_argument("--model", default=DEFAULT_MODEL_PATH)
    parser.add_argument("--metrics", default=DEFAULT_METRICS_PATH)
    args = parser.parse_args()

    df = load_data(args.csv)
    X, y = build_features(df)

    print("=== Baseline Logistic Regression (numeric features) ===")
    print(f"CSV: {args.csv}")
    print(f"Columns: {list(df.columns)}")
    print(f"Class distribution: {y.value_counts(dropna=False).to_dict()}")

    X_tr, X_te, y_tr, y_te, mode = safe_train_test_split(X, y, TEST_SIZE, RANDOM_STATE)
    total = len(X)
    n_tr = len(X_tr)
    n_te = 0 if X_te is None else len(X_te)
    print(f"Samples: total={total}, train={n_tr}, test={n_te}")
    print(f"Split mode: {mode}")

    metrics_lines = []

    # If train set has only one class, LR will fail —> fall back to Dummy on ALL data.
    if y_tr.nunique() < 2:
        print("Train set has a single class. Using DummyClassifier(most_frequent) on ALL data; metrics skipped.")
        clf = DummyClassifier(strategy="most_frequent")
        clf.fit(X, y)
        joblib.dump(clf, args.model)
        with open(args.metrics, "w") as f:
            f.write("DummyClassifier trained (single-class train set). No hold-out metrics.\n")
        print(f"\nSaved model to: {args.model}")
        print(f"Saved metrics to: {args.metrics}")
        return

    # Otherwise, train Logistic Regression
    clf = LogisticRegression(max_iter=1000, random_state=RANDOM_STATE)
    clf.fit(X_tr, y_tr)

    if X_te is None or y_te is None or len(X_te) == 0:
        print("Dataset too small to hold out a test set. Trained on all data; skipping metrics.")
        metrics_lines.append("No hold-out metrics (dataset too small).")
    else:
        y_pred = clf.predict(X_te)
        try:
            avg = "binary" if y.nunique() == 2 else "macro"
            acc = accuracy_score(y_te, y_pred)
            prec = precision_score(y_te, y_pred, zero_division=0, average=avg)
            rec = recall_score(y_te, y_pred, zero_division=0, average=avg)
        except ValueError:
            acc = accuracy_score(y_te, y_pred)
            prec = precision_score(y_te, y_pred, zero_division=0, average="macro")
            rec = recall_score(y_te, y_pred, zero_division=0, average="macro")

        print(f"Accuracy:  {acc:.4f}")
        print(f"Precision: {prec:.4f}")
        print(f"Recall:    {rec:.4f}")
        report = classification_report(y_te, y_pred, zero_division=0)
        print("\nClassification report:\n")
        print(report)

        metrics_lines.extend([
            f"Samples: total={total}, train={n_tr}, test={n_te}",
            f"Split mode: {mode}",
            f"Accuracy: {acc:.4f}",
            f"Precision: {prec:.4f}",
            f"Recall: {rec:.4f}",
            "",
            "Classification report:",
            report,
        ])

    joblib.dump(clf, args.model)
    with open(args.metrics, "w") as f:
        f.write("\n".join(metrics_lines) if metrics_lines else "No metrics (trained on all data).")

    print(f"\nSaved model to: {args.model}")
    print(f"Saved metrics to: {args.metrics}")

if __name__ == "__main__":
    main()
