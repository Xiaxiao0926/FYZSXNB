# FYZSXNB — 俄罗斯市场中国汽车全生命周期情报数据库战略规划 (Strategy V2)

**文档编号:** `FYZ-DOC-20260821-CARS-STRATEGY-V2-001`  
**任务编号:** `FYZSXNB-CARS-STRATEGY-V2-001`  
**执行角色:** Google Gemini Flash 3.7  
**前置基线:** `docs/CARS-ARCHITECTURE-AUDIT-001.md` | `UI V2 0.3.6+` | `0.4.5 Resolver V2`  
**阶段定位:** `STRATEGY ONLY (纯战略规划，零代码/数据库改动)`  

---

## 1. 战略定位与核心逻辑 (Strategic Positioning)

### 1.1 我们是什么，不是什么
- **不是什么**：FYZSXNB Cars From China 绝不是泛汽车新闻资讯站，不是新车发布会快讯搬运工，也不是机械罗列参数的汽车百科。
- **是什么**：**面向俄罗斯及欧亚市场的“中国汽车全生命周期商业与技术情报数据库” (Russia-Market China Vehicle Full-Lifecycle Intelligence Database)**。

### 1.2 商业转化闭环逻辑 (Value Chain)
```text
+----------------------------------------------------------------------------------------------------+
|                                    全生命周期情报与商业变现闭环                                      |
+----------------------------------------------------------------------------------------------------+
  [上游：中国汽车供应链] ──► 一汽/上汽/东风/自主主机厂、零部件配套厂、二手车平行出口商
          │
          ▼
  [中游：俄罗斯流通市场] ──► 0公里准新车平行进口、正规大贸渠道、二手车过户、ЭПТС/报废税(Утильсбор)
          │
          ▼
  [终端：真实车况与故障] ──► 极寒气候适应性、车机语言锁、颗粒捕捉器(GPF)、双离合(DSG)、三电系统故障
          │
          ▼
  [痛点：维修与配件盲区] ──► 俄罗斯当地无原厂维保资料、俄版与中版零件号错位、订货周期长、假货混杂
          │
          ▼
  [变现：中国供应链方案] ──► 提供精准 OE 零件号、原厂备件/高品质副厂件直发、维修技术手册、B2B 配件代采
```

---

## 2. 四大研究对象分类体系 (Vehicle Categories)

```text
+----------------------------------------------------------------------------------------------------+
|                                      四大核心资产分类架构                                           |
+--------------------------------+-------------------------------------------------------------------+
| 分类类别                       | 涵盖范围与核心攻坚方向                                             |
+--------------------------------+-------------------------------------------------------------------+
| **Category A: 中国自主品牌汽车**| BYD, Chery, Haval, Geely, Tank, Exeed, Zeekr, Li Auto, AITO 等    |
| (Chinese Domestic Brands)      | 重点攻坚：俄罗斯保有量第一梯队的故障通病、三电维保、车机汉化与配件 |
+--------------------------------+-------------------------------------------------------------------+
| **Category B: 中国出口二手车** | 0公里二手车 (平行出口准新车)、库存车出口、新能源二手车流通         |
| (China Export Used Cars)       | 重点攻坚：车况验车避坑、VIN/ЭПТС查验、真实折旧残值、极寒性能衰减   |
+--------------------------------+-------------------------------------------------------------------+
| **Category C: 中国渠道全球品牌**| Toyota, VW, BMW, Audi, Honda, Nissan, Mercedes-Benz, Mazda 等     |
| (Global Brands via China)      | **核心护城河**：中国版特供车型差异、发动机代号对应、跨市场配件互换 |
+--------------------------------+-------------------------------------------------------------------+
| **Category D: 维修与配件数据库**| 车型 ──► 发动机/变速箱 ──► 故障案例 ──► OE 零件号 ──► 供应方案     |
| (Parts & Solutions Catalog)    | **最终变现出口**：从用户故障搜索自然沉淀为高意向询盘与采购线索     |
+--------------------------------+-------------------------------------------------------------------+
```

### 2.1 Category A: 中国自主品牌汽车 (Chinese Domestic Brands)
- **市场现实**：欧美品牌退出后，中国品牌在俄新车市占率已超 60%。哈弗（Haval）、奇瑞（Chery）、吉利（Geely）、长安（Changan）已成为俄罗斯主流家庭车；极氪（Zeekr）、理想（Li Auto）、问界（AITO）成为高端新能源进口主力。
- **研究重点**：
  1. **俄罗斯在售车型真实表现**：官方引进版与中规平行进口版在配置、防锈、加热包（Winter Package）上的差异；
  2. **典型故障与投诉**：车机黑屏、俄语系统适配、传感器失灵、底盘悬挂在极寒路况下的耐久性；
  3. **中国供应链方案**：提供原厂升级模块、原装传感器、底盘加强件与车机刷机固件指引。

### 2.2 Category B: 中国出口二手车 (China Export Used Cars)
- **市场现实**：中国二手车出口到俄罗斯呈现结构性分化：一类是“0公里准新车”（以二手车名义出口的新车规避大贸壁垒），另一类是 2~5 年车龄的性价比燃油车及新能源车。
- **研究重点**：
  1. **俄罗斯买家决策心理**：为何买中国二手车（价格优势 vs 动力配置优势 vs 交付速度）；
  2. **核心痛点与风险**：退役营运车伪装个人二手车、里程表篡改、电池健康度（SOH）衰减、国内原车主云端账号未解绑导致远程锁车；
  3. **合规与查验指南**：中国第三方验车报告解读、海关报废税（Утильсбор）计算模型、ЭПТС 上牌合规路径。

### 2.3 Category C: 中国渠道进入俄罗斯的全球品牌车辆 (Global Brands via China — 核心差异化)
- **市场现实**：**这是 FYZSXNB 最具壁垒的核心切入点**。俄罗斯消费者对大众、丰田、宝马、奥迪等传统德日系车依然有巨大惯性，但欧美日断供后，一汽-大众（探岳 Tayron、途岳 Tharu）、广汽/一汽丰田（卡罗拉 Corolla、锋兰达 Frontlander、RAV4 荣放）、华晨宝马（X1、X3）、一汽奥迪（Q3、A3）成为俄罗斯获取德日系车最主要的合法进口通道。
- **研究重点**：
  1. **中国版与欧版/俄版差异**：如一汽-大众探岳（Tayron）搭载的中国特供 DKV/DPL/DTH 发动机与欧版 EA888 Gen3B 的细微传感器与管路差异；中国版颗粒捕捉器（GPF）布局差异；
  2. **维修与配件盲区**：俄罗斯修理厂拿着欧版 ETKA / 丰田 EPC 软件查不到中规车零件号，导致订错配件或维修卡死；
  3. **供应链变现机会**：提供中国特规 OE 零件号对照表，为俄罗斯独立汽修厂（Auto Services）提供中国直发配件服务。

### 2.4 Category D: 维修和配件数据库 (Parts & Solutions Catalog — 商业闭环)
- **终极商业逻辑**：
  $$\text{Model (车型)} \longrightarrow \text{Powertrain (动力总成)} \longrightarrow \text{Failure Mode (故障表现)} \longrightarrow \text{OE Part No (零件号)} \longrightarrow \text{China Supply (中国采购)}$$
- 每一篇故障分析文章末尾，天然植入“中国原厂/优质品牌件供应链直达通道”（Contact / Procurement RFQ），实现高意向技术流量向汽配外贸商机的无缝转化。

---

## 3. Evidence-First 数据来源与采信体系 (Data Source Framework)

严禁凭空捏造故障，所有文章必须建立在“双边信源交叉验证（Cross-Market Evidence）”的基础之上：

```text
                 [中国源头信源 (China Desk)]                           [俄罗斯终端信源 (Russia Desk)]
       +---------------------------------------------+       +---------------------------------------------+
       | • 汽车之家 (Autohome): 车主长期口碑与论坛帖子 |       | • Drom.ru: 二手车挂牌行情、车主真实评价     |
       | • 懂车帝 (Dongchedi): 拆解评测、实测争议与数据 | ◄───► | • Auto.ru: 交易价格、平行进口准新车配置对比  |
       | • 车质网 (12365auto): 权威车主缺陷与投诉台账  |       | • Drive2.ru: 车主维修日记、俄化刷机、改件实测|
       | • 主机厂 TSB / 服务公告 / 电子零件目录 (EPC)   |       | • VK / Telegram 俄罗斯车主群组反馈          |
       +---------------------------------------------+       +---------------------------------------------+
                                              \                     /
                                               \                   /
                                                ▼                 ▼
                                      +-------------------------------------+
                                      |   FYZSXNB 证据分级审查 (Case Gate)   |
                                      |   CASE ──► REPEATED ──► PATTERN     |
                                      +-------------------------------------+
```

### 3.1 信源准入标准
1. **CASE (单案例)**：来自 Drive2.ru 或汽车之家的单篇实名维修改装记录，包含清晰照片与故障描述 $\to$ 仅作为个体案例引用；
2. **REPEATED ISSUE (多发问题)**：在车质网投诉榜或 Drive2 车友圈出现 $\ge 3$ 起相同发动机/变速箱在特定里程下的故障 $\to$ 纳入候选缺陷清单；
3. **PATTERN (通病与设计规律)**：多起案例 + 确认的零件号迭代/官方维修技术公告 (TSB) / 主机厂改进方案 $\to$ 升级为核心通病指南（Common Problems）。

---

## 4. 车型优先级矩阵 (Cars Priority Matrix V1 — 50 车型池)

按照**“40% 中国渠道全球品牌 + 40% 中国自主品牌 + 20% 新能源与新势力”**的商业价值模型，规划首期 50 款战略车型池：

### 4.1 第一梯队：中国渠道全球品牌 (40% — 20 款车型)

| # | 品牌 | 车型 (Model) | 车辆类型 | 中国生产主机厂 | 俄罗斯流通形式 | 二手残值 | 维修价值 | 配件机会 | SEO价值 | 优先级 |
|:---:|:---|:---|:---:|:---|:---|:---:|:---:|:---:|:---:|:---:|
| 1 | **Volkswagen** | **Tayron (探岳)** | 燃油/中型SUV | 一汽-大众 | 平行进口主流 | 极高 | 极高 | 极高 (DQ381/GPF) | 极高 | **Tier 1 (已上线)** |
| 2 | **Toyota** | **Corolla (卡罗拉/雷凌)**| 燃油/紧凑轿车| 一汽/广汽丰田 | 平行进口销量#1 | 极高 | 极高 | 极高 (底盘/发动机)| 极高 | **Tier 1 (待发)** |
| 3 | **Audi** | **Q3 (奥迪Q3)** | 豪华/紧凑SUV | 一汽奥迪 | 平行进口准新车 | 高 | 极高 | 极高 (EA888/车身) | 极高 | **Tier 1 (待发)** |
| 4 | **Toyota** | **RAV4 / Wildlander** | 燃油/混动SUV | 一汽/广汽丰田 | 二手/平行进口大热| 极高 | 极高 | 极高 (四驱/三电)  | 极高 | **Tier 1** |
| 5 | **Toyota** | **Camry (凯美瑞)** | 燃油/中型轿车 | 广汽丰田 | 俄罗斯传统神车 | 极高 | 极高 | 极高 (车身/机电)  | 极高 | **Tier 1** |
| 6 | **Volkswagen** | **Tharu (途岳)** | 燃油/紧凑SUV | 上汽大众 | 平行进口性价比 | 高 | 极高 | 极高 (EA211/DQ200)| 高 | **Tier 1** |
| 7 | **Volkswagen** | **Passat / Magotan** | 燃油/中型轿车 | 上汽/一汽大众 | 俄商务用车主力 | 极高 | 高 | 高 (底盘/电气)    | 极高 | **Tier 2** |
| 8 | **BMW** | **X1 (长轴版 Li)** | 豪华/紧凑SUV | 华晨宝马 | 准新二手平行进口 | 高 | 极高 | 极高 (B38/B48加长)| 高 | **Tier 2** |
| 9 | **BMW** | **3 Series (加长 Li)**| 豪华/中型轿车 | 华晨宝马 | 准新平行进口 | 高 | 极高 | 极高 (底盘/车机)  | 高 | **Tier 2** |
| 10 | **Audi** | **A3 / A4L** | 豪华/轿车 | 一汽奥迪 | 平行进口主力 | 高 | 高 | 高 (灯具/模块)    | 高 | **Tier 2** |
| 11 | **Honda** | **CR-V / Breeze (皓影)**| 燃油/混动SUV | 东风/广汽本田 | 平行进口耐用型 | 高 | 高 | 高 (1.5T/CVT)     | 高 | **Tier 2** |
| 12 | **Honda** | **Vezel / XR-V** | 小型SUV | 广汽/东风本田 | 经济型平行进口 | 中 | 中 | 高 (滤清/传感器)  | 中 | **Tier 2** |
| 13 | **Hyundai** | **Elantra (伊兰特)** | 紧凑轿车 | 北京现代 | 出租/家用车平行 | 高 | 高 | 高 (底盘/机电)    | 高 | **Tier 2** |
| 14 | **Hyundai** | **Tucson (途胜L)** | 紧凑SUV | 北京现代 | 平行进口主力 | 高 | 高 | 高 (8AT/1.5T)     | 高 | **Tier 2** |
| 15 | **Kia** | **Sportage (狮跑加长)**| 紧凑SUV | 悦达起亚 | 平行进口主力 | 高 | 高 | 高 (外观件/易损)  | 高 | **Tier 2** |
| 16 | **Nissan** | **Qashqai / X-Trail** | 紧凑SUV | 东风日产 | 二手平行进口 | 中 | 极高 | 极高 (CVT变速箱)  | 高 | **Tier 3** |
| 17 | **Mazda** | **CX-5 / CX-50** | 紧凑SUV | 长安马自达 | 平行进口自吸车 | 高 | 中 | 中 (创驰蓝天件)   | 中 | **Tier 3** |
| 18 | **Mercedes-Benz**| **GLB / C-Class L**| 豪华SUV/轿车 | 北京奔驰 | 平行进口准新车 | 高 | 极高 | 极高 (加长件/机电)| 高 | **Tier 3** |
| 19 | **Lexus** | **ES (中国进口转手)**| 豪华中大型轿车| 丰田中国渠道转口| 高端二手进口 | 极高 | 高 | 高 (混动电池/外观)| 极高 | **Tier 3** |
| 20 | **Volkswagen** | **T-Roc (探歌)** | 紧凑跨界SUV | 一汽-大众 | 经济型平行进口 | 中 | 高 | 高 (DQ200离合器)  | 中 | **Tier 3** |

---

### 4.2 第二梯队：中国自主品牌汽车 (40% — 20 款车型)

| # | 品牌 | 车型 (Model) | 车辆类型 | 动力形式 | 俄罗斯流通形式 | 俄市场保有量 | 维修价值 | 配件机会 | SEO价值 | 优先级 |
|:---:|:---|:---|:---:|:---|:---|:---:|:---:|:---:|:---:|:---:|
| 21 | **Geely** | **Monjaro (星越L)** | 中型SUV | 2.0T+8AT | 官方+平行进口神车 | 极高 (Top 3) | 极高 | 极高 (车机/电控)  | 极高 | **Tier 1** |
| 22 | **Chery** | **Tiggo 7 Pro Max** | 紧凑SUV | 1.6T+7DCT | 官方销量台柱 | 极高 (Top 2) | 极高 | 极高 (传感器/底盘)| 极高 | **Tier 1** |
| 23 | **Chery** | **Tiggo 8 Pro Max** | 中型SUV | 2.0T+四驱 | 俄罗斯7座首选 | 极高 | 极高 | 极高 (四驱/变速箱)| 极高 | **Tier 1** |
| 24 | **Haval** | **Jolion (初恋)** | 紧凑SUV | 1.5T+7DCT | 图拉工厂+原装进口| 极高 (No. 1) | 极高 | 极高 (电子件/钣金)| 极高 | **Tier 1** |
| 25 | **Haval** | **F7 / F7x** | 紧凑轿跑SUV | 1.5T/2.0T | 俄本地组装+进口 | 极高 | 极高 | 极高 (四驱/离合器)| 极高 | **Tier 1** |
| 26 | **Tank** | **Tank 300 (坦克300)**| 硬派越野 | 2.0T+8AT | 硬派越野统治地位 | 极高 | 极高 | 极高 (改装件/分动箱)| 极高 | **Tier 1** |
| 27 | **Tank** | **Tank 500 (坦克500)**| 豪华硬派越野| 3.0T V6+9AT| 俄罗斯官商政务车 | 高 | 极高 | 极高 (空悬/大排件)| 极高 | **Tier 1** |
| 28 | **Geely** | **Coolray (缤越)** | 小型SUV | 1.5T+7DCT | 年轻人热门二手车 | 极高 | 极高 | 极高 (三缸/双离合)| 极高 | **Tier 1** |
| 29 | **Changan** | **CS55 Plus** | 紧凑SUV | 1.5T+7DCT | 官方直营主销 | 高 | 高 | 高 (蓝鲸发动机)  | 高 | **Tier 2** |
| 30 | **Changan** | **UNI-K** | 中型跨界SUV | 2.0T+8AT | 俄网红车型 | 高 | 高 | 高 (隐藏把手/车机)| 高 | **Tier 2** |
| 31 | **Exeed** | **RX (瑶光)** | 豪华跨界SUV | 2.0T+8AT/7DCT| 高端平行/官方主力 | 高 | 高 | 高 (CDC悬架/电控) | 高 | **Tier 2** |
| 32 | **Exeed** | **TXL (凌云)** | 中型SUV | 1.6T/2.0T | 中产家庭主力 | 高 | 高 | 高 (底盘件/车机)  | 高 | **Tier 2** |
| 33 | **Geely** | **Tugella (星越)** | 轿跑SUV | 2.0T+8AT | 二手高性能SUV | 高 | 高 | 高 (CMA架构通用件)| 高 | **Tier 2** |
| 34 | **Haval** | **Dargo (大狗)** | 潮玩轻越野 | 2.0T+四驱 | 俄自驾游热门车 | 高 | 高 | 高 (后桥差速锁件) | 高 | **Tier 2** |
| 35 | **Changan** | **CS35 Plus** | 小型SUV | 1.4T/1.6L | 经济耐用代步 | 中 | 中 | 高 (保养耗材)    | 中 | **Tier 2** |
| 36 | **Omoda** | **C5 (欧萌达)** | 跨界紧凑SUV | 1.5T/1.6T | 年轻女性主销车 | 极高 | 高 | 高 (全景影像/钣金)| 极高 | **Tier 2** |
| 37 | **Jaecoo** | **J7 (探索06)** | 方盒子SUV | 1.6T+四驱 | 新晋越野热门 | 中 | 高 | 高 (四驱控制模块) | 中 | **Tier 3** |
| 38 | **Jetour** | **Dashing (大圣)** | 紧凑SUV | 1.5T/1.6T | 性价比代步车 | 高 | 中 | 高 (外观件/内饰件)| 高 | **Tier 3** |
| 39 | **Jetour** | **Traveler (旅行者/T2)**| 硬派复古SUV| 2.0T+7DCT/8AT| 俄罗斯加价提车神车| 极高 | 极高 | 极高 (XWD四驱系统)| 极高 | **Tier 2** |
| 40 | **GAC** | **GS8 (传祺GS8)** | 中大型SUV | 2.0T+8AT | 俄全尺寸7座平替 | 中 | 高 | 高 (爱信8AT/底盘) | 中 | **Tier 3** |

---

### 4.3 第三梯队：新能源与智能电动车 (20% — 10 款车型)

| # | 品牌 | 车型 (Model) | 车辆类型 | 动力系统 | 俄罗斯流通形式 | 核心研究痛点 | 维修价值 | 配件机会 | SEO价值 | 优先级 |
|:---:|:---|:---|:---:|:---|:---|:---|:---:|:---:|:---:|:---:|
| 41 | **Zeekr** | **001** | 豪华猎装轿跑 | 纯电 (86/100kWh) | 俄高端电动车销量#1 | 远程账号锁/极寒续航/空气悬挂 | 极高 | 极高 (空悬/大灯/门把手)| 极高 | **Tier 1** |
| 42 | **Li Auto** | **L7 (理想L7)** | 中大型豪华SUV | 增程式 (1.5T增程器)| 俄罗斯富裕家庭首选 | 车机汉化刷机/增程器积碳/电控 | 极高 | 极高 (空悬气包/传感器) | 极高 | **Tier 1** |
| 43 | **Li Auto** | **L9 (理想L9)** | 全尺寸旗舰SUV | 增程式 (双电机四驱)| 俄高端商务用车 | 激光雷达防撞/空气悬架/电池包 | 极高 | 极高 (智驾模组/增程配件)| 极高 | **Tier 1** |
| 44 | **BYD** | **Song Plus (宋Plus)**| 紧凑SUV | DM-i混动/EV纯电 | 俄进口性价比新能源#1 | DM-i三电故障/刀片电池极寒衰减 | 极高 | 极高 (混动电机/专用电控)| 极高 | **Tier 1** |
| 45 | **BYD** | **Han (汉 EV/DM-i)** | 中大型轿车 | 纯电/插混 | 莫斯科商务高端平行 | 刹车系统/电池BMS/车机刷机 | 高 | 高 (智能刹车IPB/三电件) | 高 | **Tier 2** |
| 46 | **AITO** | **M7 (问界M7)** | 中大型SUV | 增程式 (华为智驾) | 平行进口高端车 | 鸿蒙车机锁/激光雷达校准/电驱 | 极高 | 极高 (智驾传感器/电驱件) | 极高 | **Tier 2** |
| 47 | **Voyah** | **Free (岚图Free)** | 中大型跨界SUV | 增程式/纯电 | 官方引进+平行进口 | 电池加热系统/空气悬挂/双电机 | 高 | 高 (增程器/四驱电机)   | 高 | **Tier 2** |
| 48 | **Avatr** | **11 (阿维塔11)** | 轿跑纯电SUV | 纯电 (华为电驱) | 俄前卫科技玩家首选 | 电子外后视镜/三激光雷达/BMS | 高 | 极高 (雷达传感器/车身件)| 中 | **Tier 3** |
| 49 | **BYD** | **Tang (唐 DM-p)** | 中大型7座SUV | 插电混动 (性能四驱)| 家庭越野平行进口 | 差速锁电控/后驱电机/混动变速箱| 高 | 高 (混动专用零部件)   | 中 | **Tier 3** |
| 50 | **Zeekr** | **009 / X** | 豪华MPV/紧凑SUV| 纯电架构 | 高端平行进口 | 极寒充电功率/威睿电驱/电吸门 | 高 | 高 (电机逆变器/专属件) | 中 | **Tier 3** |

---

## 5. 车型情报集群与内容生产模型 (Vehicle Intelligence Cluster)

对每一个进入数据库的战略车型，严格按照 **8 模块标准集群 (8-Module Standard Cluster)** 进行全生命周期建档：

```text
+----------------------------------------------------------------------------------------------------+
|                       标准车型情报集群结构 (Vehicle Intelligence Cluster)                           |
+------------------------------------+---------------------------------------------------------------+
| 模块序号与名称                     | 核心情报输出与商业转化价值                                     |
+------------------------------------+---------------------------------------------------------------+
| **1. Vehicle Overview**            | 车型定位、中国生产基地（一汽/广汽/自主）、三大件参数与平台代码 |
| (车型全景档案)                     | *价值：建立专业严谨基调，捕获车型大词 SEO*                     |
+------------------------------------+---------------------------------------------------------------+
| **2. Russia Market Status**        | 俄罗斯在售形式（大贸 vs 平行准新 vs 二手）、价格区间与保值率  |
| (俄罗斯市场画像)                   | *价值：帮助买家建立进口与持有成本预期*                         |
+------------------------------------+---------------------------------------------------------------+
| **3. Used Vehicle Guide**          | 二手车车况鉴别、VIN/ЭПТС查验、报废税计算、防调表与事故车避坑指南|
| (二手车避坑与清关指南)             | *价值：高价值决策指南，指导实际进口交易*                       |
+------------------------------------+---------------------------------------------------------------+
| **4. Chinese Owner Problems**      | 汇总裁决中国车主长期（3~5年/10万公里）真实故障、论坛与车质网投诉|
| (中国本土车主真实长测通病)         | *价值：打消信息不对称，提供俄罗斯本土尚未暴发的预警信号*       |
+------------------------------------+---------------------------------------------------------------+
| **5. Russian Owner Problems**      | 汇总 Drive2 / Drom 俄罗斯车主在极寒、劣质燃油、无原厂维保下的故障|
| (俄罗斯车主实测与本土痛点)         | *价值：精准共鸣本土车主日常维修痛点*                           |
+------------------------------------+---------------------------------------------------------------+
| **6. Repair & Maintenance Guide**  | 发动机/变速箱拆装要点、保养油液规格、车机语言破解刷机流程      |
| (专业维修与保养手册)               | *价值：吸引独立汽修厂技师与硬核车主专业流量*                   |
+------------------------------------+---------------------------------------------------------------+
| **7. Parts & OE Compatibility**    | 中国特规件 vs 欧版件差异对照、OE 零件号矩阵、副厂改进件替代方案 |
| (配件兼容性与零件号矩阵)           | *价值：解决订错配件死穴，提供精准物料清单 (BOM)*               |
+------------------------------------+---------------------------------------------------------------+
| **8. China Supply Solution**       | 如何从中国直采该车型备件、物流清关时效、假货鉴别与采购通道对接 |
| (中国供应链采办对接通道)           | *价值：高意向询盘与外贸采购线索直接变现入口*                   |
+------------------------------------+---------------------------------------------------------------+
```

---

## 6. 标准 SEO 内容生产矩阵范例 (以 4 款典型车型为例)

为保证内容的高密度与搜索覆盖，每款车型规划至少 5 篇高度针对性的长尾需求文章：

### 6.1 范例 1：中国渠道德系车代表 —— Volkswagen Tayron (探岳)
1. **文章 1 (综述)**：*China-Market Volkswagen Tayron vs European Tiguan: Complete Technical & Dimension Comparison*（中国版探岳与欧版途观全方位技术对比）
2. **文章 2 (二手合规)**：*Importing a Used Volkswagen Tayron from China to Russia: 2026 Customs, Utilization Fee & VIN Verification*（探岳二手平行进口关税、报废税与查验实战）
3. **文章 3 (通病1 - 变速箱)**：*Volkswagen Tayron DQ381 (0GC) DSG Emergency Mode & Solenoid Failure: Real Owner Cases & TSB Analysis*（DQ381 双离合紧急模式与电磁阀故障案例）
4. **文章 4 (通病2 - 颗粒捕捉器)**：*Volkswagen Tayron 330TSI GPF Clogging in Cold Climates: Regeneration Methods & Driving Tips*（探岳 330TSI 颗粒捕捉器极寒堵塞与再生应对）
5. **文章 5 (配件供应链)**：*Volkswagen Tayron 330TSI (DKV/DPL/DTH) Engine Parts Catalog: China-Spec OE Numbers vs European Part Numbers*（探岳 DKV/DPL 发动机中规零件号与欧版对照表及中国直发方案）

### 6.2 范例 2：中国渠道日系神车代表 —— Toyota Corolla (卡罗拉 / 广汽雷凌)
1. **文章 1 (综述)**：*China-Built Toyota Corolla 1.2T & 1.8L Hybrid: Why Russia is Importing Thousands of Units from China*（为何俄罗斯每月从中国平行进口数千台卡罗拉）
2. **文章 2 (二手合规)**：*Buying a 0-km "Used" Toyota Corolla from China: Hidden Risks, Equipment Gaps & Cold Weather Package Checks*（中国平行出口卡罗拉准新车避坑：冬季包与防锈缺失排查）
3. **文章 3 (通病 - 动力底盘)**：*Toyota 1.2T (9NR-FTS) Ignition Coil Failure & S-CVT Transmission Jerking: Chronic Issues from Chinese Forums*（卡罗拉 1.2T 点火线圈失效与 CVT 顿挫通病解析）
4. **文章 4 (车机与电气)**：*Toyota Corolla China-Market Head Unit English/Russian Localization: Firmware & CAN-Bus Adapter Guide*（中规卡罗拉大屏车机汉化与俄语刷机指南）
5. **文章 5 (配件供应链)**：*Toyota Corolla China Sourcing Parts List: Engine Mounts, Brake Pads & Body Panels Direct from Guangdong*（卡罗拉中国原厂机脚胶、刹车片与钣金件采购指南）

### 6.3 范例 3：中国自主主力代表 —— Geely Monjaro (吉利星越L)
1. **文章 1 (综述)**：*Geely Monjaro: Official Russian Spec vs China-Market Parallel Import: What You Gain and What You Lose*（吉利星越L官方中规与平行进口中规版配置、质保与差价全对比）
2. **文章 2 (通病 - 电子电控)**：*Geely Monjaro Triple-Screen Freezing, Telematics Lock & Remote Start Issues in Russian Winters*（星越L三联屏死机、T-Box 车机锁与极寒远程启动故障复盘）
3. **文章 3 (通病 - 动力总成)**：*Geely Monjaro 2.0T Drive-E Engine & Aisin 8AT: Oil Leaks, Thermostat Failure & Real Owner Repair Costs*（星越L 2.0T 节温器损坏与爱信8AT真实维修成本核算）
4. **文章 4 (维护保养)**：*Geely Monjaro 4WD Haldex 6th Gen Coupling Maintenance: Fluid Replacement Intervals & Filter Sourcing*（星越L博格华纳六代四驱差速器油液更换与滤网配件指南）
5. **文章 5 (配件供应链)**：*Where to Source Genuine Geely Monjaro Body & Sensor Parts from China: Complete Part Number Cross-Reference*（吉利星越L全车传感器与原厂外观件中国源头采购渠道）

### 6.4 范例 4：高端智能纯电代表 —— Zeekr 001 (极氪001)
1. **文章 1 (综述)**：*Zeekr 001 in Russia: Complete Buying & Ownership Guide for the Best-Selling Electric Vehicle*（极氪001在俄保有量第一纯电车选购与用车全书）
2. **文章 2 (核心痛点 - 账号锁)**：*Zeekr 001 Master Account Binding, Remote Key Lock & Telematics Bypass for Exported Vehicles*（极氪001平行出口车主账号解绑、蓝牙钥匙失效与海外车联网激活指南）
3. **文章 3 (极寒通病 - 空悬底盘)**：*Zeekr 001 Air Suspension Valve Block Freezing & Compressor Failure at -30°C: Diagnostics & Fixes*（极氪001极寒空悬分配阀结冰与气泵烧毁故障诊断与改装）
4. **文章 4 (三电系统)**：*Zeekr 001 CATL 100kWh vs VREMT 86kWh Battery Pack Health Degradation & Fast Charging Compatibility in Russia*（极氪001宁德时代与威睿电池极寒衰减对比及国标转欧标快充桩实测）
5. **文章 5 (配件供应链)**：*Zeekr 001 Air Suspension Air Springs, Electric Doors & Matrix Headlights Direct Sourcing from Ningbo*（极氪001空气弹簧气包、电吸门总成与矩阵大灯中国直发渠道）

---

## 7. 与现有架构体系的深度协同与演进路线 (Architecture Evolution)

### 7.1 A. 无需修改代码即可直接执行部分 (Immediate Zero-Code Execution)
1. **分类法挂载**：直接在现有的 `fyz_vehicle` 中注册品牌 Term 与车型 Term（如 `byd` $\to$ `song-plus`, `geely` $\to$ `monjaro`, `toyota` $\to$ `corolla`）；
2. **内容角色标记**：直接复用现有的 7 类 `fyz_research_type`（`overview`, `owner-cases`, `common-problems`, `parts-compatibility`, `market-version`, `repair-guide`, `case-study`）；
3. **双语发布管道**：使用现有的 `publish_single_article.py` 传入 `--content-language en/ru`，由 `0.4.5 Resolver V2` 自动接管，100% 保持 HTML `lang`、OG `locale`、Schema 与 Feed 纯度。

### 7.2 B. 未来需要升级部分 (Future Upgrades — Phase 2 & 3)
1. **车型元数据自定义字段 (Custom Metadata)**：
   - 为文章增加结构化参数：`_cfc_engine_code`（发动机型号）, `_cfc_transmission_code`（变速箱型号）, `_cfc_market_years`（生产年份）, `_cfc_oe_part_no`（主零件号）；
2. **Schema.org 汽车专有微数据 (Automotive Schema)**：
   - 升级 `fyzsxnb-p0-seo-patch.php`，为车型聚合页输出 `schema.org/Car` 与 `schema.org/Product` 结构化数据；
3. **Hub 页面分类布局升级**：
   - 将现有 6 品牌矩阵升级为“德系平行进口”、“日韩实用车型”、“中国自主燃油”、“智能电动/新能源”四大分组视窗；
4. **Launch Gate 达成与公网开门**：
   - 在完成 Toyota Corolla (3篇) 与 Audi Q3 (3篇) 发布后，全站车型数达 3 款，双语文章达标，正式将 `FYZSXNB_CFC_LAUNCH_GATE_OPEN` 设为 `true` 并解除 `/ru/cars-from-china/` 的 `noindex`。

### 7.3 C. 严禁修改部分 (Strict Invariants — DO NOT TOUCH)
1. **底层语言解析器**：严禁因汽车模块修改 `fyzsxnb-p0-seo-patch.php` 的通用多语言判定机制；
2. **证据优先准入原则 (Case Contract)**：严禁由 AI 批量编造无信源支持的故障或捏造不存在的零件通用性；
3. **扁平文章 URL 体系**：单篇文章 URL 必须保持在根目录 `https://fyzsxnb.com/{slug}/`，Canonical 严格自指向，保证全站 SEO 资产权重不分散。

---

## 8. 交付结论与状态汇报

已生成完整的战略规划纲领：  
[`docs/CARS-INTELLIGENCE-STRATEGY-V2-001.md`](file:///C:/Users/Administrator/Documents/Codex/2026-07-10/w/work/fyzsxnb-ui-v2/docs/CARS-INTELLIGENCE-STRATEGY-V2-001.md)

```text
STRATEGY_STATUS:
COMPLETE

POSITIONING:
Russia-Market China Vehicle Full-Lifecycle Intelligence Database

PRIORITY_POOL:
50 Vehicles (40% Global JV via China + 40% Chinese Domestic + 20% NEV/Smart EV)

DATA_MODEL:
8-Module Intelligence Cluster + 5-Article Standard SEO Matrix per Model

ARCHITECTURE_COMPATIBILITY:
PASS (100% Native on 0.3.2 Scaffolding & 0.4.5 Resolver V2)

PRODUCTION_CHANGE:
NONE (Strategy Only)

STOP
```
