import os
import re
import json

TARGET_PATTERNS = [
    r"_fyz_content_language",
    r"_fyz_content_kind",
    r"fyzsxnb_is_russian_target",
    r"fyzsxnb_get_russian_post_ids",
    r"fyzsxnb_is_russian_view",
    r"fyzsxnb_resolve_content_locale",
    r"language_attributes",
    r"aioseo_facebook_tags",
    r"aioseo_schema_output",
    r"inLanguage",
    r"og:locale",
    r"ru_RU",
    r"ru-RU",
    r"zh-CN",
    r"zh_CN",
]

SCAN_ROOTS = [
    r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\fyzsxnb-ui-v2",
    r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\site-ops",
]

matches = {pat: [] for pat in TARGET_PATTERNS}

for sroot in SCAN_ROOTS:
    for root, dirs, files in os.walk(sroot):
        if any(d in root for d in [".git", "node_modules", "vendor", "_archive"]):
            continue
        for f in files:
            if f.endswith((".php", ".py", ".js", ".json", ".md")):
                fp = os.path.join(root, f)
                rel = os.path.relpath(fp, r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work")
                try:
                    with open(fp, "r", encoding="utf-8", errors="ignore") as fh:
                        lines = fh.readlines()
                        for i, line in enumerate(lines, 1):
                            for pat in TARGET_PATTERNS:
                                if re.search(pat, line):
                                    matches[pat].append({
                                        "file": rel,
                                        "line": i,
                                        "code": line.strip()[:150]
                                    })
                except Exception:
                    pass

print("=== Implementation Boundary Audit Scan ===")
for pat, hits in matches.items():
    print(f"\nPattern: {pat} (Hits: {len(hits)})")
    for h in hits[:6]:
        print(f"  {h['file']}:{h['line']} -> {h['code']}")
