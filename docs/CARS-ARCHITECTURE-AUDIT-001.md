# FYZSXNB — Cars From China Architecture & Content Audit

**Document ID:** `FYZ-DOC-20260821-CARS-ARCHITECTURE-AUDIT-001`  
**Task ID:** `FYZSXNB-CARS-ARCHITECTURE-AUDIT-20260821-001`  
**Stage:** `ARCHITECTURAL AUDIT & STRATEGIC REVIEW`  
**Status:** `AUDIT_COMPLETE`  
**Mode:** `READ ONLY AUDIT (No Code / DB / Theme Changes)`  

---

## 1. Executive Summary & Architecture Overview (Current Architecture)

FYZSXNB 的 **Cars From China (中国汽车 / Автомобили из Китая)** 模块是一套基于证据优先（Evidence-First）、双语隔离与分层路由的内容架构。该架构在 `0.3.0 ~ 0.3.2` 阶段完成核心代码脚手架设计与部署，并与最新的 `0.4.5 Resolver V2` 语言契约完全协同。

### 1.1 核心架构组件拓扑
```text
                          +-------------------------------------------------------------+
                          |                 Cars From China 核心架构                    |
                          +-------------------------------------------------------------+
                                                         |
         +-----------------------------------------------+-----------------------------------------------+
         |                                               |                                               |
+-------------------+                           +-------------------+                           +-------------------+
|     路由与页面层    |                           |     分类与数据层   |                           |    证据与质控门禁   |
+-------------------+                           +-------------------+                           +-------------------+
| • EN Hub (Page 507)                           | • fyz_vehicle     |                           | • Launch Gate     |
| • RU Hub (Rewrite)                            |   (Brand > Model) |                           |   (noindex,follow)|
| • Brand Archives                              | • fyz_research_   |                           | • Empty Section   |
| • Model Archives                              |   type (7 种类型) |                           |   Suppression     |
| • 根目录单篇文章 URL                           | • Category 50/56/54|                           | • Case Matrix /   |
+-------------------+                           +-------------------+                           |   Issue Matrix    |
                                                                                                +-------------------+
```

---

## 2. 核心架构五问深度解答 (Architecture Questions)

### Q1: 当前 Cars From China 是文章分类、独立内容类型、还是普通 Page？
* **结论**：**混合分层架构 (Hybrid Architecture)**。
  1. **实体内容类型 (Post Type)**：依然是 WordPress 原生 `post`（文章），未新建复杂的独立 Custom Post Type（CPT），最大化复用了全站的 SEO、缓存、REST API 与发布发布流水线；
  2. **Hub 聚合入口**：
     - **EN Hub**：标准 WordPress Page（ID: 507，slug: `cars-from-china`，模板: `page-templates/cars-from-china-hub.php`，当前状态: `draft`）；
     - **RU Hub**：采用 URL 虚拟重写路由 `/ru/cars-from-china/`，通过 `template_include` 过滤器直接装载 `cars-from-china-hub.php` 模板，无需在数据库中新建冲突的重复页面对象；
  3. **分类学定位**：明确**不创建**名为 `cars-from-china` 的冗余 Category，而是通过专属分类法（Taxonomy）实现多维标记。

### Q2: 当前 URL 结构在 `/cars-from-china/` 下是如何组织的？
* **结论**：**双语分层路由 (Hierarchical Bilingual Routing)**。
  - **英文端 (EN Routes)**：
    * 桌面 Hub：`https://fyzsxnb.com/cars-from-china/`
    * 品牌聚合页：`https://fyzsxnb.com/cars-from-china/{brand}/`（如 `/cars-from-china/volkswagen/`）
    * 车型聚合页：`https://fyzsxnb.com/cars-from-china/{brand}/{model}/`（如 `/cars-from-china/volkswagen/tayron/`）
  - **俄文端 (RU Routes)**：
    * 俄文 Hub：`https://fyzsxnb.com/ru/cars-from-china/`
    * 俄文品牌页：`https://fyzsxnb.com/ru/cars-from-china/{brand}/`（如 `/ru/cars-from-china/volkswagen/`）
    * 俄文车型页：`https://fyzsxnb.com/ru/cars-from-china/{brand}/{model}/`（如 `/ru/cars-from-china/volkswagen/tayron/`）
  - **单篇文章 URL**：
    * 保持全站扁平永久链接 `https://fyzsxnb.com/{post-slug}/`（例如 `https://fyzsxnb.com/volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay/`），通过车型页中的卡片和相关文章模块进行精准内链注入。

### Q3: 品牌（Brand）是否已经存在 Taxonomy？
* **结论**：**已存在，作为 `fyz_vehicle` 的顶级父项 Term（Parent Term）**。
  - 架构设计将品牌与车型统一收敛在分层分类法 `fyz_vehicle` 中；
  - 顶级 Term（`parent = 0`）即为 Brand（如 `volkswagen` ID: 59, `audi`, `toyota`, `hyundai`, `honda`, `bmw`）。

### Q4: 车型（Model）是否已有 `brand/model` 层级关系？
* **结论**：**已完全建立并实施严格的代码级父子关系防伪校验**。
  - **数据库层**：`fyz_vehicle` 为 `hierarchical => true`，车型 Term（如 `tayron` ID: 60）的 `parent` 指向品牌 Term（`volkswagen` ID: 59）；
  - **路由拦截防伪**：`fyzsxnb_cfc_validate_parent()` 在 `parse_query` 阶段进行断言，若 URL 中的车型与品牌非直属父子关系（如恶意访问 `/cars-from-china/audi/tayron/`），立即触发 **404 Not Found**，杜绝伪造 URL。

### Q5: 未来扩展是否支持全球与中国自主品牌（Toyota, VW, BMW, BYD, Chery, Haval 等）？
* **结论**：**完全支持，原生解耦且具备即插即用扩展性**。
  - **合资/外资在华生产车型**：已在初始矩阵定义 `volkswagen` (tayron, tharu, golf, t-roc)、`audi` (q3, a3)、`toyota` (corolla)、`hyundai` (elantra)、`honda` (vezel)、`bmw` (x1)；
  - **中国自主品牌**：只需在矩阵或管理后台直接插入 Term（如 `byd` -> `frigate-07`/`han`/`tang`、`chery` -> `tiggo-7-pro`/`tiggo-8-pro`、`haval` -> `h6`/`jolion`），现有渲染器即可自动适配。

---

## 3. 当前车辆内容资产全景台账 (Content Inventory)

在全站已发布的 96 篇正式文章与页面资产中，车辆及中国汽车出海相关资产统计如下：

### 3.1 车辆相关文章台账明细 (共 15 篇)

| Post ID | 语言 | 关联车型 / 品牌 | 所属分类 | 研究类型 / 核心议题 | 文章 Slug | 线上状态 |
|:---:|:---:|:---|:---:|:---|:---|:---:|
| **514** | RU | VW Tayron | 50, 54 | `common-problems` (DQ381 变速箱故障案例) | `volkswagen-tayron-kitay-dq381-avariynyy-rezhim-realnye-sluchai` | Published |
| **513** | EN | VW Tayron | 50 | `common-problems` (DQ381 Emergency Mode) | `china-market-volkswagen-tayron-dq381-emergency-mode-owner-cases` | Published |
| **512** | RU | VW Tayron | 50, 54 | `common-problems` (330TSI 颗粒捕捉器 GPF 堵塞) | `volkswagen-tayron-330tsi-kitay-gpf-opyt-vladeltsev` | Published |
| **511** | EN | VW Tayron | 50 | `common-problems` (330TSI GPF Owner Cases) | `china-market-volkswagen-tayron-330tsi-gpf-owner-cases` | Published |
| **510** | RU | VW Tayron | 50, 54 | `parts-compatibility` (DKV/DPL/DTH 发动机配件差异) | `volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay` | Published |
| **509** | EN | VW Tayron | 50 | `parts-compatibility` (330TSI DKV/DPL/DTH Parts) | `china-market-volkswagen-tayron-330tsi-dkv-dpl-dth-parts` | Published |
| **504** | RU | BYD (Frigate 07) | 50, 54 | `overview` / `case-study` (Openpilot 自动驾驶适配) | `byd-frigate-07-openpilot-dannye-dlya-adaptacii` | Published |
| **503** | RU | BYD (通用) | 50, 54 | `repair-guide` (Openpilot 相机/CAN/ECU 指纹检测) | `kak-proverit-byd-pered-ustanovkoy-openpilot-camera-can-ecu-fingerprint` | Published |
| **500** | RU | BYD (平台) | 50, 54 | `overview` (Openpilot BYD 2026 支持现状) | `openpilot-byd-2026-support-open-source` | Published |
| **485** | RU | Chery (Tiggo 7/8 Pro) | 54, 56 | `repair-guide` (车机 Android Auto 升级与刷机) | `chery-android-auto-obnovlenie-tiggo-7-8-pro` | Published |
| **484** | RU | 进口车通用 (二手车) | 54, 56 | `guide` (购车前 VIN 查验电子车辆档案 ЭПТС) | `proverka-epts-po-vin-pered-pokupkoj` | Published |
| **432** | RU | 进口车通用 (关税) | 54, 56 | `guide` (2026 中国汽车进口俄罗斯报废税 Утильсбор) | `utilization-fee-china-car-import-russia-2026` | Published |
| **426** | RU | BMW (N55 发动机) | 54, 56 | `repair-guide` (更换气门室盖垫后漏油分析) | `bmw-n55-oil-leak-after-gasket-replacement` | Published |
| **415** | RU | 纯电/新势力通用 | 50, 54, 56 | `market-version` (中国电动车平行出口远程锁车风险) | `kitayskiy-elektromobil-udalennaya-blokirovka-eksport-risk` | Published |
| **405** | RU | BMW (N55 发动机) | 54, 56 | `parts-compatibility` (密封垫材质 FKM 对比 NBR) | `ru-bmw-n55-oil-leak-gasket-fkm-nbr` | Published |

### 3.2 页面与分类法资产统计汇总

| 资产类型 | 数量 | 详细明细 | 备注 |
|:---|:---:|:---|:---|
| **核心页面 (Pages)** | 1 + 1 虚拟 | • Page 507 (EN Hub `/cars-from-china/` - Draft)<br>• `/ru/cars-from-china/` (RU Hub - Rewrite) | 模板 `cars-from-china-hub.php` |
| **已注册分类法 (Taxonomies)** | 2 | • `fyz_vehicle` (车辆品牌与车型)<br>• `fyz_research_type` (研究类型) | 深度集成于主题与 REST |
| **已打标品牌 (Brands)** | 6 (种子) + 3 (存量) | • 种子品牌: Volkswagen, Audi, Toyota, Hyundai, Honda, BMW<br>• 存量覆盖品牌: BYD, Chery, BMW | 品牌 Term 随时可扩展 |
| **已打标车型 (Models)** | 10 (种子) + 3 (存量) | • 种子车型: Tayron, Tharu, Golf, T-Roc, Q3, A3, Corolla, Elantra, Vezel, X1<br>• 存量覆盖车型: Frigate 07, Tiggo 7/8 Pro | 仅有已发布文章的车型生成链接 |

---

## 4. 现有 SEO 架构与车型数据库模式适配度分析 (Existing SEO Structure)

### 4.1 现有 SEO 架构现状
1. **Title & Meta Tags**：通过 AIOSEO 及主题钩子动态生成。车型页自动提取车型名及双语 Deck 描述；
2. **规范链接 (Canonical)**：品牌页与车型页均指向分层规范 URL（如 `https://fyzsxnb.com/cars-from-china/volkswagen/tayron/`）；
3. **多语言互链 (Hreflang)**：基于 `/ru/` 前缀区分，语言属性由 0.4.5 Resolver V2 精准注入 `lang="ru-RU"` / `lang="en-US"`；
4. **面包屑导航 (Breadcrumbs)**：车型页原生提供 `<nav class="cfc-crumbs">`（`Cars from China › Volkswagen › Tayron`）；
5. **发布门禁与防死链 (Zero Dead Links & Launch Gate)**：
   - 模型矩阵中未发布的车型自动渲染为纯文本 `<span class="cfc-matrix__pending">`，绝不输出空页面或死链；
   - 车型页面中若某个研究类型暂无文章，渲染器自动静默消除该区块（Empty-Section Suppression），杜绝薄内容（Thin Content）。

### 4.2 车型数据库（Vehicle Database / Catalog Mode）能力评估与差距 (Gap Analysis)

```text
+-----------------------+-----------------------+--------------------------------------------------------------+
| 评估维度              | 当前支持状态          | 车型数据库（Catalog Mode）差距与升级需求                     |
+-----------------------+-----------------------+--------------------------------------------------------------+
| **层级化 URL 结构**   | **完全支持 (PASS)**   | `/cars-from-china/{brand}/{model}/` 原生支持车型聚合。        |
| **Schema 结构化数据** | **基础支持 (BASIC)**  | 目前输出标准 `WebPage` / `Article`，缺少 `schema.org/Car`、  |
|                       |                       | `Vehicle`、`engineDisplacement` 等汽车专用微数据字段。       |
| **技术参数结构化**    | **缺失 (GAP)**        | 排量、年款、发动机代号（如 DKV）、变速箱（如 DQ381）分散于正  |
|                       |                       | 文，缺少文章级/车型级自定义元数据字段（Custom Meta）。       |
| **多维交叉筛选 (Faceted)**| **基础支持 (BASIC)** | 仅支持按 `fyz_vehicle` + `fyz_research_type` 筛选，暂不支持   |
|                       |                       | 按“动力类型（燃油/PHEV/EV）”、“排量”、“系统故障域”交叉检索。   |
| **配件号直接检索**    | **全文搜索 (BASIC)**  | 依赖 WP 原生标题与正文全文检索，缺少专用 OE 零件号独立索引。 |
+-----------------------+-----------------------+--------------------------------------------------------------+
```

---

## 5. 当前架构局限性与五大业务扩展场景评估 (Current Limitations & Expansion)

针对未来战略规划的五大业务场景，对现有架构的扩展性、升级需求与禁改边界进行全面界定：

```text
                                        业务场景扩展适配矩阵
+----------------------------------------+-------------------+-----------------------------------+
| 业务场景                               | 架构扩展适配度    | 需要改造 / 升级的模块             |
+----------------------------------------+-------------------+-----------------------------------+
| 1. 中国自主品牌出海 (BYD/Chery/Haval)  | **直接扩展 (100%)**| 扩展种子矩阵，更新 Hub 车型矩阵分类|
| 2. 中国出口二手车 (合规/检测/关税)     | **直接扩展 (95%)** | 建议新增 `import-clearance` 分类  |
| 3. 中国渠道进口欧美日平行车 (Tayron等) | **原生完全适配 (100%)| 无需改动，当前 Tayron 即为范本    |
| 4. 车辆维修与故障数据库 (TSB/案例/通病)| **需结构化升级 (70%)| 需引入技术参数与故障域元数据字段   |
| 5. 配件解决方案 (OE号/替代件/跨版本)   | **需检索升级 (65%) | 建议建立零件号与互换关系专用索引   |
+----------------------------------------+-------------------+-----------------------------------+
```

### 5.1 哪些可以直接扩展？（Zero Code Modification）
1. **合资与自主品牌新车型入库**：直接向 `fyz_vehicle` 分类法插入新品牌与新车型，文章发布时打上对应 Term 即可自动在车型页与相关文章呈现；
2. **双语文章发布流水线**：直接复用 `publish_single_article.py`，传入 `--content-language` 及对应 Taxonomy Term，无需调整后端发布引擎；
3. **内容聚合与内链系统**：车型聚合页与品牌聚合页自动按发布状态更新链接，零维护成本。

### 5.2 哪些需要升级？（Upgrade Required in Future Phases）
1. **车型元数据体系升级 (Vehicle Metadata Schema)**：
   - 引入专有元数据字段：`_cfc_engine_code` (发动机代号), `_cfc_transmission_code` (变速箱代号), `_cfc_market_years` (产销年份), `_cfc_drivetrain` (ICE/PHEV/EV)；
2. **Schema.org 汽车专有微数据 (Automotive Schema)**：
   - 在 `fyzsxnb-p0-seo-patch.php` 中扩充针对车型聚合页的 `schema.org/Car` 与 `schema.org/ItemPage` 结构化标记；
3. **Hub 页面分类矩阵重构 (Hub Presentation Upgrade)**：
   - 当品牌数量超过 10 个时，当前单列矩阵需升级为“中国自主品牌（EV/混动）”与“中国制造全球品牌（平行出口）”分组排布展示；
4. **Launch Gate 阈值评估与开门 (Launch Gate Release)**：
   - 当前锁定条件为：$\ge 3$ 个车型，每车型 $\ge 2$ 篇文章，EN $\ge 3$ 且 RU $\ge 3$。当后续补充 Toyota Corolla 与 Audi Q3 文章后，需按规程翻转 `FYZSXNB_CFC_LAUNCH_GATE_OPEN = true` 并解除 `noindex`。

### 5.3 哪些绝对不要修改？（Strict Invariants — DO NOT TOUCH）
1. **底层语言契约与 Resolver V2**：`fyzsxnb_resolve_content_locale()` 及 `_fyz_content_language` 核心逻辑严禁因汽车模块做任何硬编码分叉；
2. **证据优先与案例准入契约 (Case Contract)**：严格禁止为了填充数据库而生成未经真实信源佐证的“AI 虚构车型故障”或“捏造零件兼容性”；
3. **扁平文章 URL 与 Canonical 规范**：单篇文章 URL 必须保持在根路径，Canonical 严格自指向，绝不能因为层级聚合页而修改单篇文章原生永久链接。

---

## 6. 下一步演进规划与建议 (Strategic Recommendations)

```text
阶段一 (当前已就绪)                阶段二 (内容矩阵填充)               阶段三 (车型库体验升级)
+-----------------------+         +-----------------------+         +-----------------------+
| 0.3.2 CFC 脚手架      |         | 填充 Corolla & Q3     |         | 结构化参数 (Specs)     |
| 0.4.5 Resolver V2     | ──────► | 达到门禁标准 (>=3车)   | ──────► | Automotive Schema     |
| Tayron (6 篇双语已发) |         | 开放 Launch Gate 索引 |         | 车型库高级检索与筛选  |
+-----------------------+         +-----------------------+         +-----------------------+
```

1. **建议 1（优先填充现有既定合资车型）**：
   - 优先按照既定路线图完成 **Toyota Corolla** 与 **Audi Q3** 的调研与双语案例（TAY/COR/Q3 三车矩阵达成），满足 Launch Gate 门槛，正式开放搜索引擎索引与导航入口；
2. **建议 2（平滑收编现有自主品牌存量内容）**：
   - 将现有存量的 BYD（504, 503, 500）与 Chery（485）文章打上新注册的 `fyz_vehicle` Term，零开发成本扩充自主品牌展示面；
3. **建议 3（车型数据库中长期演进）**：
   - 在完成内容基础沉淀后，针对高价值配件与故障排查需求，规划专有技术参数 Custom Fields 与 Schema.org 汽车专有结构化数据扩展方案。

---

## 7. 审计门禁与结论

```text
AUDIT_STATUS:
COMPLETE

CURRENT_ARCHITECTURE:
HYBRID (Post Type + fyz_vehicle / fyz_research_type + Hierarchical Rewrites)

EXISTING_CAR_ARTICLES:
15 (6 Dedicated CFC Tayron + 9 Generic Automotive/EV/Import)

EXPANSION_CAPABILITY:
PASS (Native support for Joint-Venture & Chinese Domestic Brands)

PRODUCTION_CHANGE:
NO

STOP
```
