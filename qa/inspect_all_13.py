import json
import os
import re

TARGET_IDS = [479, 470, 444, 435, 424, 411, 394, 388, 358, 355, 347, 213, 209]
SLUGS = {
    479: "nmpa-udi-2027-class2-devices-ivd-implementation-guide",
    470: "crp-saa-poct-antibiotic-stewardship-village-clinics",
    444: "russia-eaeu-ivd-registration-transition-2026-2028",
    435: "gacc-order-281-special-goods-2026",
    424: "national-anti-fraud-center-ai-content-identification-guide",
    411: "china-pharma-exports-2026-formulations-glp1-api",
    394: "plaud-baseband-engineer-ai-earbuds-signal-analysis",
    388: "shenzhen-biomed-special-items-import-export-process-2026",
    358: "waic-2026-agent-phone-robots-product-signal",
    355: "xiaomi-mijia-water-flosser-pro-product-signal",
    347: "kimi-k3-zhihu-open-source-model",
    213: "schweberegale",
    209: "20251013",
}

SEARCH_ROOTS = [
    r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\agent-handoff",
    r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\site-ops",
]

found_evidence = {pid: [] for pid in TARGET_IDS}

for sroot in SEARCH_ROOTS:
    for root, dirs, files in os.walk(sroot):
        for f in files:
            if f.endswith((".json", ".md")):
                fp = os.path.join(root, f)
                try:
                    with open(fp, "r", encoding="utf-8", errors="ignore") as fh:
                        content = fh.read()
                        for pid in TARGET_IDS:
                            slug_hint = SLUGS[pid]
                            if slug_hint in content.lower() or (f.endswith(".json") and f'"{pid}"' in content):
                                rel = os.path.relpath(fp, r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work")
                                found_evidence[pid].append((rel, f, content[:4000]))
                except Exception:
                    pass

for pid in TARGET_IDS:
    print(f"\n=======================================================")
    print(f"=== Post {pid}: {SLUGS[pid]} ===")
    print(f"=======================================================")
    ev_list = found_evidence[pid]
    print(f"Evidence files found: {len(ev_list)}")
    for rel_path, fname, snippet in ev_list[:5]:
        print(f"\n--- File: {rel_path} ---")
        if fname.endswith(".seo.json") or "quality" in fname or "manifest" in fname or "payload" in fname:
            try:
                d = json.loads(snippet)
                lang = d.get("language") or d.get("locale")
                kind = d.get("article_type") or d.get("tier") or d.get("content_kind")
                print(f"  Structured Data: language='{lang}', kind='{kind}', title='{d.get('title')}'")
            except Exception:
                # search for language and type keys
                m_lang = re.search(r'"language"\s*:\s*"([^"]+)"', snippet)
                m_type = re.search(r'"article_type"\s*:\s*"([^"]+)"', snippet)
                m_title = re.search(r'"title"\s*:\s*"([^"]+)"', snippet)
                print(f"  Extracted: lang={m_lang.group(1) if m_lang else None}, type={m_type.group(1) if m_type else None}, title={m_title.group(1) if m_title else None}")
        else:
            m_lang = re.search(r'"language"\s*:\s*"([^"]+)"', snippet)
            m_type = re.search(r'"article_type"\s*:\s*"([^"]+)"', snippet)
            if m_lang or m_type:
                print(f"  Extracted: lang={m_lang.group(1) if m_lang else None}, type={m_type.group(1) if m_type else None}")
            else:
                print(f"  Snippet: {snippet[:200].replace(chr(10), ' ')}")
