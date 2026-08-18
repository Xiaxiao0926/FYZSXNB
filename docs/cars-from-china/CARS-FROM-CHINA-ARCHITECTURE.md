# Cars from China — Architecture (V1 Matrix)

> Part of task `FYZSXNB-CARS-FROM-CHINA-MATRIX-V1` (architecture only — no content research yet).
> Workspace: `fyzsxnb-ui-v2` → child theme `fyzsxnb-neve-child`.
> Status: **implemented in code, not deployed to production theme yet.**

---

## 1. What this is

A content architecture scaffold for the **Cars from China** research desk: two
lightweight taxonomies, hierarchical public URLs (EN + RU), and
evidence-first page renderers. It creates **structure only**. It never
fabricates cases, common problems, counts, or parts conclusions, and it never
renders an empty section.

The desk lives entirely inside the existing child theme. There is **no new
plugin** and **no database migration beyond taxonomy seeding** (option-guarded,
idempotent).

---

## 2. Files

| File | Role |
|---|---|
| `theme/fyzsxnb-neve-child/inc/cars-from-china.php` | All CFC logic (taxonomies, rewrites, renderers, queries). `define('FYZSXNB_CFC_VERSION','0.1.0')`, `define('FYZSXNB_CFC_CATEGORY_RU',54)`. |
| `theme/fyzsxnb-neve-child/functions.php` | `require_once` of the inc file + `fyzsxnb_cfc_enqueue_styles()` (enqueues `cars-from-china.css` after `research-wire.css`). |
| `theme/fyzsxnb-neve-child/page-templates/cars-from-china-hub.php` | Template "Cars from China Hub". Renders `fyzsxnb_cfc_render_hub()`. Used by EN page 507 and by the RU hub rewrite. |
| `theme/fyzsxnb-neve-child/taxonomy-fyz_vehicle.php` | Archive template for `fyz_vehicle`. Dispatches brand vs model by `parent`. |
| `theme/fyzsxnb-neve-child/assets/css/cars-from-china.css` | Scoped desk styles (`cfc-*` classes; media 1024/768/420). |
| `qa/cars-from-china-structural-checks.ps1` | 27 structural assertions (static code checks) — **all PASS**. |
| `qa/cars_from_china_seo_regression.py` | Read-only SEO regression against prod — **all PASS** (see below). |
| `preview/cars-from-china-model-preview.html` | **TEST ONLY** static preview of a Model page with mock data (noindex, not deployable content). |
| `docs/cars-from-china/CARS-FROM-CHINA-CASE-CONTRACT.md` | Evidence rules for cases / common problems. |
| `docs/cars-from-china/CARS-FROM-CHINA-CONTENT-CONTRACT.md` | Language, taxonomy and launch-gate rules for articles. |

---

## 3. Taxonomies

Registered on `init` by `fyzsxnb_cfc_register_taxonomies()`:

### `fyz_vehicle` (hierarchical: Brand › Model)
- `public=true`, `hierarchical=true`, `show_in_rest=true`, `show_admin_column=true`.
- `rewrite=false` — URLs are produced by our own rewrite rules so they stay
  hierarchical (`/cars-from-china/{brand}/{model}/`).
- `query_var=fyz_vehicle`.

### `fyz_research_type` (non-hierarchical, article-type labels)
Slugs and bilingual labels (`fyzsxnb_cfc_research_types()`):

| slug | EN | RU |
|---|---|---|
| `overview` | Overview | Обзор |
| `owner-cases` | Owner Cases | Опыт владельцев |
| `common-problems` | Common Problems | Типовые проблемы |
| `parts-compatibility` | Parts & Compatibility | Запчасти и совместимость |
| `market-version` | China vs Export Version | Китайская vs экспортная версия |
| `repair-guide` | Repair Guides | Руководства по ремонту |
| `case-study` | Case Studies | Кейсы |

These are **article-type labels, not fault types**. Specific fault types
(e.g. "DSG judder") must wait for real cases — see the Case Contract.

---

## 4. V1 initial vehicle matrix

`fyzsxnb_cfc_initial_matrix()` — structural coverage only; **no pages are
created for empty slots** (matrix rows render pending models as text).

```php
volkswagen: tayron, tharu, golf, t-roc
audi:       q3, a3
toyota:     corolla
hyundai:    elantra
honda:      vezel
bmw:        x1
```

Terms are seeded once by `fyzsxnb_cfc_seed_terms()` (option
`fyzsxnb_cfc_terms_seeded`, idempotent, re-parents stray children). Brands are
parent terms; models are children. Seeding is triggered by `init` priority 11.

> Note: seeding only happens when the theme is deployed and activated on the
> target site. In the current environment this is a deploy-time step (see
> §9).

---

## 5. URLs & rewrites

`fyzsxnb_cfc_rewrites()` registers five rules (RU prefix first so the more
specific rules win):

| Pattern | Query |
|---|---|
| `^ru/cars-from-china/([^/]+)/([^/]+)/?$` | `fyz_vehicle=$matches[2]&fyz_cfc_parent=$matches[1]&fyz_cfc_lang=ru` |
| `^ru/cars-from-china/([^/]+)/?$` | `fyz_vehicle=$matches[1]&fyz_cfc_lang=ru` |
| `^ru/cars-from-china/?$` | `fyz_cfc_ru_hub=1` |
| `^cars-from-china/([^/]+)/([^/]+)/?$` | `fyz_vehicle=$matches[2]&fyz_cfc_parent=$matches[1]` |
| `^cars-from-china/([^/]+)/?$` | `fyz_vehicle=$matches[1]` |

- **EN hub** = real page (id 507, slug `cars-from-china`, status **draft**).
- **RU hub** = rewrite + `template_include` filter
  (`fyzsxnb_cfc_ru_hub_template()` → the hub template). No second page object
  needed, because WP page slugs are globally unique.
- Custom query vars: `fyz_cfc_parent`, `fyz_cfc_lang`, `fyz_cfc_ru_hub`.

### Parent validation (404 protection)

`fyzsxnb_cfc_validate_parent()` on `parse_query`: when `fyz_cfc_parent` is set,
the queried model must be a direct child of that brand, otherwise the request
is 404. Prevents fake URLs like `/cars-from-china/audi/tayron/`.

### Language

`fyzsxnb_cfc_is_ru()`: true when `fyz_cfc_lang=ru` **or** the request path is
under `/ru/`. `fyzsxnb_cfc_term_link($term, $ru)` builds canonical
hierarchical URLs with the `/ru` prefix.

---

## 6. Queries (evidence-first, language-aware)

`fyzsxnb_cfc_posts($term, $research_type, $ru, $limit)`:
- `post_type=post`, `post_status=publish`, ordered by date DESC.
- Optional `fyz_vehicle` term and `fyz_research_type` slug (`relation=AND`).
- **Language contract**: RU view → `category__in=[54]` (Russian Library only);
  EN view → `category__not_in=[54]` (excludes Russian Library). Constant
  `FYZSXNB_CFC_CATEGORY_RU = 54`.

Helpers:
- `fyzsxnb_cfc_has_published($term)` — cheap 1-post probe; used so
  unpublished models render as text, never as dead links.
- `fyzsxnb_cfc_post_card($post)` — list card with category/research-type meta.
- `fyzsxnb_cfc_section($type, $term, $ru, $limit)` — renders a research-type
  section, **returns `''` when empty** (empty-section suppression).
- `fyzsxnb_cfc_model_matrix($ru)` — brand › models matrix; only published
  model archives become links.

---

## 7. Page renderers

| Renderer | Used by | Content |
|---|---|---|
| `fyzsxnb_cfc_render_hub()` | hub template (EN page / RU rewrite) | hero (h1), model matrix, latest research (language-filtered), static research-area labels, "how we research", contact CTA (no commerce). |
| `fyzsxnb_cfc_render_brand($brand)` | `taxonomy-fyz_vehicle.php` when `parent=0` | hero h1, models covered (links only if published), latest brand research. |
| `fyzsxnb_cfc_render_model($model)` | `taxonomy-fyz_vehicle.php` when child | crumbs, hero h1, latest research, then research-type sections in reading order (`owner-cases → common-problems → parts-compatibility → market-version → repair-guide → case-study`), empty sections suppressed. |

`fyzsxnb_cfc_is_active_view()` gates CSS enqueue: true on `is_tax('fyz_vehicle')`,
on the hub page template, or any path containing `/cars-from-china`.

---

## 8. Production checks already done (read-only, against live site)

`qa/cars_from_china_seo_regression.py` (run via the secure publisher wrapper):

- `https://fyzsxnb.com/cars-from-china/` → **404** (page is draft — not public).
- `https://fyzsxnb.com/ru/cars-from-china/` → **404** (rewrite not deployed).
- `sitemap.xml` → 200, does **not** contain `cars-from-china`.
- REST: page id 507, slug `cars-from-china`, **status=draft**.
- Existing key pages (BYD cluster + `/ru/`): HTTP 200, `lang=ru-RU`,
  canonical self-referencing, single `<h1>`, robots meta intact.

**No production changes were made by the scaffold.** The draft hub page
(507) was created via REST with status `draft` and no template assigned.

---

## 9. Deploy notes (when the theme ships)

1. Deploy theme files (inc, templates, css, functions.php) to prod theme.
2. **Assign template** `page-templates/cars-from-china-hub.php` to page 507
   via REST — the template param is rejected before deployment (400
   `rest_invalid_param`), which is why the draft was created without it.
3. Flush rewrite rules (theme activation or `flush_rewrite_rules`); seeding of
   terms happens on `init` automatically.
4. Re-run `qa/cars-from-china-structural-checks.ps1` + SEO regression against
   prod after deployment.
5. Until real content exists, keep hub/model views **draft + noindex** and
   keep them out of nav/sitemap (launch gate — see Content Contract §5).

---

## 10. Out of scope (by design)

- Content research and article writing (ChatGPT side; DeepSeek publishes via
  the bounded secure publisher).
- SEO pages for empty models (explicitly forbidden — evidence-first).
- Commerce, price lists, dealer data.
