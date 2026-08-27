# FYZSXNB SEO Language Contract V2 — Specifications & Invariants

**Document ID:** `FYZ-DOC-20260820-SEO-V2`  
**Stage:** `0.4.3`  
**Author:** Google Gemini Flash 3.7  
**Status:** `DESIGN_PROPOSAL`  
**Scope:** SEO Governance across Canonical, Hreflang, HTML Attributes, Schema, and Sitemaps  

---

## 1. 核心设计原则 (Core Principles)

在引入 `zh`（中文）及未来更多语种时，SEO 体系必须坚守**零歧义、自闭环、防御性隔离**原则：
1. **Self-Canonical 绝对铁律**：每篇已发布文章（无论语言）必须有且仅有自指向的 Canonical URL，杜绝跨语言规范化合并。
2. **Hreflang 仅限经过审核的等价群组**：严禁仅因主题相关或同一作者而生成跨语言 hreflang，严格受 0.4.0 Translation Pair Contract 管辖。
3. **首页与文章页分层治理**：首页与单文章页采用独立且互不干扰的 SEO 标签输出逻辑。

---

## 2. 页面元标签与 HTML 属性映射矩阵 (Metadata Mapping Matrix)

当 `_fyz_content_language` 扩展为 `['en', 'ru', 'zh']` 时，MU-Plugin 及 SEO 过滤器的标准映射规范如下：

| `_fyz_content_language` | `<html> lang` 属性 | `og:locale` | Schema `inLanguage` | AIOSEO / Twitter / FB 描述语种 |
|:---:|:---:|:---:|:---:|:---:|
| **`en`** | `lang="en-US"` | `en_US` | `"en-US"` | 英文正文摘要 |
| **`ru`** | `lang="ru-RU"` | `ru_RU` | `"ru-RU"` | 俄文正文摘要 |
| **`zh`** | `lang="zh-CN"` | `zh_CN` | `"zh-CN"` | 中文正文摘要 |
| *(未定义/异常)* | `lang="en-US"` (默认回退) | `en_US` | `"en-US"` | 英文默认回退 |

---

## 3. Hreflang 策略 V2 (Hreflang Contract V2)

### 3.1 首页 Hreflang 治理
- **现状（两元闭环）**：
  ```html
  <!-- 在 Page 11 (/) 与 Page 400 (/ru/) 输出 -->
  <link rel="alternate" hreflang="en" href="https://fyzsxnb.com/" />
  <link rel="alternate" hreflang="ru" href="https://fyzsxnb.com/ru/" />
  <link rel="alternate" hreflang="x-default" href="https://fyzsxnb.com/" />
  ```
- **V2 演进规则**：
  * **在独立中文首页（如 `/zh/`）未正式上线前**：绝对禁止修改或破坏现有的 Page 11 ↔ Page 400 首页配对。
  * **当未来建立独立 `/zh/` 首页后**：以安全补丁方式升级为三元互联（`en` + `ru` + `zh` + `x-default`）。

### 3.2 文章页 Hreflang 治理 (Article Hreflang)
- **严格遵循 0.4.0 Translation Pair Contract**：
  * 仅当文章拥有显式 `_fyz_translation_group` 且经人工审核语义等价时，才在组内成员间输出互联 hreflang。
  * **示例（若未来 Post 479 存在 EN 与 RU 翻译）**：
    ```html
    <link rel="alternate" hreflang="zh" href="https://fyzsxnb.com/nmpa-udi-2027.../" />
    <link rel="alternate" hreflang="en" href="https://fyzsxnb.com/nmpa-udi-2027-en.../" />
    <link rel="alternate" hreflang="ru" href="https://fyzsxnb.com/nmpa-udi-2027-ru.../" />
    ```
  * **当前现状（0 组有效文章译文对）**：全站 96 篇文章页面**一律不输出任何文章级 hreflang**，保持干净留白。

---

## 4. Sitemap 与索引策略 (Sitemap & Indexation)

1. **统一索引覆盖**：
   - 所有已发布的 `en`、`ru`、`zh` 文章均平等进入 XML Sitemap。
   - 不因为语言不同而将文章做 noindex 处理（只要 status=publish 且具备有效 meta）。
2. **隔离归档与垃圾防御**：
   - 属于 `C. LEGACY_ARCHIVE` 的 2 篇 2025 历史种子文章，保持现有状态，不强制做 301 重定向或删除。
