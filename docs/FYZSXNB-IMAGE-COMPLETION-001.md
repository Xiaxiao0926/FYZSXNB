# FYZSXNB-IMAGE-COMPLETION-001: 全站历史文章配图补齐计划

> **任务编号**：FYZSXNB-IMAGE-COMPLETION-001  
> **执行角色**：Google Gemini Flash 3.7  
> **依据规范**：`FYZSXNB-VISUAL-GUIDELINE-001` / `FYZSXNB-CONTENT-GROWTH-PRIORITY-001`  
> **状态**：`IMAGE_COMPLETION_PLAN_READY`  
> **统计基准**：全站 97 篇已发布文章（扫描时间：2026-08-24）

---

## 一、资产缺口与现状统计

全站 97 篇已发布文章的视觉资产扫描结果如下：

| 统计指标 | 数量 | 占比 | 现状说明 |
|---|---|---|---|
| **全站已发布文章总数** | **97 篇** | 100% | 俄文与英文双语文章资产 |
| **Visual System 2.0 规范封面 (Featured Image)** | **6 篇** | 6.2% | 首批完成模板化重构（Post 640, 484, 420, 448, 432, 466） |
| **旧版 AI 模版 / 暗色 CAD 封面** | **91 篇** | 93.8% | 需分批升级替换为 5 大专属视觉模板 |
| **正文已配图文章数 (>= 2 张)** | **89 篇** | 91.8% | 历史技术图保留，作为正文插图继续使用 |
| **正文仅 1 张配图文章数** | **8 篇** | 8.2% | 后续结合深度内容逐步补充架构/参数图 |
| **正文 0 配图文章数** | **0 篇** | 0.0% | 无纯文字裸奔文章 |

---

## 二、文章分级体系 (Article Tiering)

根据用户搜索意图与商业价值，将全站 97 篇文章划分为三个优先级层级：

```
                    ┌────────────────────────────────────────┐
                    │      Priority A (核心搜索文章 · 19篇)    │
                    │  1 规范封面 + 至少 2 张正文深度结构图    │
                    └───────────────────┬────────────────────┘
                                        │
                    ┌───────────────────┴────────────────────┐
                    │      Priority B (长期 SEO 文章 · 78篇)   │
                    │  1 规范封面 + 至少 1 张正文信息图        │
                    └───────────────────┬────────────────────┘
                                        │
                    ┌───────────────────┴────────────────────┐
                    │      Priority C (短讯/归档 · 0篇待建)   │
                    │  仅补齐 1 张 Visual System 2.0 规范封面  │
                    └────────────────────────────────────────┘
```

### 1. Priority A (核心搜索文章 · 19 篇)
- **要求**：必须具备 **1 张 Visual System 2.0 专属模板封面** + **至少 2 张正文深度图**（流程图、参数表、电气架构、故障对比图、步骤图）。
- **已完成 VS 2.0 封面**：4 篇（Post 640 Tayron, 484 EPTS, 432 关税, 466 FDA）
- **待补封面缺口**：15 篇。

### 2. Priority B (长期 SEO 文章 · 78 篇)
- **要求**：至少具备 **1 张 Visual System 2.0 专属模板封面** + **1 张正文信息图**。
- **已完成 VS 2.0 封面**：2 篇（Post 420 荣耀, 448 拓竹）
- **待补封面缺口**：76 篇。

### 3. Priority C (短讯与归档)
- **要求**：仅补齐 1 张 16:9 标准规范封面。

---

## 三、Visual System 2.0 模板映射规则

严格禁止全站使用单一暗色 CAD 风格，所有文章按内容领域强制绑定 5 大模板：

| 模板名称 | 适用领域 | 核心视觉语言 | 严禁元素 |
|---|---|---|---|
| **Template A (Automotive Engineering)** | 汽车维修、DQ381、CAN 逆向、电气与机械故障 | 深色工程蓝、机械切面、冷白线框、故障代码芯片 | 严禁过度科幻 AI 粒子 |
| **Template B (Vehicle Market)** | 车型介绍、二手车选购、大众/丰田/比亚迪/奇瑞 | 实景演播室、自然冷灰背景、车型外形特写、配置标签 | 严禁虚构不存在的外观与型号 |
| **Template C (Product 3C / Industrial)** | 3C 产品、3D 打印、无人机、4-20mA 传感器、工业变频器 | 浅灰白色背景、产品棚拍光影、精密工业质感 | 严禁花哨杂乱贴图 |
| **Template D (Market Policy)** | EPTS 电子汽车合格证、2026 乌尔费、海关关税、合规政策 | 极简仪表盘风、政策印鉴、合规数据表卡片 | 严禁过度仿制政府公章公文 |
| **Template E (Biomedical & Regulatory)** | FDA 21 CFR 207、NMPA UDI 2027、分子 POCT、IVD 诊断 | 临床科研白、医疗蓝、自动化仪器特写、标准标签 | 严禁恐怖病理或夸大疗效图 |

---

## 四、分批推进生产计划 (4-Stage Batch Plan)

### 阶段一：高价值汽车生态与平行进口 (Batch 1 · 24 篇)
- **核心重点**：中国进口二手车、大众 Tayron、DQ381 变速箱维修、EPTS/VIN 查验、2026 乌尔费计算、比亚迪 Openpilot CAN、奇瑞 ADB 固件。
- **模板分配**：Template A (11 篇) / Template B (11 篇) / Template D (2 篇)。
- **目标**：筑牢俄罗斯市场汽车搜索流量护城河。

### 阶段二：工业供应链与自动化 (Batch 2 · 54 篇)
- **核心重点**：4-20mA 压力变送器、变频器 PID 接线、M22/BSPP 螺纹接头、PLC 国产替代、3C 智能终端。
- **模板分配**：Template C (52 篇) / Template A (2 篇)。
- **目标**：建立工业品海外采购与工程师选型第一技术站。

### 阶段三：医疗合规与生物医药 (Batch 3 · 19 篇)
- **核心重点**：FDA 外国药企注册、NMPA UDI 2027 强制时间线、iFIND 自动化分子 POCT 仪器与结核 TBR 试剂盒。
- **模板分配**：Template E (19 篇)。
- **目标**：打造合规与出海医疗器械采购权威。

### 阶段四：长尾归档与短讯 (Batch 4)
- **目标**：全量清理早期遗留暗色 AI 模板，达成全站 100% 视觉规范统一。

---

## 五、图片技术规格与正文配图规范

### 1. Featured Image (封面图) 规范
- **物理分辨率**：`1200 × 675` px（标准 16:9）
- **文件格式**：高质量 JPG / WebP（体积控制在 150KB~280KB 之间）
- **构图铁律**：主体明确居中或黄金分割；信息标签控制在 3~4 个以内；移动端 390px 缩略图清晰可辨；**严禁大段正文文字搬运**。

### 2. In-article Body Image (正文插图) 规范
- **唯一目的**：**增强工程与业务理解**。
- **允许类型**：
  1. 流程图（如 EPTS 查验 5 步流程、FDA 注册 4 阶段）
  2. 参数表（如 DKV/DPL 引擎件号对照、乌尔费排量费率表）
  3. 架构图（如 CAN-FD 收发器接线、4-20mA 2线制回路）
  4. 故障对比图（如 DQ381 传感器焊点裂纹 vs 修复焊点）
  5. 拆解步骤图（如 仪表盘拆卸、ADB 开发者选项激活）
- **禁止类型**：无信息量的纯装饰性 AI 生成插画。

---

## 六、全站文章配图缺口详细清单 (97 篇)

| ID | 文章标题 | 语言 | 分级 | 匹配模板 | 封面图现状 | 正文图数 | 计划操作 |
|---|---|---|---|---|---|---|---|
| 640 | Volkswagen Tayron из Китая: обзор модели, пла... | RU | Priority A | Template B (Vehicle Market) | VS 2.0 已完成 | 4 张 | 保持规范封面 |
| 514 | DQ381 на Volkswagen Tayron из Китая: аварийны... | RU | Priority A | Template A (Automotive Engineering) | 旧版 AI 模版 | 4 张 | 生成 Template |
| 513 | DQ381 Emergency Mode on the China-Market Volk... | EN | Priority A | Template A (Automotive Engineering) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 512 | Volkswagen Tayron 330TSI из Китая: что реальн... | RU | Priority A | Template B (Vehicle Market) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 511 | China-Market Volkswagen Tayron 330TSI GPF: Wh... | EN | Priority A | Template B (Vehicle Market) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 510 | Volkswagen Tayron 330TSI из Китая: почему DKV... | RU | Priority A | Template B (Vehicle Market) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 509 | China-Market Volkswagen Tayron 330TSI: Why DK... | EN | Priority A | Template B (Vehicle Market) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 504 | BYD Frigate 07 и openpilot: какие данные нужн... | RU | Priority A | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 4 张 | 生成 Template |
| 503 | Как проверить BYD перед установкой openpilot:... | RU | Priority A | Template A (Automotive Engineering) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 500 | Openpilot для BYD в 2026 году: что уже есть в... | RU | Priority A | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 496 | REDMAGIC Cooler 6 Pro+ China Launch: Buyer&#8... | EN | Priority B | Template B (Vehicle Market) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 495 | Huawei MatePad Pro 2026 China Announcement Bu... | EN | Priority B | Template B (Vehicle Market) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 494 | REDMI Kids Watch Pro China Version: Buyer&#82... | EN | Priority B | Template B (Vehicle Market) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 493 | Pet Food Label: Find the Manufacturer and Che... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 492 | Датчик давления 4-20 мА: WIKA A-10 и Danfoss ... | RU | Priority A | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 491 | G1/4, R1/4 и NPT 1/4: резьба датчика давления... | RU | Priority A | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 490 | Go Raw Pet Food Recall 2026: Lot Codes &#038;... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 489 | Как подобрать датчик давления 4-20 мА для час... | RU | Priority A | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 4 张 | 生成 Template |
| 487 | FDA GUDID and AccessGUDID Procurement Verific... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 486 | Tongue-Swab Tuberculosis Diagnostic Accuracy:... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 485 | Бесплатное обновление Android Auto для Chery ... | RU | Priority A | Template A (Automotive Engineering) | 旧版 AI 模版 | 4 张 | 生成 Template |
| 484 | Проверка ЭПТС по VIN перед покупкой автомобил... | RU | Priority A | Template B (Vehicle Market) | VS 2.0 已完成 | 4 张 | 保持规范封面 |
| 480 | FDA Establishment Registration Verification G... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 479 | 国家药监局第21号公告UDI实施解读：2027二类器械与一类IVD赋码、数据库上传及医保代... | EN | Priority A | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 4 张 | 生成 Template |
| 470 | 村卫生室CRP与SAA联合POCT对抗生素处方率影响评估... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 466 | FDA Foreign Drug Establishment Registration G... | EN | Priority A | Template E (Biomedical & Regulatory) | VS 2.0 已完成 | 4 张 | 保持规范封面 |
| 465 | FDA Labeler Code Checklist for Foreign Compan... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 464 | FXR0906 Clinical Trial China: Fosun Evidence ... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 463 | FDA Global Generic Drug Affairs: Overseas Tea... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 462 | FDA Drug Registration and Listing: A Pre-Work... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 461 | GDUFA III Controlled Correspondence: Scope, S... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 460 | Cell and Gene Therapy BLA Readiness: What FDA... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 451 | Casgevy for Children Aged 2 and Older: Direct... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 450 | GLP-1 Generic Development Is Not One Pathway:... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 449 | Model-Integrated Evidence for Generic Drugs: ... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 448 | Bambu Lab из Китая для России: что проверить ... | RU | Priority B | Template C (Product 3C / Industrial) | VS 2.0 已完成 | 4 张 | 保持规范封面 |
| 447 | Digitally Derived Endpoints: A Sponsor Readin... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 446 | FDA 2026 CGT CMC Guidance: What Is Flexible a... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 445 | Как проверить регистрацию китайской IVD в Рос... | RU | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 444 | 2026—2028年俄罗斯与EAEU医疗器械注册过渡期：中国IVD厂家路线图... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 443 | TB Test LoD: What 10 vs 100 CFU/mL Really Mea... | EN | Priority B | Template A (Automotive Engineering) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 442 | Стартер и карбюратор бензокосы 43/52cc: прове... | RU | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 441 | Китайский тест iFIND TBR: доказательства и тр... | RU | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 439 | iFIND IFQ INH/FQ Cartridge: Evidence and Proc... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 3 张 | 生成 Template |
| 437 | iFIND TBR MTB/RIF Cartridge: Evidence and Pro... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 3 张 | 生成 Template |
| 435 | 海关总署令281号详解：2026年特殊物品进出境合规自查清单... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 434 | Petmi против российских кормов: что доказывае... | RU | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 433 | Разъём шланга мойки: как выбрать адаптер без ... | RU | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 432 | Утильсбор 2026: три пути ввоза авто из Китая ... | RU | Priority A | Template D (Market Policy) | VS 2.0 已完成 | 4 张 | 保持规范封面 |
| 431 | Fully Automated Molecular POCT: iFIND S2/S4/S... | EN | Priority A | Template A (Automotive Engineering) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 426 | BMW N55 снова течет после замены прокладки: к... | RU | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 425 | Microplastics in Pet Food: What the 76% Study... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 424 | 国家反诈中心AI内容鉴定怎么用？结果能证明什么... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 420 | HONOR из Китая для России: 15 проверок перед ... | RU | Priority B | Template B (Vehicle Market) | VS 2.0 已完成 | 4 张 | 保持规范封面 |
| 415 | Удалённая блокировка китайского электромобиля... | RU | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 413 | Microplastics in Pet Food: What the Latest Te... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 411 | 2026 中国西药出口拆解：制剂增长、GLP-1 原料药与新兴市场机会... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 405 | Течь масла BMW N55: NBR, HNBR или FKM?... | RU | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 398 | AI Voice Recorders Compared: Subscription Cos... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 394 | PLAUD 招聘基带工程师意味着什么：AI 耳机还是独立联网录音设备？... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 390 | Китайский корм для кошек в России: как сравни... | RU | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 388 | 深圳生物医药特殊物品进出口机制：哪些环节真的变快了？... | EN | Priority B | Template E (Biomedical & Regulatory) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 372 | HONOR Magic V6 для России: китайская версия п... | RU | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 3 张 | 生成 Template |
| 362 | Best Budget Robot Vacuum in 2026: A Durable B... | EN | Priority B | Template B (Vehicle Market) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 361 | Lickable Cat Supplements: How to Read Calming... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 360 | How to Choose a 27-Inch IPS Monitor Under CAD... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 359 | Bristol Myers&#8217; AI Factory and Samsung&#... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 358 | WAIC 2026 阶跃展台的信号：智能体手机、汽车与机器人，哪类先落地？... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 357 | Tempus to Buy Personalis for $1.5B: What the ... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 356 | Pedigree Wet Dog Food Recall: What Owners Sho... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 355 | 小米米家智能冲牙器 Pro 开售：349 元定价背后的产品信号与选购框架... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 350 | Kimi K3: почему китайская открытая модель на ... | RU | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 349 | Kimi K3 Explained: Why China&#8217;s 2.8T Ope... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 347 | Kimi K3 为什么刷屏：知乎争议、2.8 万亿参数与开源模型的新问题... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 304 | Ditch the Kibble: My Secret to Healthy, Homem... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 297 | The Ultimate Cat Wooden Jigsaw Puzzle: A Mind... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 3 张 | 生成 Template |
| 290 | Healing Crystal Cat Ornaments: A Guide to The... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 4 张 | 生成 Template |
| 287 | Bring Whimsical Charm to Your Space with Our ... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 3 张 | 生成 Template |
| 284 | The Grim Reaper on a Toilet Statue: Your Ulti... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 3 张 | 生成 Template |
| 276 | Shan Gui Hua Qian: The Ultimate Guide to This... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 268 | Dog Fur Memorial Pendant: Carry Their Eternal... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 3 张 | 生成 Template |
| 257 | Unlock Abundance: The Ancient Wealth Amulet f... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 3 张 | 生成 Template |
| 250 | How to Decorate Your Desk with Léon: The Prof... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 3 张 | 生成 Template |
| 239 | How to Buy from Taobao Directly: A Step-by-St... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 11 张 | 生成 Template |
| 233 | Legacy of the Dragon Tomb – Chapter 8... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 1 张 | 生成 Template |
| 226 | How to Choose the Best Keepsake for Pet Fur t... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 222 | Legacy of the Dragon Tomb – Chapter 7... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 1 张 | 生成 Template |
| 219 | Legacy of the Dragon Tomb – Chapter 6... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 1 张 | 生成 Template |
| 217 | Legacy of the Dragon Tomb – Chapter 5... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 1 张 | 生成 Template |
| 215 | Legacy of the Dragon Tomb &#8211; Chapter 4... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 1 张 | 生成 Template |
| 213 | 跨境电商亚马逊德国Schweberegale（浮动搁板）市场研究分析报告-20251013... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 209 | 跨境电商亚马逊平台法国首饰收纳市场深度分析报告-2025年9月... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 2 张 | 生成 Template |
| 206 | Legacy of the Dragon Tomb – Chapter 3... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 1 张 | 生成 Template |
| 204 | Legacy of the Dragon Tomb &#8211; Chapter 2... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 1 张 | 生成 Template |
| 195 | Legacy of the Dragon Tomb &#8211; Chapter 1... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 1 张 | 生成 Template |
| 193 | Wie Sie Ihre Katze davon abhalten, auf Ihrer ... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 4 张 | 生成 Template |
| 52 | How to Stop Your Cat From Sitting on Your Key... | EN | Priority B | Template C (Product 3C / Industrial) | 旧版 AI 模版 | 4 张 | 生成 Template |

---

## 七、生产与部署 SOP (Standard Operating Procedure)

```
本地根据 Template 规范生成
           ↓
AI / 人工双重真实性与信息过载审核
           ↓
PowerShell / Python REST API 上传至 WordPress Media Library (设置 ALT & Caption)
           ↓
更新对应文章 featured_media 字段绑定
           ↓
LiteSpeed 缓存清理 (feed-cache / litespeed_purge_all)
           ↓
实机 390px / 1440px 首页与文章页视觉验收
```

- **历史资产保留原则**：旧技术配图继续保留在 Media 库与正文中，不进行物理删除。

---

## 八、完成度度量指标

- **全站文章总数**：97 篇
- **已完成 VS 2.0 规范文章**：6 篇 (6.2%)
- **待补齐 Featured Image 数量**：91 篇 (93.8%)
- **正文插图合规率**：100% (89 篇 >= 2 张，8 篇 1 张，0 篇 0 张)

---

**计划就绪状态**：  
`IMAGE_COMPLETION_PLAN_READY`
