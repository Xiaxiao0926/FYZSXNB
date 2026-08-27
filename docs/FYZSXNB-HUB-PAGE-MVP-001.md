# FYZSXNB — 四大 Pillar Hub 落地页 MVP 架构与实施规范 (Hub Page MVP 001)

**文档编号:** `FYZ-SPEC-20260824-HUB-PAGE-MVP-001`  
**任务编号:** `FYZSXNB-HUB-PAGE-MVP-001`  
**执行角色:** Google Gemini Flash 3.7  
**设计依据:** [`docs/FYZSXNB-HUB-ARCHITECTURE-001.md`](file:///C:/Users/Administrator/Documents/Codex/2026-07-10/w/work/fyzsxnb-ui-v2/docs/FYZSXNB-HUB-ARCHITECTURE-001.md), [`docs/CONTENT-GROWTH-PRIORITY-001.md`](file:///C:/Users/Administrator/Documents/Codex/2026-07-10/w/work/fyzsxnb-ui-v2/docs/CONTENT-GROWTH-PRIORITY-001.md), [`docs/FYZSXNB-VISUAL-GUIDELINE-001.md`](file:///C:/Users/Administrator/Documents/Codex/2026-07-10/w/work/fyzsxnb-ui-v2/docs/FYZSXNB-VISUAL-GUIDELINE-001.md)  
**阶段状态:** `HUB_PAGE_MVP_COMPLETE`  
**实施原则:** 使用标准 WordPress Page 创建，零改动底层主题结构、零改动现有文章、零改动 taxonomy 与 URL 规则。  

---

## 一、 四大 Pillar Hub MVP 页面全景设计 (The 4 Pillar Hub MVPs)

```text
================================================================================
THE 4 PILLAR HUB LANDING PAGES (MVP OVERVIEW)
================================================================================
┌──────────┬─────────────────────────────────────┬──────────────────┬─────────────────────────────┐
│ 页面编号 │ Hub 名称 (Pillar Name)              │ 生产路由 (Route) │ 核心定位与价值主张          │
├──────────┼─────────────────────────────────────┼──────────────────┼─────────────────────────────┤
│ Hub 1    │ Cars From China in Russia           │ /cars-from-china/│ 俄罗斯中国汽车生态与平行进口│
│ Hub 1-RU │ Автомобили из Китая в России        │ /ru/cars-from-china/│ 选车、验车、报废税与配件总入口│
│ Hub 2    │ China Auto Repair Knowledge Base    │ /repair-knowledge-base/ │ 中国车型常见故障与维修数据库│
│ Hub 3    │ China Industrial Supply Chain       │ /industrial-supply-chain/ │ 工业传感器/自动化/欧美平替  │
│ Hub 4    │ China Medical & Regulatory Intel    │ /biomed-regulatory/ │ 医疗出海/FDA/NMPA UDI/POCT  │
└──────────┴─────────────────────────────────────┴──────────────────┴─────────────────────────────┘
```

---

## 二、 4 大 Hub 页面详细结构与模块规范

### 🚗 页面 1: Cars From China in Russia (俄罗斯中国汽车总入口)

```text
================================================================================
HUB 1: CARS FROM CHINA IN RUSSIA (MVP SPECIFICATION)
================================================================================
- 英文路由: /cars-from-china/
- 俄文路由: /ru/cars-from-china/
- 一句话定位: 俄罗斯市场最权威的中规新车/二手车平行进口、合规查验与维保知识总库。
- 视觉风格: Visual System 2.0 Template B (实车公路场景 + 现代智库轻量面板)

【页面组件结构 (Component Hierarchy)】
1. Hero Header (顶部导航与主视觉):
   - H1: Cars From China in Russia (Автомобили из Китая в России: покупка, проверка и обслуживание)
   - Deck: 中国新车/二手车在俄进口指南、17 位 VIN 与 EPTS 验真、2026 报废税计算及常见故障排查。
   - 快速导航徽章: [中国二手车] [VIN/EPTS验真] [报废税计算] [品牌专区] [维保与配件]

2. Module 1: China Used Cars Import Ecosystem (中国二手车进口生态):
   - 内容: 俄罗斯进口中国二手车爆发趋势、车况评级、原厂公里数核验、平行进口采购流程。
   - 核心文章锚点: 探岳选车总览 (Post 640), 极氪/比亚迪远程锁车防范 (Post 415)。

3. Module 2: Vehicle Verification & Customs (车辆查验与通关法务):
   - 内容: 17 位 VIN 解码、EPTS (СЭП / elpts.ru) 电子车辆护照验真、2026 报废税计算规则 (3 400 ₽ vs 800 800 ₽)、СБКТС 认证。
   - 核心文章锚点: EPTS 验真指南 (Post 484), 2026 报废税阶梯 (Post 432)。

4. Module 3: China Brand Matrix (核心品牌矩阵导航):
   - 品牌卡片:
     * [大众中国 (FAW/SAIC-VW)]: Tayron, Tiguan L, Tharu, Passat Pro (MQB A2, DKV/DPL 发动机).
     * [丰田中国 (GAC/FAW-Toyota)]: Camry (XV70/80), Wildlander, RAV4 (TNGA 平台).
     * [中国自主品牌]: 吉利 (Monjaro, Tugella), 奇瑞 (Tiggo 8 Pro Max), 长城 (Tank 300/500).
     * [新能源系列]: 比亚迪 (Song Plus, Frigate 07), 极氪 (001, 009), 理想 (L7/L8/L9).

5. Module 4: Repair & Adaptation Knowledge (维保与本土化):
   - 内容: DQ381 双离合维修、EA888 配件直采、车机改俄语 (русификация)、冬包改装。
   - 核心文章锚点: DQ381 变速箱维修 (Post 514), 探岳发动机配件 (Post 510), 奇瑞车机刷机 (Post 485)。

6. Module 5: Research CTA & Legal Verification Tool (合规咨询与查验入口):
   - 引导车主/车商提交 VIN 码进行中俄互换零件查询与 EPTS 状态核验。
================================================================================
```

---

### 🔧 页面 2: China Auto Repair Knowledge Base (中国汽车维修数据库)

```text
================================================================================
HUB 2: CHINA AUTO REPAIR KNOWLEDGE BASE (MVP SPECIFICATION)
================================================================================
- 生产路由: /repair-knowledge-base/ (俄文: /ru/repair-knowledge-base/)
- 一句话定位: 结构化梳理中规车型常见故障现象、DTC 诊断码与中国低成本维修方案的工程数据库。
- 视觉风格: Visual System 2.0 Template A (硬核深色工程风 / CAD 剖面与电路拓扑)

【页面组件结构 (Component Hierarchy)】
1. Hero Header:
   - H1: China Auto Repair Knowledge Base (База знаний по ремонту авто из Китая)
   - Deck: 从故障码到配件直采：基于中国原厂工程方案的变速箱、发动机、颗粒捕捉器与车机维保指南。

2. The "Failure-to-Parts" Loop (故障到配件闭环架构):
   - 交互卡片: [选择车型] → [选择故障系统 (DCT / 发动机 / GPF / 车机)] → [查看 DTC 与维修方案] → [获取配件编号]。

3. Module 1: Transmission Systems (双离合变速箱专区):
   - 核心系统: DQ381 / DQ380 (0GC) 7 速湿式双离合。
   - 常见故障: P173500 / P173600 (离合器位置传感器电气故障)、阀体微距焊接与压力传感器芯片更换。
   - 核心文章锚点: DQ381 应急模式维修实操 (Post 514), DQ381 案例深度解析 (Post 513)。

4. Module 2: Powertrain & Emissions (发动机与排放系统):
   - 核心系统: EA888 Gen3B (DKV/DPL 2.0 TSI) 与 国六 GPF 颗粒捕捉器。
   - 常见故障: GPF 堵塞再生失败、节温器漏水、双喷射积碳。
   - 核心文章锚点: 探岳 GPF 堵塞车主案例 (Post 512), 探岳 330TSI 发动机配件直采 (Post 510)。

5. Module 3: Infotainment & ADAS Electronics (车机固件与智驾):
   - 核心系统: 奇瑞/吉利车机芯片 (高通 8155)、比亚迪 CAN-FD 智驾总线。
   - 核心文章锚点: 奇瑞车机固件与 ADB 软件安装 (Post 485), 护卫舰 07 Openpilot CAN 接线 (Post 504)。
================================================================================
```

---

### ⚙️ 页面 3: China Industrial Supply Chain (中国工业供应链智库)

```text
================================================================================
HUB 3: CHINA INDUSTRIAL SUPPLY CHAIN (MVP SPECIFICATION)
================================================================================
- 生产路由: /industrial-supply-chain/ (俄文: /ru/industrial-supply-chain/)
- 一句话定位: 俄罗斯重工业与制造业在欧美断供背景下的“中国工业自动化与传感器替代采购指南”。
- 视觉风格: Visual System 2.0 Template C (高光工业仪器摄影 + 规范参数对照)

【页面组件结构 (Component Hierarchy)】
1. Hero Header:
   - H1: China Industrial Supply Chain & Automation (Промышленные компоненты и автоматизация из Китая)
   - Deck: 工业传感器、变频驱动、PLC 与流体五金：欧美停产部件的中国原厂替代与接线选型指南。

2. Module 1: Industrial Sensors & Transmitters (工业传感器与变送器):
   - 覆盖品类: 4-20mA 两线制回路压力变送器、PT100/PT1000 温度传感器、扩散硅芯片。
   - 核心文章锚点: 4-20mA 压力变送器变频器接线规范 (Post 489)。

3. Module 2: Industrial Fluidics & Thread Adapters (工业流体与螺纹接头):
   - 覆盖品类: M22 螺纹、G1/4 管螺纹、NPT 国际标准转换接头、高压管路。
   - 核心文章锚点: 高压清洗机软管接头与转接头选型 (Post 433)。

4. Module 3: Automation & PLC Control (自动化与工控系统):
   - 覆盖品类: 国产 PLC (汇川/台达) 替代西门子 S7、变频器 (VFD) Modbus RTU RS485 联调。

5. Module 4: B2B Sourcing & Cross-Reference Request (工业选型咨询入口):
   - 为海外工厂工程师提供“欧美旧件型号 → 中国现货替代品”对照咨询通道。
================================================================================
```

---

### 🧬 页面 4: China Medical & Regulatory Intelligence (中国医疗出海合规智库)

```text
================================================================================
HUB 4: CHINA MEDICAL & REGULATORY INTELLIGENCE (MVP SPECIFICATION)
================================================================================
- 生产路由: /biomed-regulatory/
- 一句话定位: 中国药企、IVD 诊断与医疗器械企业走向欧美及全球市场的法定监管合规决策平台。
- 视觉风格: Visual System 2.0 Template E (Nature/Clinical 纯白底与医疗深蓝)

【页面组件结构 (Component Hierarchy)】
1. Hero Header:
   - H1: China Medical & Regulatory Intelligence Platform
   - Deck: 21 CFR Part 207 FDA 境外药企登记、NMPA UDI 2027 强制时间线与分子 POCT 试剂盒技术评估。

2. Module 1: FDA Global Compliance Roadmap (FDA 国际监管合规):
   - 核心法规: 21 CFR 207 境外药品生产设施登记、FEI 编码获取、US Agent 授权与 SPL XML 提交。
   - 核心文章锚点: FDA 境外药企注册指南 (Post 466)。

3. Module 2: NMPA Medical Device UDI 2027 (中国医疗器械唯一标识):
   - 核心法规: 2027 年 II 类/III 类医疗器械 DI/PI 赋码、国家药监局 UDI 数据库申报。
   - 核心文章锚点: NMPA UDI 2027 合规指南 (Post 479)。

4. Module 3: Molecular POCT & Diagnostics Procurement (分子 POCT 实验室采购):
   - 覆盖系统: iFIND S2/S4/S8 全自动分子 POCT 仪器、结核 (MTB/RIF) 与耐药基因 (INH/FQ) 检测试剂盒。
   - 核心文章锚点: POCT 系统采购 (Post 431), TBR 试剂盒 (Post 437), IFQ 耐药盒 (Post 439)。
================================================================================
```

---

## 三、 SEO 元数据配置规范 (SEO Metadata Blueprint)

| 页面 | Title (SEO 标题) | Meta Description (搜索描述) | H1 (主标题) |
|:---|:---|:---|:---|
| **Hub 1 (EN)**<br>`/cars-from-china/` | Cars From China in Russia: Import, Verification & Repair Guide | Complete intelligence guide on importing Chinese new & used cars to Russia. VIN & EPTS check, 2026 recycling fee, DQ381 repair and spare parts. | Cars From China in Russia: Selection, Verification & Repair |
| **Hub 1 (RU)**<br>`/ru/cars-from-china/` | Автомобили из Китая в России: импорт, проверка ЭПТС, утильсбор и ремонт | Полное руководство по покупке и обслуживанию авто из КНР: проверка ЭПТС по VIN, расчет утильсбора 2026, ремонт DQ381 и запчасти. | Автомобили из Китая в России: покупка, проверка и обслуживание |
| **Hub 2**<br>`/repair-knowledge-base/` | China Auto Repair Knowledge Base: DQ381, EA888, GPF & Firmware Solutions | Structured failure diagnostic database for Chinese-market vehicles. DQ381 mechatronic sensor fixes, EA888 DKV parts, GPF cleaning and TCU flashing. | China Auto Repair Knowledge Base: Diagnostics & China Solutions |
| **Hub 3**<br>`/industrial-supply-chain/`| China Industrial Supply Chain: Sensors, Automation & EU Replacement | Technical sourcing guide for Chinese industrial components: 4-20mA pressure transmitters, VFD Modbus wiring, M22 thread adapters and PLC replacements. | China Industrial Supply Chain & Automation Solutions |
| **Hub 4**<br>`/biomed-regulatory/` | China Medical & Regulatory Intelligence: FDA 21 CFR 207, NMPA UDI & POCT | Statutory compliance and procurement platform for Chinese biomed: FDA foreign drug registration, NMPA UDI 2027 and molecular POCT evaluations. | China Medical & Regulatory Intelligence Platform |

---

## 四、 首页 `fyz-desks` 升级为四大 Pillar Hub 黄金入口

首页中段的 `fyz-desks` 网格升级为 **四大战略 Pillar Hub 门户卡片**，直接导流：

```text
================================================================================
HOMEPAGE DESKS UPGRADE SPECIFICATION
================================================================================
[Card 1: 汽车生态总库]
- Title: 🚗 Cars From China in Russia (Автомобили из Китая)
- Copy: 中规新车/二手车进口导购、17 位 VIN 与 EPTS 验真、2026 报废税与配件。
- Link: /cars-from-china/ (RU: /ru/cars-from-china/)

[Card 2: 维修与故障数据库]
- Title: 🔧 Auto Repair Knowledge Base (База знаний по ремонту)
- Copy: DQ381 双离合传感器修复、EA888 零件直采、GPF 堵塞与车机固件。
- Link: /repair-knowledge-base/

[Card 3: 工业自动化与替代件]
- Title: ⚙️ Industrial Supply Chain (Промышленные компоненты)
- Copy: 4-20mA 压力变送器选型、变频器 Modbus 联调与欧美停产件平替。
- Link: /industrial-supply-chain/

[Card 4: 医疗出海与合规智库]
- Title: 🧬 Biomed & Regulatory (Биомедицина и регуляторика)
- Copy: FDA 境外药企 21 CFR 207 登记、NMPA UDI 2027 与分子 POCT 采购。
- Link: /biomed-regulatory/
================================================================================
```

---

## 五、 MVP 分步实施计划 (Implementation Roadmap)

```text
================================================================================
PHASED IMPLEMENTATION WORKFLOW (SAFE MVP ROLLOUT)
================================================================================
- Step 1 (页面创建): 通过 WordPress REST API 创建 4 个标准 Page (包含 EN 与 RU 分支)。
- Step 2 (HTML 语义组件注入): 采用轻量 Gutenberg HTML 块注入 Hero、子专题网格、旗舰文章卡片与 CTA。
- Step 3 (双向内链绑定): 在 11 篇已完成升级的核心文章正文顶部与底部添加对应 Hub 链接。
- Step 4 (首页入口同步): 更新 inc/home.php 中的 desks 配置，指向全新的 4 个 Hub URL。
- Step 5 (全量验收): 测试 HTTP 200、响应式布局 (390px/780px/1440px) 与 LiteSpeed 缓存刷新。
================================================================================
```

---

## 六、 最终交付状态

```text
HUB_PAGE_MVP_COMPLETE

DELIVERABLE_DOCUMENT:
docs/FYZSXNB-HUB-PAGE-MVP-001.md

SPECIFICATIONS_FROZEN:
- 4 Pillar Hub Landing Pages (Structure, SEO Fields, Components, Internal Links)
- Homepage Desks Gateway Upgrade Plan
- Safe WordPress Page-based MVP Rollout Strategy

STATUS:
MVP ARCHITECTURE READY (Awaiting user directive for live WordPress page creation)

STOP
```
