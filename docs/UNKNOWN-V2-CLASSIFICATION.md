# FYZSXNB Unknown V2 Classification & Governance Framework

**Document ID:** `FYZ-DOC-20260820-UNK-V2`  
**Stage:** `0.4.3`  
**Author:** Google Gemini Flash 3.7  
**Status:** `DESIGN_PROPOSAL`  
**Scope:** Re-classification and Target Lifecycle for 13 Unknown Posts  

---

## 1. Unknown V2 五级分类体系 (Taxonomy)

为了彻底告别将所有未打标内容一概归为“未知/损坏”的粗放处理，V2 规范将存量与增量未定义元数据对象严格拆解为 5 种业务属性：

```text
+---------------------------------------------------------------------------------------------------+
|                                  UNKNOWN V2 分类体系                                                |
+-------------------+---------------------------------------------------------+---------------------+
| 类别代号           | 业务定义                                                | 目标治理策略         |
+-------------------+---------------------------------------------------------+---------------------+
| A. MISSING_META   | 属于现行 EN/RU 生产流水线，但因技术中断丢失元数据       | 查找发布记录直接补齐 |
| B. ZH_CONTENT     | 明确为中文原创内容资产，等待 Language Contract V2 开放  | 启用 zh 后批量确权   |
| C. LEGACY_ARCHIVE | 早期历史种子/归档文章（如 2025 电商报告），不再进 Feed   | 永久保留隔离或软下线 |
| D. MANUAL_REVIEW  | 跨语言主题/结构歧义文（如中文写俄罗斯器械），需人工定性 | 人工编辑决策         |
| E. CONFLICT       | 存在结构性矛盾（如元数据语言与 Category 冲突）          | 阻断写入并报错       |
+-------------------+---------------------------------------------------------+---------------------+
```

---

## 2. 13 篇存量 Unknown 重新定级台账 (Re-classification Ledger)

> **声明**：本台账为纯架构设计与治理方案，**严禁对生产数据库或文章执行任何物理写入**。

| ID | Slug | 旧状态 (0.4.2) | V2 新分类 | 事实与分类理由 | 未来进入 ZH 资格 |
|---:|:---|:---:|:---:|:---|:---:|
| **479** | `nmpa-udi-2027-class2-devices-ivd-implementation-guide` | FULLY_CONFIRMED (zh) | **B. ZH_CONTENT** | 原始 SEO 声明明确为 `language: zh`, `article_type: guide`。契约升级后可直接确权。 | **YES** (Level A) |
| **470** | `crp-saa-poct-antibiotic-stewardship-village-clinics` | FULLY_CONFIRMED (zh) | **B. ZH_CONTENT** | 原始 SEO 声明明确为 `language: zh`, `article_type: signal`。契约升级后可直接确权。 | **YES** (Level A) |
| **444** | `russia-eaeu-ivd-registration-transition-2026-2028` | CONFLICT | **D. MANUAL_REVIEW** | 标题含俄罗斯与 EAEU，但正文为中文且分类在 [52]，无 Cat 54。需编辑确认是保持 ZH 还是翻译为 RU。 | **待定 (需人工决策)** |
| **435** | `gacc-order-281-special-goods-2026` | LANG_ONLY_CONFIRMED | **B. ZH_CONTENT** | Cluster Patch 记录为 `zh`，分类在 [52]（特殊物品进出境合规）。需补齐 kind 确认。 | **YES** (需补 kind) |
| **424** | `national-anti-fraud-center-ai-content-identification-guide` | NO_EVIDENCE | **B. ZH_CONTENT** | 契约前国家反诈 AI 鉴定深度解析，正文为中文，分类在 [50]（China Tech）。 | **YES** (需编辑确认) |
| **411** | `china-pharma-exports-2026-formulations-glp1-api` | LANG_ONLY_CONFIRMED | **B. ZH_CONTENT** | Cluster Patch 记录为 `zh`，分类在 [52]（中国西药出口拆解）。需补齐 kind 确认。 | **YES** (需补 kind) |
| **394** | `plaud-baseband-engineer-ai-earbuds-signal-analysis` | NO_EVIDENCE | **B. ZH_CONTENT** | 契约前 PLAUD 基带硬件分析，正文为中文，分类在 [50, 55]。 | **YES** (需编辑确认) |
| **388** | `shenzhen-biomed-special-items-import-export-process-2026` | NO_EVIDENCE | **B. ZH_CONTENT** | 契约前深圳生物医药特殊物品通关分析，正文为中文，分类在 [52]。 | **YES** (需编辑确认) |
| **358** | `waic-2026-agent-phone-robots-product-signal` | NO_EVIDENCE | **B. ZH_CONTENT** | 契约前 WAIC 2026 阶跃展台智能体分析，正文为中文，分类在 [50]。 | **YES** (需编辑确认) |
| **355** | `xiaomi-mijia-water-flosser-pro-product-signal` | NO_EVIDENCE | **B. ZH_CONTENT** | 契约前小米冲牙器产品信号，正文为中文，分类在 [50]。 | **YES** (需编辑确认) |
| **347** | `kimi-k3-zhihu-open-source-model` | NO_EVIDENCE | **B. ZH_CONTENT** | 契约前 Kimi K3 知乎争议与开源模型分析，正文为中文，分类在 [50]。 | **YES** (需编辑确认) |
| **213** | `schweberegale` | NO_EVIDENCE | **C. LEGACY_ARCHIVE** | 2025 年 10 月亚马逊德国浮动搁板市场调研历史种子，分类在 [33]。属于早期归档。 | **NO** (保持归档) |
| **209** | `20251013` | NO_EVIDENCE | **C. LEGACY_ARCHIVE** | 2025 年 9 月亚马逊法国首饰收纳深度分析历史种子，分类在 [33]。属于早期归档。 | **NO** (保持归档) |

---

## 3. 统计汇总与业务洞察

- **B. ZH_CONTENT (中文资产)**：`10` 篇（其中 479、470 为 Level A 双全；435、411 为 Level A 语言；6 篇为契约前存量中文科技/医药文）。
- **C. LEGACY_ARCHIVE (历史归档)**：`2` 篇（Post 213, 209）。
- **D. MANUAL_REVIEW (人工决策)**：`1` 篇（Post 444）。
- **A. MISSING_METADATA (真正丢失的 EN/RU)**：`0` 篇。

> **关键洞察**：
> 现网并不存在“属于 EN 或 RU 但被遗漏的在役文章”。所谓的 13 unknown 本质上是 **10 篇中文内容资产 + 2 篇 2025 历史归档 + 1 篇跨语言歧义文**。
> 这直接证明现网 58 EN / 25 RU 的发布体系非常健康完备，真正的诉求是为 10 篇中文内容赋予合法的 V2 语言身份。
