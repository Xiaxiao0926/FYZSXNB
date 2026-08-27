# FYZSXNB 0.4.5-B — Language Contract V2 Offline Integration QA Specification

**Document ID:** `FYZ-DOC-20260820-INTEGRATION-QA-045B`  
**Stage:** `0.4.5-B`  
**Author:** Google Gemini Flash 3.7  
**Scope:** Offline Integration Test Architecture & Verification Assertions  

---

## 1. 验证目标与数据链路 (Objective & Data Pipeline)

本规范旨在离线环境中对从编辑器输入到首页展示的完整多语言数据链路进行闭环端到端验证：

```text
Editor Input / CLI (--content-language en|ru|zh)
                │
                ▼
Sanitize & Post Meta (_fyz_content_language)
                │
                ▼
Feed Post Locale Resolver (fyzsxnb_home_post_locale -> en-US | ru-RU | zh-CN)
                │
                ▼
Homepage Dynamic Feeds Query (fyzsxnb_home_get_feed)
                │
                ├─► EN Homepage Feed (locale: 'en-US')  ──► 仅接收 en 文章 (0 泄漏)
                ├─► RU Homepage Feed (locale: 'ru-RU')  ──► 仅接收 ru 文章 (0 泄漏)
                └─► ZH Homepage Feed (locale: 'zh-CN')  ──► 仅接收 zh 文章
```

---

## 2. 测试组与断言矩阵 (Test Groups Matrix)

| 测试组 | 验证主题 | 用例数 | 关键断言与标准 |
|:---|:---|:---:|:---|
| **Group A** | Publishing Metadata Flow | 4 | `publish_single_article.py` 接收 `en`, `ru`, `zh`；拒绝非法 `de`。 |
| **Group B** | Feed Resolver Integration | 5 | `fyzsxnb_home_post_locale` 将 `zh` 规范映射为 `zh-CN`；缺失语言输出空；Category 54 兜底输出 `ru-RU`（`source: legacy`）。 |
| **Group C** | Homepage Isolation | 9 | ZH 文章在 EN/RU 首页 Feed 中完全不可见（0 泄漏）；RU 文章在 EN/ZH 首页完全不可见；EN 文章在 RU/ZH 首页完全不可见。 |
| **Group D** | Conflict Handling | 3 | ZH/EN 带 Cat54 或 RU 缺 Cat54 均触发黄色警告；坚守 `Language Metadata > Category` 不变式。 |
| **Group E** | Regression Snapshot | 4 | 96 篇真实生产快照中，58 EN $\to$ `en-US`，25 RU $\to$ `ru-RU`，13 Unknown $\to$ 空隔离；语义差异为 **0**。 |
| **Group F** | Future SEO Boundary Check | 6 | MU-Plugin、Canonical、Hreflang、Schema、OG 保持 100% 原状，`SEO_TOUCH_COUNT = 0`。 |

---

## 3. 测试夹具目录结构 (Fixtures Directory)

```text
qa/fixtures/language-v2/
  ├── fixture_en.json         (Valid EN mock posts)
  ├── fixture_ru.json         (Valid RU mock posts)
  ├── fixture_zh.json         (Valid ZH mock posts)
  └── fixture_conflicts.json  (Structural conflict & legacy fallback mock posts)
```
