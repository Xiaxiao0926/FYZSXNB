# -*- coding: utf-8 -*-
"""feed_0361_unknown_table.py — prints the 13 unknown-locale posts table."""
import json, os, sys

HERE = os.path.dirname(os.path.abspath(__file__))
inv = json.load(open(os.path.join(HERE, "feed_036_inventory_report.json"), encoding="utf-8"))
rows = [r for r in inv["migration"] if r["legacy_locale"] == ""]
print("| ID | Title | Categories | Language | Kind | Action |")
print("|---:|-------|-----------:|:--------:|:----:|--------|")
for r in sorted(rows, key=lambda x: x["id"]):
    title = (r["title"] or "").replace("|", "\\|")[:70]
    cats = ",".join(str(c) for c in r["categories"])
    print(f"| {r['id']} | {title} | {cats} | UNKNOWN | (none) | manual review |")
