# FYZSXNB SEO Tag Mapping Specification V2

**Document ID:** `FYZ-DOC-20260820-SEO-MAP-044`  
**Stage:** `0.4.4`  
**Author:** Google Gemini Flash 3.7  
**Scope:** Technical Tag Mapping for English, Russian, and Chinese Content  

---

## 1. 全语种 SEO 标签映射规范矩阵 (SEO Mapping Matrix)

| 语种契约 (`_fyz_content_language`) | HTML 根属性 (`language_attributes`) | OpenGraph 区域 (`og:locale`) | Schema.org 语种 (`inLanguage`) | Twitter Card / Meta Description |
|:---:|:---:|:---:|:---:|:---|
| **`en` (English)** | `lang="en-US"` | `en_US` | `"en-US"` | 英文正文原生 Excerpt / AIOSEO 描述 |
| **`ru` (Russian)** | `lang="ru-RU"` | `ru_RU` | `"ru-RU"` | 俄文正文原生 Excerpt / AIOSEO 描述 |
| **`zh` (Chinese)** | `lang="zh-CN"` | `zh_CN` | `"zh-CN"` | 中文正文原生 Excerpt / AIOSEO 描述 |
| *(未定义/异常回退)* | `lang="en-US"` | `en_US` | `"en-US"` | 英文默认兜底 |

---

## 2. 现有输出零回归保证 (Zero Regression Proof)

1. **现有 58 篇 EN 文章**：
   - 维持 `lang="en-US"`、`og:locale="en_US"`、Schema `inLanguage="en-US"` 输出，**前后逐位一致**。
2. **现有 25 篇 RU 文章**：
   - 维持 `lang="ru-RU"`、`og:locale="ru_RU"`、Schema `inLanguage="ru-RU"` 输出，**前后逐位一致**。
3. **Page 11 (EN 首页) & Page 400 (RU 首页)**：
   - Page 11 维持 `lang="en-US"`，Page 400 维持 `lang="ru-RU"` 及专用描述，**前后逐位一致**。
4. **新增 ZH 文章（如 Post 479、470）**：
   - 仅当其被确权写入 `_fyz_content_language = 'zh'` 后，其单页 HTML 才激活 `lang="zh-CN"` 与 `zh_CN`，对站内其他任何页面 0 影响。
