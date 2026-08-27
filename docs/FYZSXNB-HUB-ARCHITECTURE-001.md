# FYZSXNB — 知识库架构与 SEO 主题集群体系设计规范 (Knowledge Hub Architecture 001)

**文档编号:** `FYZ-ARCH-20260824-HUB-ARCHITECTURE-001`  
**任务编号:** `FYZSXNB-HUB-ARCHITECTURE-001`  
**执行角色:** Google Gemini Flash 3.7  
**设计定位:** 信息架构 (IA) / SEO Topic Cluster 主题集群 / 知识库中台设计  
**设计目标:** 将 FYZSXNB 从扁平的“文章博客”升级为面向俄罗斯及全球海外市场的 **“China Technology & Supply Chain Intelligence Hub (中国技术与供应链决策情报智库)”**。  
**阶段状态:** `HUB_ARCHITECTURE_COMPLETE`  

---

## 1. 核心架构哲学与 4 层金字塔结构 (Core Architecture Philosophy)

```text
================================================================================
4-TIER KNOWLEDGE HUB PYRAMID ARCHITECTURE
================================================================================
┌──────────────────────────────────────────────────────────────────────────────┐
│ Level 1: Pillar Hub (一级专题大门 / 权威总入口)                              │
│ - 覆盖 4 大核心战略领域，汇聚全站主题权重，面向行业大词与高搜索量商业词。   │
├──────────────────────────────────────────────────────────────────────────────┤
│ Level 2: Sub Hub / Brand / Topic Category (二级子专题 / 品牌与品类枢纽)      │
│ - 如: 大众中规车、中国二手车进口、EPTS 验真、工业变送器、FDA 药监合规。     │
├──────────────────────────────────────────────────────────────────────────────┤
│ Level 3: Article Cluster (三级深度文章集群 / 核心决策指南)                   │
│ - 详尽解析长文 (1 封面 + 2 正文图)，覆盖长尾技术搜索意图与具体车型/设备。   │
├──────────────────────────────────────────────────────────────────────────────┤
│ Level 4: Tools / Failure Solutions / B2B Entry (四级工具与商业转化闭环)      │
│ - 报废税计算器、VIN 解码库、DTC 故障码排查器、中俄配件替代对照表。           │
└──────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. 必须落地的四大一级 Pillar Hub 详构 (The 4 Pillar Hubs)

```text
================================================================================
THE 4 PILLAR INTELLIGENCE HUBS
================================================================================
┌──────────┬─────────────────────────────────────┬──────────────┬───────────────────────────────┐
│ Hub 编号 │ Hub 名称 (Pillar Name)              │ 战略优先级   │ 核心用户搜索意图与商业闭环    │
├──────────┼─────────────────────────────────────┼──────────────┼───────────────────────────────┤
│ Hub A    │ Cars From China in Russia           │ ★★★★★ (最高) │ 选车/平行进口/二手车/通关验真 │
│ Hub B    │ China Auto Repair Knowledge Base    │ ★★★★★ (最高) │ 故障诊断(DTC)/配件直采/维修包 │
│ Hub C    │ China Industrial Supply Chain       │ ★★★★☆ (重点) │ 工业传感器/自动化/欧美停产平替│
│ Hub D    │ China Medical & Regulatory Intel    │ ★★★★☆ (高价值)│ 药企出海/FDA/NMPA UDI/POCT采购│
└──────────┴─────────────────────────────────────┴──────────────┴───────────────────────────────┘
```

---

### 🚗 Hub A: Cars From China in Russia (俄罗斯市场中国汽车生态总库)
- **定位**: 成为俄罗斯全网最具技术公信力、覆盖中规新车/二手车平行进口与合规的“第一决策入口”。
- **涵盖核心品牌**:
  * **合资中规主力**: 大众中国 (一汽/上汽: Tayron, Tiguan L, Tharu, Passat Pro, Talagon)、丰田中国 (广汽/一汽: Camry, Wildlander, RAV4, Crown Kluger)。
  * **中国自主头部**: 吉利 (Monjaro, Tugella, Coolray)、奇瑞 (Tiggo 7/8 Pro Max, Exeed)、长城 (Tank 300/500, Haval Dargo/Jolion)。
  * **新能源与智能车**: 比亚迪 (Song Plus, Tang, Han, Frigate 07)、极氪 (001, 009, X)、理想 (Li Auto L7/L8/L9)。
- **5 大下属子专题 (Sub-hubs)**:
  * **Sub-hub A1: China Used Cars Import (中国二手车进口与采购行情)**: 俄罗斯自中国进口二手车政策、车况评估、公里数核验、拍卖行直采。
  * **Sub-hub A2: Vehicle Verification & Legal (验车与合规工具)**: 17 位 VIN 解码、EPTS (СЭП / elpts.ru) 电子护照核验、2026 报废税计算、СБКТС 安全认证。
  * **Sub-hub A3: China EV Overseas Risks (新能源车海外落地与避坑)**: 远程锁车 (Remote Bricking) 防范、车机离线激活、极寒电池管理、主账号权限转移。
  * **Sub-hub A4: China Car Local Adaptation (本土化与冬包适配)**: 中文车机刷俄语固件 (русификация)、MIB3 汉化、加装座椅/方向盘/后视镜加热。
  * **Sub-hub A5: China Auto Parts Supply Chain (中俄汽配供应链)**: EPC 电子零件目录、原厂零件号 (OEM No.) 查询、中俄替代件互换。

---

### 🔧 Hub B: China Auto Repair Knowledge Base (中国汽车维修与故障数据库)
- **定位**: 全球首个结构化梳理“中国制造车型常见故障与中国工程解决方案”的维保知识中台。
- **核心逻辑: Failure-to-Parts Loop (从故障到配件闭环)**:
  ```text
  [车型与年款] → [故障现象与 DTC 诊断码] → [机电/电路故障诊断] → [中国低成本维修方案] → [配件直采/维修包转化]
  ```
- **核心重点技术专题**:
  * **DQ381 / DQ380 (0GC) 7 速湿式双离合**: 离合器位置传感器电气故障 (P173500 / P173600)、阀体压力传感器微距焊接修复、TCU 固件标定。
  * **EA888 Gen3B (DKV/DPL/DTH) 2.0 TSI**: 凸轮轴相位器、电子水泵节温器总成、双喷射系统积碳清洗。
  * **国六 GPF (颗粒捕捉器) 堵塞**: 差压传感器数据流读取、再生触发条件、物理清洗与屏蔽方案。
  * **ADAS & CAN-FD 智驾逆向**: 比亚迪/吉利 CAN-FD 线束针脚定义、Openpilot 转向控制抓包、毫米波雷达标定。
  * **车机 MCU/SoC 固件**: 奇瑞/吉利车机芯片 (高通 8155 / 联发科) 固件更新、USB ADB 提权与应用侧载。

---

### ⚙️ Hub C: China Industrial Supply Chain (中国工业供应链与欧美平替智库)
- **定位**: 俄罗斯重工业与制造企业在欧美设备断供背景下的“中国工业替代品采购指南”。
- **涵盖核心品类**:
  * **工业传感器**: 4-20mA 两线制电流环压力变送器、PT100/PT1000 热电阻、光电与接近开关。
  * **工业自动化**: 国产 PLC (汇川/台达/信捷) 替代西门子 S7-200/300、Modbus RTU RS485 通信联调。
  * **变频驱动 (VFD)**: 矢量变频器参数设置、重载电机启动与过流保护排障。
  * **接头与流体五金**: M22 螺纹、G1/4 管螺纹、NPT 标准高压管路接头互换。

---

### 🧬 Hub D: China Medical & Regulatory Intelligence (中国医疗出海与全球监管合规智库)
- **定位**: 中国生物医药、体外诊断 (IVD) 与医疗器械企业出海欧美及欧亚经济联盟 (EAEU) 的法定合规指南。
- **涵盖核心板块**:
  * **FDA 监管合规**: 境外药企生产设施登记 (21 CFR 207)、FEI 编号申请、US Agent 授权任命、CDER Direct SPL XML 结构化标签传输。
  * **NMPA 医疗器械 UDI**: 2027 年 II 类/III 类医疗器械唯一标识强制实施时间线与数据库对接。
  * **分子 POCT 与实验室评估**: 全自动核酸 POCT 系统 (iFIND) 灵敏度 (LoD)、结核 (MTB/RIF) 与耐药基因试剂盒采购评估。
  * **EAC / Roszdravnadzor 俄罗斯医疗注册**: 欧亚经济联盟医疗器械注册证办理与本地临床试验。

---

## 3. URL 路由架构与分类设计 (URL Routing Architecture)

```text
================================================================================
MULTILINGUAL URL ROUTING SPECIFICATION
================================================================================
┌──────────────────────┬────────────────────────────────┬────────────────────────────────┐
│ Hub 模块             │ 英文路径 (English Route)       │ 俄文路径 (Russian Route)       │
├──────────────────────┼────────────────────────────────┼────────────────────────────────┤
│ 首页总览             │ https://fyzsxnb.com/           │ https://fyzsxnb.com/ru/        │
│ Hub A (汽车总库)     │ /cars-from-china/              │ /ru/cars-from-china/           │
│ Hub A 子车型专题     │ /cars-from-china/{make}/{model}/│ /ru/cars-from-china/{make}/{model}/ │
│ Hub A 工具专区       │ /tools/{tool-slug}/            │ /ru/tools/{tool-slug}/         │
│ Hub B (维修知识库)   │ /repair-knowledge-base/        │ /ru/repair-knowledge-base/     │
│ Hub B 故障排查专页   │ /repair/{system}/{failure-slug}/│ /ru/repair/{system}/{failure-slug}/ │
│ Hub C (工业供应链)   │ /industrial-supply-chain/      │ /ru/industrial-supply-chain/   │
│ Hub D (医疗合规)     │ /biomed-regulatory/            │ /ru/biomed-regulatory/         │
└──────────────────────┴────────────────────────────────┴────────────────────────────────┘
```

### WordPress 分类法与模板底层映射设计：
1. **Custom Post Type / Hierarchical Taxonomy**:
   - `fyz_vehicle` 分类法: 用于车型父子层级（`volkswagen` -> `tayron` / `tharu` / `golf`）。
   - `fyz_research_type` 分类法: 用于内容性质（`Buyer Guide`, `Failure Case`, `Policy Briefing`, `Regulatory Framework`）。
   - `fyz_industry_desk` 分类法: 用于四大 Pillar Hub 聚合。
2. **Page Templates**:
   - `page-templates/cars-from-china-hub.php`: 汽车生态聚合母页。
   - `page-templates/repair-knowledge-base.php`: 故障维修索引中台。
   - `page-templates/industrial-hub.php`: 工业元器件索引母页。
   - `page-templates/biomed-regulatory-hub.php`: 医疗合规决策母页。

---

## 4. 全站 97 篇文章全量 Hub 归属映射表 (97 Posts Mapping)

| 文章 ID | 语言 | 文章 Slug | 归属 Pillar Hub | 归属 Sub Hub | 页面角色 (Page Role) | 核心上下游链接关系 |
|:---:|:---:|:---|:---:|:---:|:---:|:---|
| **640** | RU | `volkswagen-tayron-from-china-overview` | **Hub A** | A1 (车型介绍) | **Pillar Anchor** | 链接至 484(验车), 432(报废税), 514(DQ381), 510(配件) |
| **484** | RU | `proverka-epts-po-vin-pered-pokupkoj` | **Hub A** | A2 (验车合规) | **Tool Anchor** | 链接至 640(车型), 432(报废税), 415(锁车防范) |
| **432** | RU | `utilization-fee-china-car-import-russia-2026` | **Hub A** | A2 (政策工具) | **Policy Anchor** | 链接至 640(探岳), 484(EPTS 验真) |
| **415** | RU | `kitayskiy-elektromobil-udalennaya-blokirovka-eksport-risk` | **Hub A** | A3 (新能源风险)| **Cluster Guide** | 链接至 504(智驾), 484(VIN 验真) |
| **504** | RU | `byd-frigate-07-openpilot-can-hardware-guide` | **Hub B** | ADAS & CAN | **Cluster Guide** | 链接至 415(电车风险), Hub B 知识库母页 |
| **514** | RU | `volkswagen-tayron-kitay-dq381-avariynyy-rezhim-realnye-sluchai`| **Hub B** | 变速箱 (DQ381) | **Failure Anchor** | 链接至 513(案例解析), 510(配件), 640(探岳母页) |
| **513** | EN | `china-market-volkswagen-tayron-dq381-emergency-mode-owner-cases`| **Hub B** | 变速箱 (DQ381) | **Technical Case** | 链接至 514(俄语版), 509(配件) |
| **510** | RU | `volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay` | **Hub A** | A5 (配件直采) | **Parts Anchor** | 链接至 640(探岳), 514(维修), 512(GPF) |
| **509** | EN | `china-market-volkswagen-tayron-330tsi-dkv-dpl-dth-parts` | **Hub A** | A5 (配件直采) | **Parts Anchor** | 链接至 510(俄语版), 513(DQ381) |
| **512** | RU | `volkswagen-tayron-330tsi-kitay-gpf-opyt-vladeltsev` | **Hub B** | 发动机 (GPF) | **Failure Case** | 链接至 510(配件), 640(探岳母页) |
| **511** | EN | `china-market-volkswagen-tayron-330tsi-gpf-owner-cases` | **Hub B** | 发动机 (GPF) | **Failure Case** | 链接至 512(俄语版), 509(配件) |
| **485** | RU | `chery-tiggo-firmware-update-adb-software-installation` | **Hub B** | 车机固件 (MCU) | **DIY Guide** | 链接至 484(验车), Hub B 知识库母页 |
| **420** | RU | `honor-iz-kitaya-v-rossii-proverka-pered-pokupkoy` | **Hub C/Tech**| 智能硬件 (3C) | **Product Anchor** | 链接至 372(频段实测), 448(拓竹 3D) |
| **372** | RU | `honor-china-vs-eu-version-russia-guide` | **Hub C/Tech**| 智能硬件 (3C) | **Product Guide** | 链接至 420(15 项检查) |
| **448** | RU | `bambu-lab-china-russia-pre-purchase-check` | **Hub C/Tech**| 3D 打印硬件 | **Product Anchor** | 链接至 398(AI 硬件), 420(数码出海) |
| **489** | RU | `pressure-transmitter-4-20ma-vfd-wiring-guide` | **Hub C** | 工业传感器 | **Industry Anchor**| 链接至 433(工业软管), Hub C 工业母页 |
| **433** | RU | `pressure-washer-hose-connector-compatibility-guide` | **Hub C** | 流体与五金 | **Industry Guide** | 链接至 489(变送器) |
| **466** | EN | `fda-foreign-drug-establishment-registration-guide` | **Hub D** | FDA 监管合规 | **Regulatory Anchor**| 链接至 479(UDI 2027), 431(POCT 采购) |
| **479** | EN | `nmpa-udi-2027-class-ii-medical-devices` | **Hub D** | NMPA UDI | **Regulatory Guide**| 链接至 466(FDA), 431(POCT 采购) |
| **431** | EN | `fully-automated-molecular-poct-system-ifind-procurement-guide` | **Hub D** | IVD & POCT | **Procure Anchor** | 链接至 437(TBR), 439(IFQ), 466(FDA) |
| **437** | EN | `ifind-tbr-mtb-rif-cartridge-procurement-guide` | **Hub D** | IVD & POCT | **Clinical Guide** | 链接至 431(仪器采购), 439(耐药盒) |
| **439** | EN | `ifind-ifq-inh-fluoroquinolone-resistance-cartridge-guide` | **Hub D** | IVD & POCT | **Clinical Guide** | 链接至 431(仪器采购), 437(TBR 盒) |
| **398** | EN | `ai-voice-recorder-buying-guide-subscription-privacy-offline` | **Hub C/Tech**| AI 硬件 | **Product Guide** | 链接至 448(拓竹), 420(手机) |
| **362** | EN | `best-budget-robot-vacuum-2026-reddit-guide` | **Hub C/Tech**| 家电硬件 | **Product Guide** | 链接至 448(拓竹) |
| **350** | RU | `kimi-k3-ru-open-model` | **Hub C/Tech**| AI 算法与软件 | **Tech Case** | 链接至 398(AI 硬件) |
| **424** | CN | `national-anti-fraud-center-ai-content-identification-guide` | **Hub C/Tech**| AI 安全与鉴伪 | **Tech Case** | 链接至 350(Kimi K3) |
| *239-361*| EN | *71 篇历史选品、宠物、家居、工艺品长尾归档文章* | **Archive** | 跨境选品长尾 | **Long-tail Spoke** | 统一归入 `/blog/` 历史长尾库 |

---

## 5. 内部网状链接策略与 Topic Authority 护城河 (Internal Linking Web)

```text
================================================================================
INTERNAL LINKING TRIANGLE ARCHITECTURE
================================================================================
                                ┌──────────────────────────────────────┐
                                │   PILLAR PAGE: /ru/cars-from-china/  │
                                └──────────────────┬───────────────────┘
                                                   │
                   ┌───────────────────────────────┴───────────────────────────────┐
                   ▼                                                               ▼
   ┌───────────────────────────────┐                               ┌───────────────────────────────┐
   │ SUB-HUB: 大众中规车平行进口   │                               │ SUB-HUB: 车辆验真与合规工具   │
   │ /cars-from-china/volkswagen/  │                               │ /cars-from-china/verification/│
   └───────────────┬───────────────┘                               └───────────────┬───────────────┘
                   │                                                               │
     ┌─────────────┴─────────────┐                                   ┌─────────────┴─────────────┐
     ▼                           ▼                                   ▼                           ▼
[Post 640: 探岳选车总览]   [Post 510: 探岳配件表]               [Post 484: EPTS 验真]    [Post 432: 报废税 2026]
     │                           │                                   │                           │
     ├───────────────────────────┼───────────────────────────────────┼───────────────────────────┤
     ▼                           ▼                                   ▼                           ▼
[Post 514: DQ381 变速箱维修] [Post 512: GPF 堵塞案例]          [Post 415: 远程锁车防范]  [СБКТС 清关指南]
================================================================================
```

### 内部链接执行铁律：
1. **三大核心枢纽节点 (Mega-Hub Anchors)**:
   - **车型锚点 (Post 640)**: 所有探岳相关的发动机 (Post 510)、变速箱 (Post 514)、颗粒捕捉器 (Post 512) 必须在第一段以超链接形式回溯到 Post 640。
   - **合规锚点 (Post 484)**: 所有涉及从中国提车、买车、关税的文章，必须在“购车避坑”章节嵌入 Post 484 (EPTS 验真) 与 Post 432 (报废税)。
   - **维保锚点 (Post 514)**: 所有二手车选购评测在提及 DQ381 变速箱时，强制锚定链接至 Post 514 维修指南。
2. **面包屑闭环**: 全站单篇文章顶部一律展示 3 级标准面包屑（`Главная › Авто из Китая › Volkswagen Tayron`）。
3. **底部相关推荐矩阵 (`fyz-related`)**: 严格限制推荐同属一个 Sub-hub 的精准关联文章，彻底杜绝把“宠物粮”推荐给“大众探岳车主”的错位现象。

---

## 6. 首页四大 Hub 战略展示方案 (Homepage Gateway Design)

为了将网站性质从“时间流博客”彻底进化为“决策智库”，首页中段的 `fyz-desks` 升级为 **四大战略 Pillar Hub 黄金导航区**：

```text
================================================================================
HOMEPAGE PILLAR GATEWAYS (首页四大智库入口设计)
================================================================================
┌────────────────────────────────────────┬────────────────────────────────────────┐
│ [HUB A: 俄罗斯中国汽车总库]            │ [HUB B: 汽车故障与维修知识库]          │
│ 🚗 Cars From China in Russia           │ 🔧 Auto Repair Knowledge Base          │
│ - 新车/二手车中国平行进口导购          │ - DQ381 双离合阀体与传感器维修         │
│ - 17位 VIN 解码与 EPTS 电子护照核验    │ - EA888 2.0T (DKV/DPL) 原厂配件库      │
│ - 2026 俄罗斯报废税与清关合规          │ - GPF 堵塞再生与 Chery 车机刷机        │
│ [ 进入汽车生态总库 → ]                 │ [ 进入维修数据库 → ]                   │
├────────────────────────────────────────┼────────────────────────────────────────┤
│ [HUB C: 中国工业供应链与欧美平替]      │ [HUB D: 生物医药与全球监管合规]        │
│ ⚙️ Industrial Automation & Parts       │ 🧬 Biomed & Regulatory Intelligence    │
│ - 4-20mA 压力/温度变送器接线与选型     │ - FDA 境外药企 21 CFR 207 登记指南     │
│ - 欧美停产工控件中国原厂平替指南       │ - NMPA 医疗器械 UDI 2027 强制时间线    │
│ - 工业管路与 M22/G1/4 螺纹互换对照     │ - 全自动分子 POCT 试剂盒采购评估       │
│ [ 进入工业供应链智库 → ]               │ [ 进入医疗合规平台 → ]                 │
└────────────────────────────────────────┴────────────────────────────────────────┘
```

---

## 7. 未来 100 篇高价值内容排产与 Hub 承载矩阵 (Future 100 Content Pipeline)

```text
================================================================================
FUTURE 100 POSTS DISTRIBUTION ACROSS 4 HUBS
================================================================================
┌──────────────────────────────────────┬──────────┬────────────────────────────────────┐
│ 所属 Pillar Hub                      │ 规划配额 │ 重点规划车系 / 设备 / 法规专题     │
├──────────────────────────────────────┼──────────┼────────────────────────────────────┤
│ Hub A: 俄罗斯中国汽车生态总库        │ 40 篇    │ 丰田凯美瑞/威兰达中规车、吉利      │
│                                      │          │ Monjaro、坦克 300/500、极氪 001、   │
│                                      │          │ 理想 L7/L8、二手车拍卖直采流程     │
├──────────────────────────────────────┼──────────┼────────────────────────────────────┤
│ Hub B: 汽车故障与配件维修数据库      │ 25 篇    │ 8AT 爱信/长城变速箱维修、增程器    │
│                                      │          │ 低温保养、CAN-FD 逆向、TCU 固件    │
├──────────────────────────────────────┼──────────┼────────────────────────────────────┤
│ Hub C: 中国工业供应链与平替智库      │ 18 篇    │ 国产 PLC/VFD 替代西门子、高精度    │
│                                      │          │ 流量计选型、工业快插接头标准       │
├──────────────────────────────────────┼──────────┼────────────────────────────────────┤
│ Hub D: 生物医药与合规决策智库        │ 17 篇    │ 欧亚联盟 EAC 医疗注册、体外诊断    │
│                                      │          │ 冷链跨境规范、临床试验数据解读     │
└──────────────────────────────────────┴──────────┴────────────────────────────────────┘
```

---

## 8. 最终交付状态

```text
HUB_ARCHITECTURE_COMPLETE

DELIVERABLE_DOCUMENTS:
1. docs/FYZSXNB-HUB-ARCHITECTURE-001.md (本官方信息架构规范)
2. docs/CONTENT-GROWTH-PRIORITY-001.md (全站内容增长优先级)
3. docs/FYZSXNB-VISUAL-GUIDELINE-001.md (官方视觉规范法典)

CORE_PILLARS_FROZEN:
- Hub A: Cars From China in Russia (Pillar & Sub-hubs Active)
- Hub B: China Auto Repair Knowledge Base (Failure-to-Parts Active)
- Hub C: China Industrial Supply Chain (Sensors & VFD Active)
- Hub D: China Medical & Regulatory Intelligence (FDA & UDI Active)

STATUS:
INFORMATION ARCHITECTURE FROZEN (Ready for theme taxonomy rollout & Batch 2-B execution)

STOP
```
