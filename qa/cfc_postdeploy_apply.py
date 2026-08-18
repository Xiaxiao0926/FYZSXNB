#!/usr/bin/env python
"""cfc_postdeploy_apply.py — apply CFC post-upload steps via REST (write scope: page template + read taxonomies)."""
from __future__ import annotations
import base64, json, os
from urllib.parse import urlencode
from urllib.request import Request, urlopen
from urllib.error import HTTPError

SITE = os.environ.get("FYZSXNB_SITE_URL", "https://fyzsxnb.com").rstrip("/")
API = f"{SITE}/wp-json/wp/v2"
TEMPLATE = "page-templates/cars-from-china-hub.php"

def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}

def call(method, path, payload=None):
    data = json.dumps(payload).encode() if payload is not None else None
    h = {"User-Agent": "fyz-cfc-postdeploy/0.1", "Accept": "application/json"}
    if auth(): h.update(auth())
    if data is not None: h["Content-Type"] = "application/json"
    try:
        with urlopen(Request(f"{API}{path}", data=data, headers=h, method=method), timeout=45) as r:
            txt = r.read().decode("utf-8", "replace")
            return r.status, (json.loads(txt) if txt else {})
    except HTTPError as e:
        return e.code, (json.loads(e.read().decode("utf-8", "replace")) if e.headers.get("Content-Type","").startswith("application/json") else e.read().decode("utf-8","replace")[:300])

report = {"step": "post-deploy apply", "template": TEMPLATE}

# 1) Page 507: confirm draft + assign template
st, page = call("GET", f"/pages?{urlencode({'slug': 'cars-from-china', 'status': 'any', '_fields': 'id,slug,status,template'})}")
if isinstance(page, list) and page:
    pid = page[0]["id"]
    report["page"] = {"id": pid, "slug": page[0]["slug"], "status": page[0]["status"], "template_before": page[0]["template"]}
    st2, upd = call("PATCH", f"/pages/{pid}", {"template": TEMPLATE})
    report["template_assign"] = {"http": st2, "template_after": upd.get("template") if isinstance(upd, dict) else upd}
else:
    report["page"] = {"error": page}

# 2) fyz_vehicle terms (seeded on init post-deploy) — 6 brands + 10 models expected
st3, terms = call("GET", "/fyz_vehicle?per_page=100&_fields=id,name,slug,parent")
if isinstance(terms, list):
    brands = [t for t in terms if not t["parent"]]
    models = [t for t in terms if t["parent"]]
    report["fyz_vehicle"] = {"http": st3, "total": len(terms), "brands": len(brands), "models": len(models),
                             "brand_slugs": sorted(t["slug"] for t in brands),
                             "model_slugs": sorted(t["slug"] for t in models),
                             "orphan_models": [t["slug"] for t in models if not any(b["id"] == t["parent"] for b in brands)]}
    orphan = [t["slug"] for t in models if not any(b["id"] == t["parent"] for b in brands)]
    report["fyz_vehicle"]["orphan_models"] = orphan
else:
    report["fyz_vehicle"] = {"http": st3, "error": terms}

# 3) fyz_research_type terms — 7 expected
st4, rtypes = call("GET", "/fyz_research_type?per_page=100&_fields=id,name,slug")
if isinstance(rtypes, list):
    report["fyz_research_type"] = {"http": st4, "total": len(rtypes), "slugs": sorted(t["slug"] for t in rtypes)}
else:
    report["fyz_research_type"] = {"http": st4, "error": rtypes}

print(json.dumps(report, ensure_ascii=False, indent=2))
