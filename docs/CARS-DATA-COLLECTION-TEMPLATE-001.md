# FYZSXNB — 车型事实底座数据采集标准规范模板体系 (Vehicle Fact Pack V1)

**文档编号:** `FYZ-DOC-20260821-CARS-DATA-COLLECTION-TEMPLATE-001`  
**任务编号:** `FYZSXNB-CARS-DATA-COLLECTION-TEMPLATE-001`  
**执行角色:** Google Gemini Flash 3.7  
**前置基线:** `docs/CARS-ARCHITECTURE-AUDIT-001.md` | `docs/CARS-INTELLIGENCE-STRATEGY-V2-001.md` | `docs/CARS-TRAFFIC-STRATEGY-001.md` | `docs/CARS-CONTENT-FACTORY-001.md`  
**阶段定位:** `DATA SYSTEM DESIGN ONLY (数据采集与输入契约标准设计，零代码/数据库改动)`  

---

## 1. 核心定位与数据契约铁律 (Core Principles)

在 FYZSXNB Cars From China 的内容生产工厂中，**《车型事实底座 (Vehicle Fact Pack)》是所有文章生成的唯一合法数据输入源**。

```text
  [数据采集与清洗] ──► 填写完整的《车型事实底座 (Vehicle Fact Pack)》
           │
           ▼
  [人工审核冻结] ──► 事实核查员复核所有三大件代号、证据级别与信源有效性
           │
           ▼
  [标准化文章 Brief] ──► 提取 Fact Pack 字段生成 6 大标准文章 Prompt / Brief
           │
           ▼
  [AI 辅助受限起草] ──► AI 严格基于 Fact Pack 事实撰写，严禁脱离底座发散虚构
```

### 1.1 核心数据铁律 (Data Invariants)
1. **严禁无底座生成 (No Prompt Without Fact Pack)**：严禁直接向 AI 输入模糊的“写一篇大众探岳通病”指令，必须挂载经过审核的 Fact Pack；
2. **三级证据链锁定 (Mandatory Evidence Attribution)**：所有故障、改装、零件互换与口碑必须绑定唯一的 `Source ID`；
3. **技术代号绝对精准 (Precision Code Standard)**：发动机（如 `DKV`, `9NR-FTS`）、变速箱（如 `0GC DQ381`, `Aisin 8AT`）、底盘平台代码必须精确到具体代次。

---

## 2. 车型基础数据模板 (Vehicle Basic Information Sheet)

```markdown
### 模块 1：车型基础参数 (Vehicle Basic Info)

| 基础属性字段 | 标准取值范例 (以 Volkswagen Tayron 为例) | 必填要求与填写规范 |
|:---|:---|:---|
| **品牌 (Brand)** | Volkswagen | 英文全称 (注册在 `fyz_vehicle` 的父项 Slug) |
| **车型 (Model)** | Tayron | 英文全称 (注册在 `fyz_vehicle` 的子项 Slug) |
| **中国市场名称** | 一汽-大众 探岳 | 主机厂 + 中文商业全称 |
| **俄罗斯流通名称** | Фольксваген Тайрон | 俄语标准拼写与本土常用译名 |
| **车型类别 (Body Type)**| 中型 SUV (Crossover SUV) | SUV / Sedan / MPV / EV / Hybrid / Off-Road |
| **中国生产主机厂** | 一汽-大众汽车有限公司 (FAW-VW) | 生产制造独立合资厂或自主品牌主机厂 |
| **中国生产基地** | 天津工厂 (华北基地) / 长春工厂 | 具体总装制造城市与工厂代码 |
| **在产年份 / 代际** | 2018 - 2024 (第一代 / 第一代中期改款) | 明确当前研究针对的具体年款跨度 |
| **底盘平台代码** | MQB A2 (MQB Evo 衍生平台) | 明确大众/丰田/吉利等底层平台代号 |
| **长宽高 / 轴距** | 4592 × 1860 × 1665 mm / 2731 mm | 毫米 (mm)，标明是否为中国加长轴距 (L/Li)|

#### 动力总成与三大件核心参数 (Powertrain Specifications)
| 动力组件 | 参数与官方代号 | 详细规格说明 |
|:---|:---|:---|
| **主打发动机 1** | **2.0 TSI (330TSI)** | • 代号：`DKV` (早期国六B) / `DPL` (改款低功) / `DTH`<br>• 排量：1984 cc (EA888 Gen3B)<br>• 最大功率：137 kW (186 PS) @ 4100-6000 rpm<br>• 最大扭矩：320 N·m @ 1500-4000 rpm<br>• 供油与排放：350bar 直喷 + 集成缸盖 + 国六B GPF |
| **主打发动机 2** | **2.0 TSI (380TSI)** | • 代号：`DKX` / `DTJ` (高功率)<br>• 功率/扭矩：162 kW (220 PS) / 350 N·m (EA888 Gen3 高功) |
| **主打发动机 3** | **1.4T / 1.5T** | • 代号：`DJS` (1.4T EA211) / `DSV` (1.5T EA211 Evo2)<br>• 功率/扭矩：110 kW (150 PS) / 250 N·m |
| **变速箱型号** | **DQ381 (0GC)** | • 供应商：大众自动变速器(天津)有限公司 (VWATJ)<br>• 类型：7速湿式双离合 (Wet 7-speed DSG)<br>• 最大承受扭矩：420 N·m |
| **驱动形式** | 前驱 (FWD) / 4MOTION 全时四驱 (AWD)| 四驱采用博格华纳第 5 代电控多片离合器中央差速器 |
| **悬架形式** | 前麦弗逊独立 / 后多连杆独立 | 标配铸铁摆臂与前副车架结构 |
```

---

## 3. 中国本土市场数据模板 (China Market Profile)

```markdown
### 模块 2：中国本土市场画像 (China Market Profile)

| 市场维度 | 调研数据与画像输出 | 数据采信来源 (Source ID) |
|:---|:---|:---|
| **本土销量规模** | 年均销量 10万~15万台，细分市场长期位列前 5 | `SRC-TAYRON-CN-001` (懂车帝销量榜) |
| **主销配置版本** | 330TSI 两驱豪华 Plus 进阶版 / 380TSI 四驱 R-Line | `SRC-TAYRON-CN-002` (汽车之家配置销量分析) |
| **本土新车指导价** | 20.79万 - 25.99万 RMB (终端优惠 4~6万 RMB) | `SRC-TAYRON-CN-003` (易车全国均价) |
| **核心买家画像** | 30-45 岁家庭用户，看重空间、EA888 动力与德系底盘质感 | `SRC-TAYRON-CN-004` (车主调研问卷) |
| **用户核心买点** | 1. 2.0T+湿式双离合动力充沛；2. 后排空间大；3. 品牌认可度高 | `SRC-TAYRON-CN-005` (汽车之家口碑库) |
| **车主主要槽点** | 1. 颗粒捕捉器易堵塞 (330TSI)；2. 车机大屏偶发黑屏；3. 悬挂偏硬 | `SRC-TAYRON-CN-006` (车质网统计年报) |
| **主要竞争车型** | 上汽大众途观L (Tiguan L)、别克昂科威Plus、丰田威兰达 | `SRC-TAYRON-CN-007` (竞品对比白皮书) |
```

---

## 4. 俄罗斯市场画像数据模板 (Russia Market Profile)

```markdown
### 模块 3：俄罗斯市场流通与环境适应性 (Russia Market Profile)

| 俄市场流通维度 | 调研事实与实测数据 | 采信来源与本地验证 (Source ID) |
|:---|:---|:---|
| **进入俄罗斯方式** | 1. 0公里准新车平行进口 (中俄口岸陆运)；2. 个人自用二手进口 | `SRC-TAYRON-RU-001` (Auto.ru 挂牌数据) |
| **俄罗斯市场存量** | 2026 年平行进口德系 SUV 单月进口量 No. 1 (~842 台/月) | `SRC-TAYRON-RU-002` (AUTOSTAT 统计) |
| **终端市场售价** | 3,100,000 - 4,200,000 卢布 (约合 24万-32万 RMB 落地) | `SRC-TAYRON-RU-003` (Drom.ru 实时均价) |
| **税费与通关成本** | 报废税 (Утильсбор) 2026 标准税率 + 15% 关税 + 20% 增值税 | `SRC-TAYRON-RU-004` (俄联邦海关税则) |
| **俄买家核心动机** | 1. 传统欧版途观断供后的唯一平替；2. 2.0T+DQ381 机械素养过硬 | `SRC-TAYRON-RU-005` (Drive2 车主提车帖) |
| **俄买家核心疑虑** | 1. 中规车机无俄语且锁 4G eSIM；2. 无原厂冬季包；3. 零配件缺货 | `SRC-TAYRON-RU-006` (VK 大众车友圈反馈) |
| **极寒气候适应性** | 严寒 (-30℃) 下 GPF 再生困难；中规机油 0W-20 冷启动表现优异 | `SRC-TAYRON-RU-007` (西伯利亚长测专栏) |
| **零件供应痛点** | DKV/DPL 气门室盖垫、原装 GPF 传感器在欧版 ETKA 查无此号 | `SRC-TAYRON-RU-008` (莫斯科汽修厂访谈) |
```

---

## 5. 真实车主口碑与证据库模板 (Owner Evidence Database)

```markdown
### 模块 4：中俄真实车主口碑事实库 (Owner Evidence Database)

#### 4.1 中国车主事实库 (China Owner Evidence Pool)
- **案例 ID**: `CASE-TAYRON-CN-001`
  * **信源与链接**: 汽车之家口碑帖 (`SRC-TAYRON-CN-008`)
  * **车辆信息**: 2021款 330TSI 两驱豪华智联版 (发动机: DKV, 里程: 68,000 km)
  * **车主评价**: “动力输出顺畅，高速超车底盘扎实。但在市区短途代步油耗偏高，且冬季出现过一次颗粒捕捉器堵塞提示，跑了 30 公里高速后自动消除。”
  * **维修经历**: 50,000 km 更换前刹车片，60,000 km 重力更换 DQ381 变速箱油，无其他大修。

#### 4.2 俄罗斯车主事实库 (Russia Owner Evidence Pool)
- **案例 ID**: `CASE-TAYRON-RU-001`
  * **信源与链接**: Drive2.ru 真实用车日志 (`SRC-TAYRON-RU-009`)
  * **车辆信息**: 2023款 中规探岳 330TSI (进口至新西伯利亚, 里程: 24,000 km)
  * **车主评价**: “车子比老途观宽敞很多，-28℃ 冷启动一次点火成功。唯一头疼的是没有方向盘加热和挡风玻璃电加热，后加装了俄罗斯第三方的座椅加热垫并刷了俄语车机固件。”
  * **改装与维修**: 更换俄标防冻液 (冰点 -45℃)，加装底盘金属护板与空腔防锈蜡。
```

---

## 6. 故障情报与通病数据模板 (Problem Intelligence Sheet)

严禁在没有事实证据的情况下使用“全系通病”字眼，每个问题必须完整填写本表：

```markdown
### 模块 5：故障与技术缺陷情报表 (Problem Intelligence Sheet)

| 故障项目 | 故障 1：DQ381 双离合进入紧急模式 | 故障 2：330TSI 颗粒捕捉器 (GPF) 堵塞 |
|:---|:---|:---|
| **问题名称** | DQ381 (0GC) 机电单元电磁阀卡滞报故障 | 330TSI (DKV/DPL) 市区短途 GPF 4级堵塞 |
| **涉及发动机/代号** | 2.0 TSI (330TSI / 380TSI 全系) | 2.0 TSI (DKV 国六B 早期版本最明显) |
| **涉及变速箱型号** | DQ381 (0GC) 7速湿式双离合 | DQ381 (与变速箱无关，系排气系统问题) |
| **涉及车型年份** | 2019 - 2022 款车型 | 2020 - 2022 款 330TSI 车型 |
| **典型故障症状** | 仪表提示“变速箱处于紧急运行模式，无法挂入倒挡/偶数挡” | 仪表黄色排气故障灯常亮，自动启停失效，油耗增加 2~4L/100km |
| **发生工况与里程** | 40,000 - 80,000 km，多发于长期拥堵起步及高温重载工况 | 10,000 - 30,000 km，多发于北方冬季长期短途代步 (<5km) |
| **技术确诊原因** | 机电控制单元内部微铁屑吸附在油路电磁阀上，导致阀体卡滞 | 尾气温度未达 600℃，排气管微粒无法氧化燃烧，累积达临界值 |
| **解决方案与备件** | 1. 清洗电磁阀并更换改进型机电单元油滤 (`0GC 325 429 H`)；<br>2. 重新加注原厂 G 055 529 A2 变速箱油并执行基础设定 | 1. 定期以 80 km/h 巡航行驶滑行（强制被动再生）；<br>2. 4S店升级发动机 ECU 喷油点火程序；<br>3. 更换改进型排气压差传感器 |
| **证据等级** | **Level 1 [Confirmed]** | **Level 1 [Confirmed]** |
| **采信信源链接** | 车质网投诉汇总 `[1]` + 官方 TSB 公告 `[2]` | 一汽-大众官方 ECU 升级公告 `[3]` + 懂车帝长测 `[4]` |
```

---

## 7. 关键词数据库模板 (Keyword Matrix)

```markdown
### 模块 6：俄语关键词矩阵体系 (Keyword Matrix)

| 关键词类别 | 俄语目标关键词 (Target Russian Keyword) | 中文对应意图 | 搜索意图 (Intent) | 搜索热度与优先级 |
|:---|:---|:---|:---|:---:|
| **车型主词** | `Volkswagen Tayron`, `Фольксваген Тайрон` | 车型认知 | Informational | **High** |
| **市场在售** | `Volkswagen Tayron Россия`, `Тайрон из Китая` | 俄在售状态 | Informational / Comm | **High** |
| **二手交易** | `Volkswagen Tayron бу`, `Тайрон с пробегом купить` | 二手购买 | Transactional | **High** |
| **车主口碑** | `Volkswagen Tayron отзывы`, `отзывы владельцев` | 真实口碑 | Commercial Invest | **High** |
| **通病故障** | `Volkswagen Tayron проблемы`, `что ломается в Тайрон`| 故障长尾 | Informational / Tech | **High** |
| **变速箱通病** | `ремонт коробки DQ381 Tayron`, `аварийный режим DSG` | 专业维修 | Tech / Commercial | **Medium** |
| **排气故障** | `забился сажевый фильтр Tayron 330tsi GPF` | 专业故障 | Tech / Problem | **Medium** |
| **维护保养** | `Volkswagen Tayron ТО регламент`, `масло в двигатель` | 保养耗材 | Informational | **Medium** |
| **版本对比** | `Volkswagen Tayron отличия от Tiguan`, `китайская версия`| 差异对比 | Commercial Invest | **High** |
| **配件采购** | `запчасти Фольксваген Тайрон из Китая каталог` | 配件采办 | Transactional (P2) | **Medium** |
```

---

## 8. 标准文章 Brief 生成模板 (Article Brief Template)

在调用 AI 撰写具体文章前，必须先将 Fact Pack 的数据导出为标准的《文章任务 Brief》：

```markdown
### 模块 7：标准文章生成 Brief 示例 (Article Brief: Tayron Common Problems)

- **任务 ID**: `BRIEF-TAYRON-ART-005`
- **目标文章类型**: `Article 5: Common Problems (fyz_research_type = common-problems)`
- **目标语言**: `Russian (ru)`
- **目标核心词**: `Volkswagen Tayron проблемы`, `неисправности DQ381`, `сажевый фильтр GPF 330TSI`
- **核心必须包含事实 (Must Include)**:
  1. 准确列出 330TSI 发动机代号 (`DKV`, `DPL`) 与 DQ381 (0GC) 7速变速箱；
  2. 详细解析 DQ381 紧急模式电磁阀故障（发生里程、机理、清洗与更换原厂机滤 `0GC 325 429 H`）；
  3. 详细解析 330TSI 极寒短途颗粒捕捉器 (GPF) 堵塞及 ECU 升级与滑行再生解决办法；
  4. 故障证据等级必须清晰标注为 `Level 1 Confirmed`。
- **严格禁止包含内容 (Forbidden / Negative Constraints)**:
  1. 严禁声称“探岳所有批次变速箱 100% 必然报废”（夸大个案）；
  2. 严禁捏造未经证实的召回谣言；
  3. 严禁出现未经 Fact Pack 收录的虚构零部件号。
- **引用信源清单 (Source References)**:
  - `[1]` 车质网大众 DQ381 投诉统计数据 (`SRC-TAYRON-CN-006`)
  - `[2]` 一汽-大众官方售后 TSB 技术服务公告 (`SRC-TAYRON-CN-009`)
  - `[3]` Drive2 莫斯科车主变速箱清洗案例日志 (`SRC-TAYRON-RU-009`)
```

---

## 9. 统一信源注册表 (Source Registry Template)

每一个被引用的链接与数据，必须在注册表获得唯一编号：

```markdown
### 模块 8：信源档案登记簿 (Source Registry)

| 信源 ID | 来源平台 / 权威机构 | 原始页面 URL | 所属国家 | 语言 | 记录日期 | 核查用途与证据支持内容 |
|:---|:---|:---|:---:|:---:|:---:|:---|
| `SRC-TAY-CN-001` | 汽车之家 (Autohome) | `https://www.autohome.com.cn/4857/` | 中国 | 中文 | 2026-08 | 探岳配置参数、官方指导价与车主口碑 |
| `SRC-TAY-CN-006` | 车质网 (12365auto) | `https://m.12365auto.com/series/2391/` | 中国 | 中文 | 2026-08 | 探岳颗粒捕捉器堵塞与变速箱电磁阀投诉台账 |
| `SRC-TAY-RU-001` | Auto.ru | `https://auto.ru/cars/volkswagen/tayron/` | 俄罗斯 | 俄文 | 2026-08 | 莫斯科中规平行进口准新车在售价格与配置 |
| `SRC-TAY-RU-009` | Drive2.ru | `https://www.drive2.ru/r/volkswagen/tayron/...` | 俄罗斯 | 俄文 | 2026-08 | 俄罗斯车友探岳刷俄语系统及冬季用车日志 |
| `SRC-TAY-DE-001` | Volkswagen AG (ETKA) | 官方电子零件目录数据库 | 德国/全球| 德/英 | 2026-08 | 欧版途观与中规探岳底盘件及密封垫零件号比对 |
```

---

## 10. 发布前人工审核表 (Publishing Review Sheet)

责任编辑在执行最终发布前，必须在下方审核单逐一签字确认：

```markdown
### 模块 9：发布前审查验收单 (Publishing Review Sign-Off)

- **车型与文章**: Volkswagen Tayron / Article 5 (Common Problems)
- **审校责任人**: Editorial Lead
- **审核日期**: 2026-08-21

| 审查维度 | 严格核查标准 | 审核状态 | 责任人备注 |
|:---|:---|:---:|:---|
| **1. 参数代码真实性** | 发动机代号 (`DKV/DPL`)、变速箱代码 (`DQ381`) 与官方 EPC 100% 一致 | [x] PASS | 已核对主机厂铭牌 |
| **2. 故障等级与分级** | 明确标注 `Level 1 Confirmed`，无主观臆测与个案泛化 | [x] PASS | 车质网与TSB双重支持 |
| **3. 证据链条可追溯** | 文内所有 `[N]` 均在文末 `REF_URLS` 对应有效且活跃的信源链接 | [x] PASS | 4条链接均200可达 |
| **4. 俄语本土化术语** | 专业术语（如 `сажевый фильтр`, `гидроблок`, `утильсбор`）纯正地道 | [x] PASS | 符合 Drive2 行业表达 |
| **5. 关键词与 SEO** | 自然融入 3~4 个长尾词，密度 2.1%，单 H1，Canonical 规范自指向 | [x] PASS | 符合 SEO 规范 |
| **6. 多语言与 Resolver**| Category 包含 `54`，`_fyz_content_language` 设为 `ru`，Resolver V2 正常 | [x] PASS | 0.4.5 契约合规 |
| **7. 零死链与区块填充** | 对应 Model Hub 聚合页正常调用该文章卡片，无空白幽灵区 | [x] PASS | 聚合渲染测试通过 |
```

---

## 11. 交付结论与状态汇报

已生成完整的车型事实底座数据采集标准规范模板体系：  
[`docs/CARS-DATA-COLLECTION-TEMPLATE-001.md`](file:///C:/Users/Administrator/Documents/Codex/2026-07-10/w/work/fyzsxnb-ui-v2/docs/CARS-DATA-COLLECTION-TEMPLATE-001.md)

```text
TEMPLATE_SYSTEM_STATUS:
COMPLETE

DATA_CONTRACT:
Vehicle Fact Pack (9 Comprehensive Modules Covering Specs, Market, Evidence, Problems, Keywords, Briefs, Sources, Review)

EVIDENCE_LEVELS:
Level 1 Confirmed / Level 2 Reported / Level 3 Unverified (100% Attributed)

PIPELINE_ROLE:
Sole Upstream Fact Source for Content Factory (No Hallucinations Permitted)

PRODUCTION_CHANGE:
NONE (Template System Design Only)

STOP
```
