# Cars from China Production Deployment Report

Date: 2026-08-18  
Package: `fyzsxnb-cfc-matrix-01`  
Theme: `fyzsxnb-neve-child`  
Final result: **PASS**

## Deployment

- Six manifest-scoped files were uploaded through the encrypted FTP wrapper.
- Every remote SHA-256 matched `DEPLOYMENT_MANIFEST.json` after upload.
- No WordPress core, parent-theme, upload, MU-plugin, permalink setting,
  taxonomy content, post content or database table was overwritten.
- Page 507 remains `draft` and uses
  `page-templates/cars-from-china-hub.php`.
- The user completed the required WordPress permalink save to flush rewrites.
- LiteSpeed and object caches were purged by a version-guarded, one-time CFC
  cache hook after the rewrite flush.

## Production Fixes Applied During Acceptance

1. Normalized an encrypted FTP host value so both `ftp.example.com` and
   `ftp://example.com` are handled without duplicating the scheme.
2. Added idempotent seeding for the seven `fyz_research_type` terms. The
   original package defined the terms but did not insert them.
3. Corrected the deploy-time expectation from 12 models to 10. The
   authoritative V1 matrix contains 6 brands and exactly 10 model slugs; no
   unsupported models were invented.
4. Added a one-time CFC-version cache purge so pre-flush LiteSpeed 404 entries
   cannot survive the rewrite update.
5. Corrected the verifier so the English homepage expects `en-US` while the
   Russian homepage and sampled Russian articles expect `ru-RU`.

## Term Seeding

- Brands: 6
- Models: 10
- Research types: 7
- Orphan models: 0

## Public and SEO Verification

- CFC CSS: HTTP 200; SHA-256 matches the manifest.
- English hub: HTTP 404, expected because page 507 remains draft.
- Russian hub: HTTP 200.
- Volkswagen brand route: HTTP 200.
- Volkswagen Tayron model route: HTTP 200.
- Sitemap: HTTP 200; `cars-from-china` is absent while the English hub is
  draft.
- Existing English homepage: HTTP 200, `lang=en-US`, self canonical, one H1.
- Existing Russian homepage: HTTP 200, `lang=ru-RU`, self canonical, one H1.
- Three sampled Russian BYD/openpilot articles: HTTP 200, `lang=ru-RU`, self
  canonical, one H1.
- No sampled page contained a PHP fatal or parse-error marker.

## QA

- PHP 8.5.9 lint: pass.
- CFC structural checks: 27/27 pass.
- Post-deploy verifier: exit code 0, `all_expected_ok=true`.

## Rollback

Pre-deploy snapshots are stored under:

`work/deployments/fyzsxnb-cfc-matrix-01/snapshots/predeploy-20260818-valid/`

Additional snapshots were captured before the research-type seed fix and the
cache-purge hook. Rollback was not used.

