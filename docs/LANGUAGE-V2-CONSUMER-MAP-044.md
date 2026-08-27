# FYZSXNB 0.4.4 — Language V2 Consumer Implementation Map

**Document ID:** `FYZ-DOC-20260820-CONSUMER-MAP-044`  
**Stage:** `0.4.4`  
**Author:** Google Gemini Flash 3.7  
**Scope:** Full-Workspace Audit of Language Metadata & Resolver Consumers  

---

## 1. 消费端全景审计表 (Full Consumer Audit Table)

| # | Consumer / Hook | File | Current Behavior (0.3.6.1 / 0.4.1) | Needs `zh` Support | Inherent Risk | Recommended Action |
|---|:---|:---|:---|:---:|:---:|:---|
| **1** | `_fyz_content_language` sanitize & register | `plugin/fyzsxnb-home-dynamic-feeds/fyzsxnb-home-dynamic-feeds.php` (lines 84–93) | Sanitizes `en`, `en-us`, `en-gb` $\to$ `en`; `ru`, `ru-ru` $\to$ `ru`; other values $\to$ `''` | **YES** | Low | Add `zh`, `zh-cn`, `zh-hans` $\to$ `zh` to sanitize whitelist. |
| **2** | Editor Meta Box (`fyzsxnb_pubmeta_render_meta_box`) | `plugin/fyzsxnb-home-dynamic-feeds/fyzsxnb-home-dynamic-feeds.php` (lines 511–542) | Radio buttons for English (`en`) & Russian (`ru`). Missing either field demotes to `pending`. | **YES** | Low | Add Chinese (`zh`) radio option: `<label><input type="radio" value="zh"> Chinese (zh)</label>`. |
| **3** | Dynamic Feeds Locale Resolver (`fyzsxnb_home_post_locale`) | `plugin/fyzsxnb-home-dynamic-feeds/fyzsxnb-home-dynamic-feeds.php` (lines 103–116) | Resolves `en` $\to$ `en-US`, `ru` / Cat 54 $\to$ `ru-RU`, empty $\to$ `''`. Feeds query exact locale. | **YES** | Medium | Map `zh` $\to$ `zh-CN`. Feeds on EN/RU pages continue querying `en-US` / `ru-RU`, safely isolating `zh`. |
| **4** | MU-Plugin Content Locale Resolver (`fyzsxnb_resolve_content_locale`) | `mu-plugins/fyzsxnb-p0-seo-patch.php` (lines 57–134) | Evaluates `_fyz_content_language` for `ru` (with Cat54) & `en` (without Cat54). Legacy fallback $\to$ `ru`. Default $\to$ `en`. | **YES** | Medium | Upgrade to V2 Resolver: support `zh` (with `Cat54 = NO`). Safe fallback hierarchy for unmigrated posts. |
| **5** | HTML `language_attributes` Filter | `mu-plugins/fyzsxnb-p0-seo-patch.php` (lines 197–212) | If `fyzsxnb_is_russian_target()` is true $\to$ outputs `lang="ru-RU"`. Otherwise leaves Core default (`lang="en-US"`). | **YES** | Low | If resolved locale is `zh` $\to$ output `lang="zh-CN"`. If `ru` $\to$ `lang="ru-RU"`. Else $\to$ `lang="en-US"`. |
| **6** | OpenGraph `aioseo_facebook_tags` Filter | `mu-plugins/fyzsxnb-p0-seo-patch.php` (lines 217–238) | If Russian target $\to$ `$facebook_tags['og:locale'] = 'ru_RU'`. Else default (`en_US`). | **YES** | Low | If `zh` target $\to$ set `og:locale = 'zh_CN'`. If `ru` $\to$ `ru_RU`. Else default. |
| **7** | Schema `aioseo_schema_output` Filter | `mu-plugins/fyzsxnb-p0-seo-patch.php` (lines 245–347) | Traverses graph nodes and updates `inLanguage` to `'ru-RU'` for Russian targets. | **YES** | Low | If `zh` target $\to$ set `inLanguage = 'zh-CN'`. If `ru` $\to$ `'ru-RU'`. Else preserve/default. |
| **8** | Standard Publisher CLI (`publish_single_article.py`) | `work/site-ops/publish_single_article.py` (lines 309–312) | Argparse `--content-language` restricts `choices=["en", "ru"]`. | **YES** | Low | Expand `choices=["en", "ru", "zh"]`. |
| **9** | Feed Parity & QA Acceptance Test Suites | `work/fyzsxnb-ui-v2/qa/feed_0361_accept.py`, `qa/locale_detector_041_test.py` | Validates binary `en`/`ru` assertions and 0-leakage matrix. | **YES** | Low | Add Case ZH and multi-locale matrix assertions. |
| **10** | Theme Request Filter (`fyzsxnb_is_russian_view`) | `theme/fyzsxnb-neve-child/functions.php` (lines 53–75) | Inspects URL request path for `/ru/`, Russian library archive, and Russian single posts. | **NO (FROZEN)** | Zero | **FROZEN**. View/request routing layer remains untouched. |
| **11** | Homepage Hreflang Filter (`fyzsxnb_render_home_hreflang`) | `mu-plugins/fyzsxnb-p0-seo-patch.php` (lines 620–640) | Renders explicit Page 11 ↔ Page 400 bi-directional homepage hreflang tags. | **NO (FROZEN)** | Zero | **FROZEN**. Preserved until dedicated `/zh/` homepage is created. |
| **12** | Translation Pair REST Controller (`fyzsxnb-translation-pairs.php`) | `plugin/fyzsxnb-translation-pairs/fyzsxnb-translation-pairs.php` | Manages `_fyz_translation_group` meta. Validates pairs have distinct locales. | **YES (Future)** | Zero | Currently `LOCAL_PASS` isolated. Group logic is already locale-agnostic. |

---

## 2. 结论与改动边界总结

1. **允许改动层（3 处核心）**：
   - Feed Plugin（Meta 注册、Sanitize 白名单、后台 Meta Box 单选、Feed 语言过滤）；
   - MU-Plugin（Content Locale Resolver 升级三元、HTML lang / OG / Schema 映射）；
   - 发布脚本与 QA 测试套件。
2. **绝对冻结层（0 改动）**：
   - Theme 0.3.11（完全冻结）；
   - Canonical 策略（完全冻结）；
   - Homepage Hreflang 11 ↔ 400（完全冻结）；
   - Robots / Sitemap / URL / Permalinks（完全冻结）。
