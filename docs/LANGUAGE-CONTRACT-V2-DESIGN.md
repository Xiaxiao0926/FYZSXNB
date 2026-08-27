# FYZSXNB Language Contract V2 — Architecture & Content Model Design

**Document ID:** `FYZ-DOC-20260820-LANG-V2`  
**Stage:** `0.4.3`  
**Author:** Google Gemini Flash 3.7  
**Status:** `DESIGN_PROPOSAL`  
**Scope:** Architecture Specification for Multi-Language Content Governance  

---

## 1. 背景与核心问题 (Background & Problem Statement)

### 1.1 现状与痛点
在 FYZSXNB 0.3.6.1 建立的元数据契约中：
- `_fyz_content_language` 字段仅支持两元枚举：`'en'` 与 `'ru'`。
- 0.4.2 审计证实，全站 96 篇已发布文章中，存量的 13 篇 unknown 文章并非全系“数据损坏或丢失”，而是包含明确的**中文原创深度分析文章**（如 Post 479 为 NMPA UDI 指南，Post 470 为村卫生室 POCT 临床评估，语言均为 `zh`）。
- 现行二元模型导致中文资产无法取得合法元数据身份，陷入被隔离在 unknown 的非健康状态；同时由于历史原因，部分涉及俄罗斯主题的中文文章（如 Post 444）容易被误判为俄语。

### 1.2 V2 架构使命
建立支持 `en`（英语）、`ru`（俄语）、`zh`（中文）乃至未来扩展语种（如 `es`, `ar` 等）的**正交、可验证、防泄漏的多语言内容资产模型**。

---

## 2. 语言模型演进分析 (Language Model Analysis)

### 2.1 候选方案对比

| 评估维度 | 方案 A: 严格扩展枚举 (`enum('en', 'ru', 'zh')`) | 方案 B: 规范化 BCP-47 标签 (`'en-US'`, `'ru-RU'`, `'zh-CN'`) | 方案 C: 自由字符串 (`text`) |
|:---|:---|:---|:---|
| **契约确定性** | **极高**（白名单强校验，无歧义） | 中等（易产生大小写及子标签冗余） | 极低（易出现脏数据与拼写错误） |
| **Feed 过滤性能** | **极快**（单值精确匹配） | 较快（需额外做前缀或归一化计算） | 差（模糊查询易漏查） |
| **向后兼容性** | **100% 兼容**（直接扩展 0.3.6.1 白名单） | 破坏性（既有 83 篇需全量迁移重写） | 不可控 |
| **后台交互体验** | 单选 Radio / Select 极为清爽 | 下拉列表冗长，易误选 | 文本输入无门禁 |
| **推荐结论** | **★★★★★ (推荐采纳方案 A)** | ★★★☆☆ (作为内部导出格式) | ☆☆☆☆☆ (坚决禁止) |

### 2.2 方案 A 的综合影响评估
1. **优点**：
   - 彻底解决中文原创内容的合法元数据合规问题。
   - 零破坏性：现有 58 篇 `en` 与 25 篇 `ru` 元数据 100% 保持原样，无需执行任何数据库迁移重写。
   - 门禁规则简单明确：仅需在发布器与后台 Meta box 白名单中将 `['en', 'ru']` 扩展为 `['en', 'ru', 'zh']`。
2. **潜在风险与防御措施**：
   - **Feed 泄漏风险**：若 Feed 查询写错，可能导致中文流入英文/俄文首页。
     * *防御契约*：首页 Feed 查询必须显式绑定对应 locale（`'en'` 或 `'ru'`），永不使用“非 RU 即 EN”的二元反向排除法。
   - **分类结构错配风险**：中文文章误打 Category 54（Russian Library）。
     * *防御契约*：`zh` 文章严格执行 `Category 54 = NO` 门禁。

---

## 3. 四维正交语言模型 (Four-Dimensional Orthogonal Language Model)

为彻底避免“俄罗斯主题的中文文章被当成俄语”或“俄语文章在中文路径访问”等逻辑混乱，V2 规范严格区分以下四个独立维度：

```text
+-------------------------------------------------------------------------------+
|                             FYZSXNB 内容与访问四元模型                          |
+-------------------------------------------------------------------------------+
| 1. Content Language (文章正文语言)                                             |
|    - 唯一事实源: post_meta `_fyz_content_language`                            |
|    - 枚举范围: 'en' | 'ru' | 'zh'                                             |
|    - 决定: 页面 html lang / OG locale / 文章 Schema inLanguage                |
+-------------------------------------------------------------------------------+
| 2. Request Locale (当前请求/页面路由环境)                                       |
|    - 唯一事实源: URL 路由 path (/ -> 'en', /ru/ -> 'ru', /zh/ -> 'zh')         |
|    - 决定: 导航菜单、页眉页脚模板、当前页面 Feed 候选池                          |
+-------------------------------------------------------------------------------+
| 3. Site Locale (WordPress 底层安装环境)                                        |
|    - 唯一事实源: get_locale() -> 'en_US'                                      |
|    - 决定: WP Core 底层文案、插件后台 UI 语言                                   |
+-------------------------------------------------------------------------------+
| 4. Translation Pair (跨语言等价译文群组)                                        |
|    - 唯一事实源: post_meta `_fyz_translation_group` (0.4.0 契约)                |
|    - 决定: 文章页间的 Hreflang 互联 (如 ZH 文章 479 ↔ EN 译文 ↔ RU 译文)        |
+-------------------------------------------------------------------------------+
```

### 3.1 核心不变式 (Invariants)
1. **文章内容语言独立于主题**：
   - 一篇讨论俄罗斯 EAEU 注册但用中文撰写的文章，其 `content_language` 必须且只能是 `zh`。
   - 严禁因为文章包含“俄罗斯”、“EAEU”、“莫斯科”等关键词而将 `content_language` 判定为 `ru`。
2. **文章内容语言独立于请求路由**：
   - 文章 URL 为 `https://fyzsxnb.com/nmpa-udi-2027...`，虽然位于根路径，但只要 `content_language = 'zh'`，其属性就是 `zh`。

---

## 4. 首页 Feed 数据模型影响与隔离规则 V2 (Homepage Feed Rules V2)

### 4.1 首页 Feed 绝对隔离契约
动态 Feed 插件（`fyzsxnb-home-dynamic-feeds`）在 V2 下必须严格遵循**正向白名单匹配**，杜绝任何 cross-locale 污染：

```text
[EN 首页 (Page 11 / URL: /)]
  └── Signals Feed:  SELECT posts WHERE _fyz_content_language = 'en' AND _fyz_content_kind = 'signal'
  └── Guides Feed:   SELECT posts WHERE _fyz_content_language = 'en' AND _fyz_content_kind = 'guide'

[RU 首页 (Page 400 / URL: /ru/)]
  └── Signals Feed:  SELECT posts WHERE _fyz_content_language = 'ru' AND _fyz_content_kind = 'signal'
  └── Guides Feed:   SELECT posts WHERE _fyz_content_language = 'ru' AND _fyz_content_kind = 'guide'

[未来 ZH 首页 (Page TBD / URL: /zh/)]
  └── Signals Feed:  SELECT posts WHERE _fyz_content_language = 'zh' AND _fyz_content_kind = 'signal'
  └── Guides Feed:   SELECT posts WHERE _fyz_content_language = 'zh' AND _fyz_content_kind = 'guide'
```

### 4.2 隔离铁律
1. **禁止泄漏**：`zh` 内容绝对不可出现在 EN 首页或 RU 首页的任何 Signals / Guides / Featured 模块中。
2. **缺省保护**：`_fyz_content_language` 为空或未通过门禁的文章，一律不可进入任何公开首页 Feed。
