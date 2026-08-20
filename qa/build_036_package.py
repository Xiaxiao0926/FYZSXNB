# -*- coding: utf-8 -*-
"""build_036_package.py — assembles the 0.3.6 deployment package with the
two-stage plugin (interim v1.1.0 -> final v1.2.0) and the manifest."""
import hashlib, json, os
from datetime import datetime, timezone

REPO = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..")
PKG = os.path.join(REPO, "..", "deployments", "fyzsxnb-ui2-036")
CANONICAL = os.path.join(REPO, "plugin", "fyzsxnb-home-dynamic-feeds", "fyzsxnb-home-dynamic-feeds.php")
PROD_COPY = os.path.join(REPO, "..", "deployments", "fyzsxnb-home-dynamic-feeds", "fyzsxnb-home-dynamic-feeds.php")
INTERIM = os.path.join(PKG, "interim", "fyzsxnb-home-dynamic-feeds.php")

REMOTE = "wp-content/plugins/fyzsxnb-home-dynamic-feeds/fyzsxnb-home-dynamic-feeds.php"


def sha(path):
    h = hashlib.sha256()
    with open(path, "rb") as fh:
        for chunk in iter(lambda: fh.read(1 << 16), b""):
            h.update(chunk)
    return h.hexdigest().upper()


def entry(path, action, baseline=None):
    return {
        "path": REMOTE,
        "action": action,
        "sha256": sha(path),
        "bytes": os.path.getsize(path),
        "baseline_sha256": baseline,
    }


final_dir = os.path.join(PKG, "final-v1.2.2")
os.makedirs(final_dir, exist_ok=True)
final_path = os.path.join(final_dir, "fyzsxnb-home-dynamic-feeds.php")
with open(CANONICAL, encoding="utf-8") as fh:
    with open(final_path, "w", encoding="utf-8", newline="\n") as out:
        out.write(fh.read())

prod_sha = sha(PROD_COPY) if os.path.exists(PROD_COPY) else None
# Stage 2/3 deployed artifacts are fixed hashes; local copies superseded.
V120_SHA = "AE10E73365E66BFC4B3B5E0AF02656CAA11E4808096BB0EC3CCA8C9FD96E74E2"
V120_BYTES = 16752
V121_SHA = "ED9AC65D36A18BB7FEA6C5D9B0C30AE9F50668DE3E2EE078880093D136A45B4B"
V121_BYTES = 17169
manifest = {
    "package": "fyzsxnb-ui2-036",
    "phase": "UI V2 0.3.6 — Feed Hardening (content data layer governance)",
    "stages": [
        {
            "stage": "1-interim",
            "plugin_version": "1.1.0",
            "description": "Explicit meta registration + cache + precise invalidation + QA endpoints + decision trace; decision path keeps the v1.0.0 legacy fallback until backfill.",
            "files": [entry(INTERIM, "replace", prod_sha)],
        },
        {
            "stage": "2-final",
            "plugin_version": "1.2.0",
            "description": "Explicit-only decision path (no heuristic). Same cache keys/version h3; homepage parity guaranteed by backfill.",
            "files": [{"path": REMOTE, "action": "replace", "sha256": V120_SHA, "bytes": V120_BYTES, "baseline_sha256": sha(INTERIM)}],
        },
        {
            "stage": "3-final-1.2.1",
            "plugin_version": "1.2.1",
            "description": "Adds LiteSpeed class-API URL purge (\\LiteSpeed\\Purge::purge_url) alongside the action hook, because URL-level purge via action only is unreliable on Hostinger + LSCWP 7.9. No decision-path changes.",
            "files": [{"path": REMOTE, "action": "replace", "sha256": V121_SHA, "bytes": V121_BYTES, "baseline_sha256": V120_SHA}],
        },
        {
            "stage": "4-final-1.2.2",
            "plugin_version": "1.2.2",
            "description": "QA REST routes marked no-cache (nocache_headers + Cache-Control: no-store + litespeed_control_set_nocache) and REST URLs added to the purge list — LiteSpeed had cached an authenticated feed-state 200 and served it anonymously. No decision-path changes.",
            "files": [entry(final_path, "replace", V121_SHA)],
        },
    ],
    "created_at": datetime.now(timezone.utc).isoformat(),
}
with open(os.path.join(PKG, "DEPLOYMENT_MANIFEST.json"), "w", encoding="utf-8") as fh:
    json.dump(manifest, fh, ensure_ascii=False, indent=2)
print(json.dumps({
    "prod_v100_sha": prod_sha,
    "interim_v110": {"sha": manifest["stages"][0]["files"][0]["sha256"], "bytes": manifest["stages"][0]["files"][0]["bytes"]},
    "final_v120": {"sha": manifest["stages"][1]["files"][0]["sha256"], "bytes": manifest["stages"][1]["files"][0]["bytes"]},
    "manifest": os.path.join(PKG, "DEPLOYMENT_MANIFEST.json"),
}, indent=2))
