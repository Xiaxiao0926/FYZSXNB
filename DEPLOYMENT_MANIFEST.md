# FYZSXNB V2 Deployment Manifest

## Changed files

- `theme/fyzsxnb-neve-child/style.css` - version `0.3.0`
- `theme/fyzsxnb-neve-child/functions.php` - enqueue the presentation layer and article TOC script; add the `fyz-wire-desk` body class
- `theme/fyzsxnb-neve-child/assets/css/research-wire.css` - scoped Wire Desk presentation layer
- `theme/fyzsxnb-neve-child/assets/js/research-wire.js` - optional TOC from existing H2/H3 only

## Deliberately unchanged

- `plugin/fyzsxnb-home-dynamic-feeds/fyzsxnb-home-dynamic-feeds.php`
- `mu-plugins/fyzsxnb-p0-seo-patch.php`
- WordPress page content, slugs, taxonomies, metadata, canonical/hreflang, sitemap and feed query rules

## Rollback

The exact pre-change package is Git commit `58f8167` in the local branch
`feat/fyzsxnb-v2-research-wire`. A deployment rollback consists of restoring
the four changed files from that commit, purging LiteSpeed/object/CDN caches
through the existing deployment workflow, and repeating the public H1,
language, canonical, feed and mobile checks.

## Production status

Not deployed. The package is ready for a production upload after explicit
deployment approval and a confirmed Hostinger/FTP upload path.

