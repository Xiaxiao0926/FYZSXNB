# FYZSXNB V2 - LUNA UI Implementation Result

## BASELINE

UI-0 baseline is recorded in `docs/FYZSXNB-UI-V2-ENGINEERING-AUDIT.md` and
`baseline/BASELINE.json`. Live pages were rechecked read-only before edits.

## BRANCH

`feat/fyzsxnb-v2-research-wire`

## COMMITS

`58f8167` baseline; implementation commit follows this report.

## FILES CHANGED

The child theme presentation layer, one scoped stylesheet, one small article
TOC script and the theme version. The dynamic feeds plugin and P0 MU plugin
were copied for rollback context but not modified.

## DESIGN SYSTEM

Added a near-monochrome paper/ink Wire Desk layer with restrained blue and red
signals, rule-based metadata, smaller display type and reduced card treatment.

## HEADER

Tightened the masthead, navigation spacing and language switch without changing
the existing EN/RU URLs.

## EN HOME

Latest signals now read as an editorial two-column list. Featured remains a
manual editorial area. Research Desks use a compact index treatment.

## RU HOME

The same presentation layer applies without changing the RU page content or
locale-pure feed behavior.

## ARCHIVES

Inherited Neve archive entries receive a structured research-index treatment;
mobile switches to stacked rows.

## ARTICLE

Added an optional lightweight TOC built only from real H2/H3 headings. Added
presentation hooks for existing source/evidence callouts without inventing
content.

## MOBILE

390px checks passed with no horizontal overflow. Signal and desk grids collapse
to one column.

## ACCESSIBILITY

Existing focus rules are preserved. The TOC uses a native `details`/`summary`
control, text-only links and no extra interaction dependency.

## SEO REGRESSION

No content, URL, taxonomy, canonical, hreflang, sitemap, AIOSEO or MU-plugin
logic was changed. One-H1 behavior is preserved in the local preview and the
live baseline.

## DYNAMIC FEEDS

The dynamic feed plugin is unchanged. The CSS styles its rendered signal and
guide markup without changing queries, locale filters, fallback behavior or
Featured selection.

## LANGUAGE

EN/RU page shells remain independent. The pre-existing Russian archive
`html lang` mismatch is documented as a separate infrastructure issue and was
not silently changed here.

## PHP

Theme functions, the P0 MU plugin and dynamic feed plugin all pass PHP lint on
portable PHP 8.5.9.

## VISUAL QA

See `qa/verification.json` and `qa/screenshots/`. Desktop 1440, homepage 390,
Russian homepage 390 and a real English article with injected V2 CSS/TOC were
checked.

## PRODUCTION DEPLOYED

No. The production site was deliberately left unchanged. The exact upload and
rollback manifest is in `DEPLOYMENT_MANIFEST.md`.

## KNOWN ISSUES

Live Russian archive document language is still `en-US` while its body locale
is RU. It requires a separate infrastructure/SEO task because it belongs to
the MU language layer, not this visual-only branch.

## ROLLBACK

Restore the four changed theme files from baseline commit `58f8167`, purge
caches, and rerun the public regression checks.

## FINAL RESULT

Implementation-ready, locally linted and visually verified Wire Desk package;
not yet uploaded to WordPress.
