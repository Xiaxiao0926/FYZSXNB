# Cars from China — Content Contract (language, taxonomy, launch gate)

> Binding rules for every article in the Cars from China desk. Applies to both
> languages. The launch gate (§5) decides when the desk becomes publicly
> visible.

## 1. Language contract (matches existing site rules)

- **RU articles:** must be filed under **category 54 (Russian Library,
  `russian-library`)**. The mu-plugin detector
  (`fyzsxnb-p0-seo-patch.php`) then sets `lang=ru-RU`, `og:locale=ru_RU` and
  schema `inLanguage` automatically.
- **EN articles:** must **never** carry category 54.
- CFC queries enforce this too: RU views query `category__in=[54]`, EN views
  `category__not_in=[54]`. An article in the wrong language bucket will
  simply not appear in CFC lists.

## 2. Taxonomy rules

Every CFC article must have:

- **At least one `fyz_vehicle` term** — the model term when the article is
  model-specific (e.g. `tayron`); the brand term only for brand-level pieces.
  Model-level terms are children of their brand (hierarchical taxonomy).
- **At least one `fyz_research_type` term** from the fixed set:
  `overview`, `owner-cases`, `common-problems`, `parts-compatibility`,
  `market-version`, `repair-guide`, `case-study`.
- Category: RU → 54 (+ optionally 50 china-tech-products / 56 Автомобили);
  EN → 50 and/or 56, never 54.

Existing CATEGORY_PRESETS in `publish_single_article.py` cover
`russian-library`, `china-tech-products`, `ru-auto` — reuse them; do not
invent new categories.

## 3. URL contract

- EN: `/cars-from-china/{brand}/{model}/` (brand: `/cars-from-china/{brand}/`).
- RU: `/ru/cars-from-china/{brand}/{model}/`.
- These URLs come from the theme rewrites; article permalinks stay normal.
  Internal links between CFC pages should use the hierarchical URLs
  (see `fyzsxnb_cfc_term_link()`).

## 4. Article quality rules

- **No SEO filler.** An article exists because it has real, citable content
  (see Case Contract). No article is created "to fill a slot".
- **No fabricated numbers.** Counts, prices, ratings only from cited sources.
- **Single H1** per page; titles not duplicated across the desk.
- **Internal linking** to other CFC pages uses exact anchor text that exists
  in the target (established cluster pattern).
- Images are optional; when used, must be licensed/own and relevant.

## 5. Launch gate (hub/nav/index visibility)

The desk becomes publicly linked/visible **only when**:

- **≥ 3 models** have real content, **each with ≥ 2 published articles**; and
- **≥ 3 EN articles and ≥ 3 RU articles** are live (language contract held),
  with at least the hub linking to those model archives.

Until then:

- Hub page stays **draft + noindex** (currently page id 507, draft).
- Model/brand archives that exist only as terms render as **pending text**
  (no links) in the matrix — already enforced by
  `fyzsxnb_cfc_has_published()`.
- No CFC links in main nav, footer, or sitemap.

## 6. Publishing workflow (roles)

- **ChatGPT** plans and writes articles (research side).
- **DeepSeek** publishes and verifies via the secure WordPress REST publisher
  (`site-ops/run_wp_publisher_secure.ps1`); never writes plaintext
  credentials; uses `status=future` or `publish` only per the article task.
- After publishing: assign `fyz_vehicle` + `fyz_research_type` terms to the
  post via REST, then re-verify the CFC page renders the article in the right
  language bucket.

## 7. Verification checklist (every publish)

- [ ] category 54 present iff RU; absent for EN
- [ ] `fyz_vehicle` + `fyz_research_type` terms set
- [ ] single `<h1>`, canonical self, robots meta sane
- [ ] citations `[N]` resolved in `REF_URLS`
- [ ] no empty sections on the target model page (renderer suppresses them;
      verify nothing phantom appears)
- [ ] launch gate counts still respected (hub not publicized early)

## 8. Research-to-article gate (set by the editorial lead 2026-08-18)

- **No article is drafted before the vehicle's Case Matrix + Issue Matrix
  exist** (see Case Contract §9–§10). The matrices decide which issues pass
  CASE → REPEATED ISSUE → PATTERN; only PASSED issues may be written up.
- **One research, two outputs, no double translation:** a single confirmed
  research finding may produce an EN article (for global importers of
  CN-market Tayron, emphasizing long-term Chinese owner experience) and an RU
  article (what RU owners/importers may face), written from the **same
  evidence base** with per-language framing — never a translation of each
  other.
- A parts-level finding may additionally produce an EN "How to identify the
  correct part" + RU "Как подобрать … и не ошибиться с ревизией" pair.
- Articles land into the desk sections (Owner Cases / Common Problems /
  Parts & Compatibility / Market Versions / Repair Guides / Case Studies)
  only when real evidence exists for that section — slots are never filled
  for their own sake.
- Role boundary: ChatGPT leads research and article planning/writing;
  DeepSeek only publishes and verifies through the secure publisher. No
  research or article writing is expected from DeepSeek in this phase.
