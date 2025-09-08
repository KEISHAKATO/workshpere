import argparse, json, sys
from pathlib import Path
import pandas as pd
import joblib

def _skills_to_list(s):
    if s is None:
        return []
    if isinstance(s, list):
        return s
    return [p.strip() for p in str(s).split(",") if p.strip()]

def featurize_rows(rows):
    df = pd.DataFrame(rows)
    # training-compatible features
    df["experience_years"] = pd.to_numeric(df.get("experience_years", 0)).fillna(0)
    df["skills_list"] = df.get("skills", []).apply(_skills_to_list)
    df["skills_count"] = df["skills_list"].apply(len)

    # if location_county is non-empty
    df["location_flag"] = df.get("location_county", "").fillna("").astype(str).apply(lambda x: 0 if x.strip()=="" else 1)
    return df[["experience_years","skills_count","location_flag"]]

def score_seeker(model, seeker, jobs):
    # build paired rows (one row per job for this seeker)
    rows = []
    for j in jobs:
        rows.append({
            "experience_years": seeker.get("experience_years", 0),
            "skills": seeker.get("skills", []),
            "location_county": seeker.get("location_county", ""),
            "_job_id": j["id"],
        })
    X = featurize_rows(rows)
    proba = model.predict_proba(X)[:, 1] if hasattr(model, "predict_proba") else model.predict(X)
    out = [{"job_id": r["_job_id"], "score": float(p)} for r, p in zip(rows, proba)]
    # sort high to low
    out.sort(key=lambda x: x["score"], reverse=True)
    return out

def score_job(model, job, seekers):
    rows = []
    for s in seekers:
        rows.append({
            "experience_years": s.get("experience_years", 0),
            "skills": s.get("skills", []),
            "location_county": s.get("location_county", ""),
            "_user_id": s["id"],
        })
    X = featurize_rows(rows)
    proba = model.predict_proba(X)[:, 1] if hasattr(model, "predict_proba") else model.predict(X)
    out = [{"user_id": r["_user_id"], "score": float(p)} for r, p in zip(rows, proba)]
    out.sort(key=lambda x: x["score"], reverse=True)
    return out

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--mode", choices=["seeker","job"], required=True,
                    help="seeker: score jobs for one seeker; job: score seekers for one job")
    ap.add_argument("--model", default=str(Path(__file__).parent / "model.joblib"))
    ap.add_argument("--seeker-json", help="JSON file or inline JSON for a seeker profile")
    ap.add_argument("--job-json", help="JSON file or inline JSON for a job")
    ap.add_argument("--jobs-json", help="JSON file of jobs array")
    ap.add_argument("--seekers-json", help="JSON file of seekers array")
    ap.add_argument("--topk", type=int, default=8)
    args = ap.parse_args()

    model = joblib.load(args.model)

    def load_json_maybe(path_or_json):
        p = Path(path_or_json)
        if p.exists():
            with open(p, "r") as f:
                return json.load(f)
        return json.loads(path_or_json)

    if args.mode == "seeker":
        if not args.seeker-json or not args.jobs-json:
            print(json.dumps({"error":"missing seeker-json or jobs-json"}))
            sys.exit(1)
        seeker = load_json_maybe(args.seeker_json)
        jobs = load_json_maybe(args.jobs_json)
        out = score_seeker(model, seeker, jobs)[:args.topk]
        print(json.dumps(out))
    else:
        if not args.job-json or not args.seekers-json:
            print(json.dumps({"error":"missing job-json or seekers-json"}))
            sys.exit(1)
        job = load_json_maybe(args.job_json)
        seekers = load_json_maybe(args.seekers_json)
        out = score_job(model, job, seekers)[:args.topk]
        print(json.dumps(out))

if __name__ == "__main__":
    main()
