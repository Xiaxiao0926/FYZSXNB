# FYZSXNB Automotive Article 002 — Visual Plan

## Hero (featured image)

**Scene (photorealistic, generated, clearly illustrative):** an independent Russian repair workshop (СТО) white-lit calibration bay; a modern Chinese-brand SUV (generic silhouette — no logo, no identifiable model) parked in front of an ADAS calibration frame with target boards; a technician in workshop clothes holding a diagnostic tablet facing the screen of a multi-brand scanner. Concrete floor, natural daylight mixed with bay lighting; believable Eastern-European workshop environment.

**Style rules:** realistic photography look; no huge text overlays; no HUD/PPT infographic style; no dark CAD look; no fake news-photo claim. Alt/caption must state "Illustrative image, not a real workshop or vehicle event."

**Usage:** featured image for Article 002; OG/Twitter card.

## Figure 1 — Illustrative workflow ("Repair → verify")

Concept diagram, labelled **"Illustrative workflow"**:

```
Repair event (glass / camera / radar / bumper / alignment / suspension)
        ↓
Sensor or vehicle-geometry relationship changed
        ↓
Diagnostic check
        ↓
OEM-specific calibration requirement? (model-dependent)
        ↓
Verify system function
```

**Style:** clean line diagram, 4-5 nodes, arrow vertical flow, FYZSXNB design-system neutral palette (ink/blue accent #174bb8). Explicit tag: "Illustrative workflow — no universal procedure implied."

## Figure 2 — Russian aftermarket capability stack

Vertical stack diagram:

```
Vehicle
  ↓
Diagnostics
  ↓
OEM procedure / data
  ↓
ADAS calibration equipment
  ↓
Workshop technician
  ↓
Parts / glass / sensor supply
```

**Style:** stacked blocks with a "gap" marker highlighting layers 2–3 (procedure/data + diagnostics) as the strained ones (anchor: article §4). Neutral palette, red/amber gap marker.

## Figure 3 — China → Russia solution stack

Diagram showing the opportunity architecture:

```
Chinese equipment      ——————————┐
Chinese documentation  ——————————┤
Chinese training       ——————————┼—→  Local Russian workshops
Chinese parts          ——————————┤
Model-level data access —————————┘
```

**Style:** three-column "supply → integration → local outcome" flow; blue accent; caption: "Solution architecture direction — not a vendor endorsement; VIN-level verification remains a workshop duty."

## Compliance

- No fabricated photographic "real case" images; hero is clearly illustrative.
- All schematic figures carry "Illustrative" labelling.
- Image assets to be produced only after GPT-5.6 visual review approval (IMAGE_PLANNING_ALLOWED=YES, IMAGE_UPLOAD_ALLOWED=NO until Publish Gate).