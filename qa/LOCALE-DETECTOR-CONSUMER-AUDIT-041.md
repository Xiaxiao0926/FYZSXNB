# FYZSXNB 0.4.1 — Locale Detector Consumer Audit

**Task ID:** `FYZ-20260820-LOCALE-DETECTOR-041`  
**Date:** 2026-08-20  
**Status:** `AUDIT_COMPLETE`  
**Scope:** Inventory of all consumers of language/locale detection across MU-plugin, Theme, and Plugins.

---

## 1. Consumer Matrix

| # | Consumer / Hook | File | Function | Context | Current Source | Public Output Effect | 0.4.1 Migration Action |
|---|:---|:---|:---|:---|:---|:---:|:---|
| 1 | `language_attributes` | `mu-plugins/fyzsxnb-p0-seo-patch.php` | `fyzsxnb_filter_language_attributes` | Single Post / Page / Archive | `fyzsxnb_is_russian_target()` | `<html lang="ru-RU">` | **MIGRATE**: Central Content Resolver (Single Post) + Request Fallback |
| 2 | `aioseo_facebook_tags` | `mu-plugins/fyzsxnb-p0-seo-patch.php` | `fyzsxnb_filter_facebook_tags` | Single Post / Page | `fyzsxnb_is_russian_target()` | `og:locale = "ru_RU"` | **MIGRATE**: Central Content Resolver (Single Post) + Request Fallback |
| 3 | `aioseo_schema_output` | `mu-plugins/fyzsxnb-p0-seo-patch.php` | `fyzsxnb_filter_schema_output` | Schema Graph (WebPage/Article) | `fyzsxnb_is_russian_target()` | `"inLanguage": "ru-RU"` | **MIGRATE**: Central Content Resolver (Single Post) + Request Fallback |
| 4 | `aioseo_description` | `mu-plugins/fyzsxnb-p0-seo-patch.php` | `fyzsxnb_filter_aioseo_description` | Page 400 (`/ru/`) | Hardcoded ID 400 | Custom RU Description | **KEEP**: Page 400 special-case (no change) |
| 5 | Homepage Hreflang (`wp_head`) | `mu-plugins/fyzsxnb-p0-seo-patch.php` | `fyzsxnb_render_home_hreflang` | Front Page / Page 400 | Hardcoded ID 11 & 400 | `<link hreflang="en/ru">` | **KEEP**: Stable homepage pair (no change) |
| 6 | Theme Russian View Detector | `theme/fyzsxnb-neve-child/functions.php` | `fyzsxnb_is_russian_view` | Full Request View | Route + Cat 54 + Legacy IDs | Hero switcher / Fonts / Archive | **FROZEN**: Theme 0.3.11 frozen |
| 7 | Homepage Controller Locale | `theme/fyzsxnb-neve-child/inc/home.php` | `fyzsxnb_home_locale` | Homepage Template | `fyzsxnb_is_russian_view()` | Hero Switcher config | **FROZEN**: Theme 0.3.11 frozen |
| 8 | Feed Dynamic Data Layer | `plugin/fyzsxnb-home-dynamic-feeds/` | `fyzsxnb_get_home_feed_posts` | Dynamic Feed | `_fyz_content_language` | Home Signals/Guides feed | **FROZEN**: Feed 1.2.4 frozen |

---

## 2. 核心架构发现：两种 Locale 的清晰划分

本审计明确区分两类判定维度，杜绝单一大一统函数引发的请求路由污染：

1. **Content Locale (文章固有内容语言)**：
   - 关注点：*“Post 448 这篇文章本身是什么语言？”*
   - 适用范围：Single Post 渲染、SEO Meta (`lang`, `og:locale`, `inLanguage`)、文章级元数据。
   - 事实源优先级：`_fyz_content_language` (Primary) $\rightarrow$ `Cat 54 结构校验` $\rightarrow$ `fyzsxnb_get_russian_post_ids()` (Fallback) $\rightarrow$ Default (`en`).
2. **Request / View Locale (视图与路由请求语言)**：
   - 关注点：*“当前页面（如 `/ru/` 首页、Russian Library 归档、CFC 车型矩阵、404）按何种语言呈现？”*
   - 适用范围：非文章页面、分类归档、URL 路径路由。
   - 处理方式：保持现有安全 Request 判定不变，不将 URL 路由逻辑强塞进文章元数据。

---

## 3. 审计结论

- **直接可迁移 Consumer**：Consumer 1、2、3（HTML Lang, OG Locale, Schema inLanguage）完全收敛于 `fyzsxnb_is_russian_target()`。
- **重构范围**：仅重构 `fyzsxnb-p0-seo-patch.php` 中的 `fyzsxnb_is_russian_target()` 及其底层调用的 `fyzsxnb_resolve_content_locale()`，保持向后兼容 fallback，零改动 Theme 与 Feed 插件。
