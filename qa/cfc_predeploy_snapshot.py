#!/usr/bin/env python
"""cfc_predeploy_snapshot.py — READ-ONLY pre-deploy snapshot for CFC matrix deploy."""
from __future__ import annotations
import base64, json, os
from urllib.parse import urlencode
from urllib.request import Request, urlopen
from urllib.error import HTTPError

SITE = os.environ.get("FYZSXNB_SITE_URL", "https://fyzsxnb.com").rstrip("/")
API = f"{SITE}/wp-json/wp/v2"

def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}

def get_json(path, headers=None):
    h = {"User-Agent": "fyz-cfc-predeploy/0.1", "Accept": "application/json"}
    if headers: h.update(headers)
    try:
        with urlopen(Request(f"{API}{path}", headers=h), timeout=45) as r:
            txt = r.read().decode("utf-8", "replace")
            return json.loads(txt) if txt else []
    except HTTPError as e:
        return {"http_error": e.code, "body": e.read().decode("utf-8", "replace")[:200]}

def fetch(url):
    try:
        with urlopen(Request(url, headers={"User-Agent": "fyz-cfc-predeploy/0.1", "Cache-Control": "no-cache"}), timeout=45) as r:
            return r.status
    except HTTPError as e:
        return e.code

snap = {"captured_at": None, "pre_deploy": True}
from datetime import datetime, timezone
snap["captured_at"] = datetime.now(timezone.utc).isoformat()

ah = auth()
# 1) page 507 state
pages = get_json(f"/pages?{urlencode({'slug': 'cars-from-china', 'status': 'any', '_fields': 'id,slug,status,template'})}", ah)
snap["page_507"] = pages if isinstance(pages, list) else pages
# 2) fyz_vehicle taxonomy registered?
taxes = get_json("/taxonomies?per_page=100")
if isinstance(taxes, dict) and "fyz_vehicle" in taxes:
    snap["fyz_vehicle_registered"] = True
elif isinstance(taxes, list):
    snap["fyz_vehicle_registered"] = any(t.get("rest_base") == "fyz_vehicle" or t.get("slug") == "fyz_vehicle" for t in taxes)
else:
    snap["fyz_vehicle_registered"] = taxes
# 3) asset availability
snap["assets"] = {
    "cars_from_china_css": fetch(f"{SITE}/wp-content/themes/fyzsxnb-neve-child/assets/css/cars-from-china.css"),
    "research_wire_css": fetch(f"{SITE}/wp-content/themes/fyzsxnb-neve-child/assets/css/research-wire.css"),
    "design_system_css": fetch(f"{SITE}/wp-content/themes/fyzsxnb-neve-child/assets/css/design-system.css"),
}
# 4) public entry points
snap["public"] = {
    "en_hub": fetch(f"{SITE}/cars-from-china/"),
    "ru_hub": fetch(f"{SITE}/ru/cars-from-china/"),
}
print(json.dumps(snap, ensure_ascii=False, indent=2))