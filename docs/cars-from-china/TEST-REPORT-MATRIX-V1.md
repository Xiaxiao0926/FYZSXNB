# Cars from China — TEST REPORT (Matrix V1 architecture)

> Task `FYZSXNB-CARS-FROM-CHINA-MATRIX-V1` — architecture phase.
> Everything below was **actually executed** (no "should pass" claims).
> Scope: code scaffold + QA. No content research was done.

## 1. What was tested

| # | Check | Tool | Result |
|---|---|---|---|
| 1 | PHP lint of all 4 changed/new theme PHP files | `php -l` (portable PHP 8.5.9) | ✅ No syntax errors |
| 2 | Structural contract (27 assertions: taxonomies, rewrite rules, RU hub hook, parent 404 validation, category-54 constant, empty-section suppression, no dead links, no fabricated counts, no commerce, brace balance, includes/enqueue, file existence, template dispatch, CSS media queries, CSS overflow-safe) | `qa/cars-from-china-structural-checks.ps1` | ✅ **27/27 PASS** |
| 3 | Visual QA — static model-page preview with REAL theme CSS (`design-system.css` + `cars-from-china.css`) at 1440 / 1024 / 768 / 390: single `<h1>`, 3 matrix rows, 2 cards, empty sections suppressed, no horizontal overflow | `qa/cfc-visual-qa.cjs` (headless Chromium) | ✅ 4/4 PASS |
| 4 | SEO regression against live site (read-only): hub not public, not in sitemap, draft status, existing pages healthy | `qa/cars_from_china_seo_regression.py` via secure publisher wrapper | ✅ all PASS |

## 2. Structural check detail (27/27)

`qa/cars-from-china-structural-checks.ps1` assertions:

- register `fyz_vehicle` / `fyz_research_type`; hierarchical; rewrite=false (custom); query_var
- 5 rewrite rules (RU first); RU hub template hook; parent validation 404
- category 54 constant; empty-section suppression; `has_published` (no dead links)
- initial matrix (volkswagen …); seed-terms idempotent guard; research-types map; ru lang detection
- no fabricated-counts text; no commerce/Cart text; brace balance 85/85
- `functions.php` includes inc file; enqueues `cars-from-china.css`
- hub template file exists + calls `render_hub`; taxonomy template brand/model dispatch
- CSS media queries (768/420); CSS overflow-safe (no fixed 4-digit px widths; `max-width:`/`min-width:` media queries excluded from the width check)

## 3. Visual QA detail

`qa/cfc-visual-qa.cjs` loads `preview/cars-from-china-model-preview.html` (TEST ONLY, noindex) with the real theme stylesheets and screenshots to `qa/screenshots/cfc/`:

| Viewport | H1 | Sections rendered | Cards | Matrix rows | Overflow |
|---|---|---|---|---|---|
| 1440 | 1 | Latest Research, Parts & Compatibility, Model Matrix | 2 | 3 | no |
| 1024 | 1 | same | 2 | 3 | no |
| 768 | 1 | same | 2 | 3 | no |
| 390 | 1 | same | 2 | 3 | no |

Owner Cases / Common Problems / Case Studies sections are deliberately absent in this mock — proving empty-section suppression works (evidence-first).

Screenshots: `qa/screenshots/cfc/cfc-model-desktop-1440.png`, `...-tablet_1024-1024.png`, `...-tablet_768-768.png`, `...-mobile_390-390.png`.

## 4. SEO regression detail (read-only, against live site)

Executed via `site-ops/run_wp_publisher_secure.ps1` (secure credential injection; nothing written to the site):

- `https://fyzsxnb.com/cars-from-china/` → **404** (hub page is draft — not public)
- `https://fyzsxnb.com/ru/cars-from-china/` → **404** (RU hub rewrite not deployed)
- `sitemap.xml` → 200, does **not** contain `cars-from-china`
- REST `pages?slug=cars-from-china&status=any` → id 507, status **draft**
- Existing key pages (openpilot BYD cluster ×3 + `/ru/`): HTTP 200, `lang=ru-RU`, canonical self-referencing, single `<h1>`, robots meta `max-image-preview:large` intact

**Conclusion: the scaffold made zero production-visible changes; existing SEO is untouched.**

## 5. Known limitations / notes

- Screenshots were produced in a local headless Chromium against the static
  preview; the production header/footer (Neve) is not part of the preview.
  Full in-theme rendering must be re-verified at deploy time (see
  ARCHITECTURE.md §9).
- Model/brand term seeding runs on `init` only after the theme is deployed to
  the target site; not exercised against prod yet (by design — nothing is
  deployed in this phase).
- The RU hub is rewrite-driven (`fyz_cfc_ru_hub=1` + `template_include`);
  its 404 status on prod today is the expected pre-deploy state.

## 6. Deliverables in this phase

- Theme: `inc/cars-from-china.php`, `page-templates/cars-from-china-hub.php`,
  `taxonomy-fyz_vehicle.php`, `assets/css/cars-from-china.css`,
  `functions.php` edit
- Docs: `CARS-FROM-CHINA-ARCHITECTURE.md`, `CARS-FROM-CHINA-CASE-CONTRACT.md`,
  `CARS-FROM-CHINA-CONTENT-CONTRACT.md` (in `docs/cars-from-china/`)
- QA: structural checks ps1, SEO regression py, visual QA cjs + 4 screenshots,
  static preview html
- Prod draft hub page id 507 (draft, no template assigned yet — assigned at
  deploy; noindex stays until launch gate is met)