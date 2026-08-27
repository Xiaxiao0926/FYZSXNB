# FYZSXNB 0.4.5-A — Language Contract V2 Data Layer Specification

**Document ID:** `FYZ-DOC-20260820-DATA-045A`  
**Stage:** `0.4.5-A`  
**Author:** Google Gemini Flash 3.7  
**Scope:** Data Layer Implementation for `en`, `ru`, and `zh` Language Metadata  

---

## 1. 架构目标与范围限定 (Objectives & Boundaries)

本阶段为**数据层先行实施阶段（Local Data Layer Only）**：
1. **仅在数据层**引入对中文（`zh`）元数据的合法接收、清洗与存储支持；
2. **SEO 层（MU-Plugin）与主题层完全保持原状冻结**；
3. **生产环境保持完全只读**，无生产写入、无 FTP 部署。

---

## 2. 代码实现清单 (Code Changes)

### 2.1 Feed 插件 (`plugin/fyzsxnb-home-dynamic-feeds/fyzsxnb-home-dynamic-feeds.php`)
- **版本递增**：`v1.2.4` $\to$ `v1.2.5`。
- **Sanitize 回调扩展** (`fyzsxnb_feed_sanitize_language`)：
  * 支持 `zh`, `zh-cn`, `zh-hans`, `zh_cn`, `zh_hans`，统一规范化清洗为 `'zh'`；
  * 保持 `en`, `en-us`, `en-gb` $\to$ `'en'`，`ru`, `ru-ru` $\to$ `'ru'`。
- **Feed Locale 解析** (`fyzsxnb_home_post_locale`)：
  * 将 `_fyz_content_language = 'zh'` 解析映射为 `'zh-CN'`；
  * 保持 EN 首页只拉取 `'en-US'`，RU 首页只拉取 `'ru-RU'`，中文文章绝对隔离、零泄漏。
- **后台 Meta Box 渲染** (`fyzsxnb_pubmeta_render_meta_box`)：
  * 新增 `Chinese (zh)` 单选选项；
  * 增加 `zh` 文章被误分配至 Russian Library（Category 54）时的黄色高亮提示。
- **发布门禁** (`fyzsxnb_pubmeta_missing_fields`)：
  * 白名单扩充为 `['en', 'ru', 'zh']`，`zh` 文章在发布时不再被误降级为 `pending`。

### 2.2 发布脚本 (`work/site-ops/publish_single_article.py`)
- `--content-language` 参数 choices 扩充为 `["en", "ru", "zh"]`；
- 保持向后兼容性与发布门禁提示。

---

## 3. 首页 Feed 零泄漏证明 (Zero Leakage Invariant)

```text
[EN 首页 Feed (Page 11)]
  Target Feed Locale: 'en-US'
  Post with zh: home_post_locale() = 'zh-CN'
  'en-US' !== 'zh-CN' => EXCLUDED (100% 隔离)

[RU 首页 Feed (Page 400)]
  Target Feed Locale: 'ru-RU'
  Post with zh: home_post_locale() = 'zh-CN'
  'ru-RU' !== 'zh-CN' => EXCLUDED (100% 隔离)
```
