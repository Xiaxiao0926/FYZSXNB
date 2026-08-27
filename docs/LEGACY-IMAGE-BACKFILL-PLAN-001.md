# FYZSXNB — 历史文章视觉补全执行规划方案 (Legacy Image Backfill Plan 001)

**文档编号:** `FYZ-DOC-20260821-LEGACY-IMAGE-PLAN-001`  
**任务编号:** `FYZSXNB-LEGACY-IMAGE-PLAN-001`  
**执行角色:** Google Gemini Flash 3.7  
**阶段状态:** `LEGACY_IMAGE_PLAN_COMPLETE` (规划完成，禁止代码/文章修改)  
**规划范围:** 全站 97 篇已发布文章的分层视觉补全、图片类型定义、风格体系及批次规划  

---

## 1. 全站统计 (Sitewide Summary)

| 统计维度 | 统计数据 | 占比 / 说明 |
|:---|:---:|:---|
| **全站已发布文章总量** | **97 篇** | 100% 全量审计覆盖 |
| **初始完全缺封面图 (No Featured)** | **61 篇** | 占比 62.9%，列表页与分享卡片严重缺失 |
| **正文缺图 / 少于2张插图** | **86 篇** | 占比 88.7%，正文纯文字排版，停留时间短 |
| **Priority A (核心战略 / 立即补图)** | **18 篇** | 占比 18.6%，包含 Cars Hub、俄语核心指南、高搜索词 |
| **Priority B (技术深度 / 第二批)** | **24 篇** | 占比 24.7%，包含特定产品拆解、法规专题、诊断案例 |
| **Priority C (长尾信号 / 暂缓)** | **55 篇** | 占比 56.7%，包含短讯快报、存量归档章节 |

---

## 2. Priority A 列表 (核心战略与流量枢纽 — 立即补图)

Priority A 文章为全站品牌认知、搜索流量转化及车型 Hub 的核心载体，评分区间 ≥ 16 分，要求配置 **1 封面 + 2~3 张高价值结构化插图**：

### A-1. [Post ID: 484] Проверка ЭПТС по VIN перед покупкой автомобиля: официальный портал и чек-лист документов

- **URL**: `https://fyzsxnb.com/proverka-epts-po-vin-pered-pokupkoj/`
- **语言与分类**: `RU` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `24 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `System Diagram (系统架构) + Process Map (流程图) + Checklist (核验清单)`
- **补图立项理由**: 跨境进口与技术适配必经环节，高商业价值与转化前置情报
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `System Architecture / Official Process Banner`
  * **Image 01 (首屏技术概念)**: `硬件拓扑与总线连接图 (如 CAN 节点、相机线束或海关计算路径)`
  * **Image 02 (核心机制/流程)**: `关键参数核验与版本差异对比表 (如 EPTS 状态或智驾支持矩阵)`
  * **Image 03 (维保/选型清单)**: `实操步骤与安全自查清单 (安装指引或海关税费计算实例)`

### A-2. [Post ID: 640] Volkswagen Tayron из Китая: обзор модели, платформа MQB и особенности выбора на рынке России

- **URL**: `https://fyzsxnb.com/volkswagen-tayron-from-china-overview/`
- **语言与分类**: `RU` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `24 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Hero (外观) + Market Diff (中俄差异) + Architecture (MQB 结构)`
- **补图立项理由**: 全站核心战略车型 Hub 首发柱石文章，承接高频商业决策搜索意图
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `Hero Exterior (3/4 真实实景或高质量渲染)`
  * **Image 01 (首屏技术概念)**: `中俄市场定位与轴距空间对比图 (MQB A2 平台 2731mm vs 2678mm)`
  * **Image 02 (核心机制/流程)**: `动力总成与关键参数图 (EA888 Gen3B 2.0T + DQ381 7速湿式双离合)`
  * **Image 03 (维保/选型清单)**: `跨境适配与冬季维护场景图 (车机 MIB3 汉化/俄化与极寒启动)`

### A-3. [Post ID: 420] HONOR из Китая для России: 15 проверок перед покупкой — полный разбор

- **URL**: `https://fyzsxnb.com/honor-iz-kitaya-v-rossii-proverka-pered-pokupkoy/`
- **语言与分类**: `RU` | Product Research / 产品研究 (`B` 类)
- **综合评分 (Score)**: `22 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Product Teardown (硬件拆解) + Scenario Application (应用场景) + Sourcing Matrix (选型对比)`
- **补图立项理由**: 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `Product Studio Hero (产品主体高质感渲染/棚拍)`
  * **Image 01 (首屏技术概念)**: `核心元器件与内部硬件架构拆解图 (主控芯片、传感器与模组)`
  * **Image 02 (核心机制/流程)**: `真实工作场景与性能基准实测图 (温控、功耗、定位与响应实测)`
  * **Image 03 (维保/选型清单)**: `国行版 vs 海外版选型与兼容性核验卡 (网络锁、频段、电压与协议)`

### A-4. [Post ID: 500] Openpilot для BYD в 2026 году: что уже есть в открытом коде и почему «поддержка BYD» ещё не означает plug-and-play

- **URL**: `https://fyzsxnb.com/openpilot-byd-2026-support-open-source/`
- **语言与分类**: `RU` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `21 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `System Diagram (系统架构) + Process Map (流程图) + Checklist (核验清单)`
- **补图立项理由**: 跨境进口与技术适配必经环节，高商业价值与转化前置情报
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `System Architecture / Official Process Banner`
  * **Image 01 (首屏技术概念)**: `硬件拓扑与总线连接图 (如 CAN 节点、相机线束或海关计算路径)`
  * **Image 02 (核心机制/流程)**: `关键参数核验与版本差异对比表 (如 EPTS 状态或智驾支持矩阵)`
  * **Image 03 (维保/选型清单)**: `实操步骤与安全自查清单 (安装指引或海关税费计算实例)`

### A-5. [Post ID: 503] Как проверить BYD перед установкой openpilot: камера ADAS, CAN, ECU и fingerprint

- **URL**: `https://fyzsxnb.com/kak-proverit-byd-pered-ustanovkoy-openpilot-camera-can-ecu-fingerprint/`
- **语言与分类**: `RU` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `21 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `System Diagram (系统架构) + Process Map (流程图) + Checklist (核验清单)`
- **补图立项理由**: 跨境进口与技术适配必经环节，高商业价值与转化前置情报
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `System Architecture / Official Process Banner`
  * **Image 01 (首屏技术概念)**: `硬件拓扑与总线连接图 (如 CAN 节点、相机线束或海关计算路径)`
  * **Image 02 (核心机制/流程)**: `关键参数核验与版本差异对比表 (如 EPTS 状态或智驾支持矩阵)`
  * **Image 03 (维保/选型清单)**: `实操步骤与安全自查清单 (安装指引或海关税费计算实例)`

### A-6. [Post ID: 504] BYD Frigate 07 и openpilot: какие данные нужны, чтобы оценить сложность адаптации

- **URL**: `https://fyzsxnb.com/byd-frigate-07-openpilot-dannye-dlya-adaptacii/`
- **语言与分类**: `RU` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `21 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `System Diagram (系统架构) + Process Map (流程图) + Checklist (核验清单)`
- **补图立项理由**: 跨境进口与技术适配必经环节，高商业价值与转化前置情报
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `System Architecture / Official Process Banner`
  * **Image 01 (首屏技术概念)**: `硬件拓扑与总线连接图 (如 CAN 节点、相机线束或海关计算路径)`
  * **Image 02 (核心机制/流程)**: `关键参数核验与版本差异对比表 (如 EPTS 状态或智驾支持矩阵)`
  * **Image 03 (维保/选型清单)**: `实操步骤与安全自查清单 (安装指引或海关税费计算实例)`

### A-7. [Post ID: 510] Volkswagen Tayron 330TSI из Китая: почему DKV, DPL и DTH нужно проверить до заказа запчастей

- **URL**: `https://fyzsxnb.com/volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay/`
- **语言与分类**: `RU` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `21 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Component Teardown (部件解构) + Diagnostic Flow (诊断流程) + Repair Guide (方案)`
- **补图立项理由**: 解决俄罗斯车主高频遭遇的真实维修痛点，提升页面停留时间与技术权威度
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `Mechatronic / Engine Teardown (核心机械部件特写)`
  * **Image 01 (首屏技术概念)**: `故障机理与传感器/阀体位置示意图 (如 N433-N440 电磁阀或 GPF 过滤层)`
  * **Image 02 (核心机制/流程)**: `标准化排查与电脑诊断读取流程图 (OBD/CAN 数据流读取指标)`
  * **Image 03 (维保/选型清单)**: `维修与配件替换方案对比图 (OE 原厂备件 vs 拆车件 vs 替代方案)`

### A-8. [Post ID: 512] Volkswagen Tayron 330TSI из Китая: что реальные владельцы сообщают о GPF

- **URL**: `https://fyzsxnb.com/volkswagen-tayron-330tsi-kitay-gpf-opyt-vladeltsev/`
- **语言与分类**: `RU` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `21 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Component Teardown (部件解构) + Diagnostic Flow (诊断流程) + Repair Guide (方案)`
- **补图立项理由**: 解决俄罗斯车主高频遭遇的真实维修痛点，提升页面停留时间与技术权威度
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `Mechatronic / Engine Teardown (核心机械部件特写)`
  * **Image 01 (首屏技术概念)**: `故障机理与传感器/阀体位置示意图 (如 N433-N440 电磁阀或 GPF 过滤层)`
  * **Image 02 (核心机制/流程)**: `标准化排查与电脑诊断读取流程图 (OBD/CAN 数据流读取指标)`
  * **Image 03 (维保/选型清单)**: `维修与配件替换方案对比图 (OE 原厂备件 vs 拆车件 vs 替代方案)`

### A-9. [Post ID: 514] DQ381 на Volkswagen Tayron из Китая: аварийный режим, потеря передач и реальные случаи владельцев

- **URL**: `https://fyzsxnb.com/volkswagen-tayron-kitay-dq381-avariynyy-rezhim-realnye-sluchai/`
- **语言与分类**: `RU` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `21 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Component Teardown (部件解构) + Diagnostic Flow (诊断流程) + Repair Guide (方案)`
- **补图立项理由**: 解决俄罗斯车主高频遭遇的真实维修痛点，提升页面停留时间与技术权威度
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `Mechatronic / Engine Teardown (核心机械部件特写)`
  * **Image 01 (首屏技术概念)**: `故障机理与传感器/阀体位置示意图 (如 N433-N440 电磁阀或 GPF 过滤层)`
  * **Image 02 (核心机制/流程)**: `标准化排查与电脑诊断读取流程图 (OBD/CAN 数据流读取指标)`
  * **Image 03 (维保/选型清单)**: `维修与配件替换方案对比图 (OE 原厂备件 vs 拆车件 vs 替代方案)`

### A-10. [Post ID: 448] Bambu Lab из Китая для России: что проверить по модели, LAN-режиму и поддержке до покупки

- **URL**: `https://fyzsxnb.com/bambu-lab-china-russia-pre-purchase-check/`
- **语言与分类**: `RU` | Product Research / 产品研究 (`B` 类)
- **综合评分 (Score)**: `19 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Product Teardown (硬件拆解) + Scenario Application (应用场景) + Sourcing Matrix (选型对比)`
- **补图立项理由**: 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `Product Studio Hero (产品主体高质感渲染/棚拍)`
  * **Image 01 (首屏技术概念)**: `核心元器件与内部硬件架构拆解图 (主控芯片、传感器与模组)`
  * **Image 02 (核心机制/流程)**: `真实工作场景与性能基准实测图 (温控、功耗、定位与响应实测)`
  * **Image 03 (维保/选型清单)**: `国行版 vs 海外版选型与兼容性核验卡 (网络锁、频段、电压与协议)`

### A-11. [Post ID: 466] FDA Foreign Drug Establishment Registration Guide

- **URL**: `https://fyzsxnb.com/fda-foreign-drug-establishment-registration-guide/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规 (`D` 类)
- **综合评分 (Score)**: `18 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **补图立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `Regulatory Authority / Laboratory Bench Graphic`
  * **Image 01 (首屏技术概念)**: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)`
  * **Image 02 (核心机制/流程)**: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
  * **Image 03 (维保/选型清单)**: `现场核查与质量体系审计清单 (GMP 审计、文件清单与合规核验)`

### A-12. [Post ID: 479] 国家药监局第21号公告UDI实施解读：2027二类器械与一类IVD赋码、数据库上传及医保代码对接（已纳入医保代码数据库的产品适用）

- **URL**: `https://fyzsxnb.com/nmpa-udi-2027-class2-devices-ivd-implementation-guide/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规 (`D` 类)
- **综合评分 (Score)**: `18 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **补图立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `Regulatory Authority / Laboratory Bench Graphic`
  * **Image 01 (首屏技术概念)**: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)`
  * **Image 02 (核心机制/流程)**: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
  * **Image 03 (维保/选型清单)**: `现场核查与质量体系审计清单 (GMP 审计、文件清单与合规核验)`

### A-13. [Post ID: 485] Бесплатное обновление Android Auto для Chery TIGGO 7 PRO и TIGGO 8 PRO: проверка доступности по комплектации

- **URL**: `https://fyzsxnb.com/chery-android-auto-obnovlenie-tiggo-7-8-pro/`
- **语言与分类**: `RU` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `18 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `System Diagram (系统架构) + Process Map (流程图) + Checklist (核验清单)`
- **补图立项理由**: 跨境进口与技术适配必经环节，高商业价值与转化前置情报
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `System Architecture / Official Process Banner`
  * **Image 01 (首屏技术概念)**: `硬件拓扑与总线连接图 (如 CAN 节点、相机线束或海关计算路径)`
  * **Image 02 (核心机制/流程)**: `关键参数核验与版本差异对比表 (如 EPTS 状态或智驾支持矩阵)`
  * **Image 03 (维保/选型清单)**: `实操步骤与安全自查清单 (安装指引或海关税费计算实例)`

### A-14. [Post ID: 372] HONOR Magic V6 для России: китайская версия против глобальной

- **URL**: `https://fyzsxnb.com/honor-china-vs-eu-version-russia-guide/`
- **语言与分类**: `RU` | Product Research / 产品研究 (`B` 类)
- **综合评分 (Score)**: `17 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Product Teardown (硬件拆解) + Scenario Application (应用场景) + Sourcing Matrix (选型对比)`
- **补图立项理由**: 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `Product Studio Hero (产品主体高质感渲染/棚拍)`
  * **Image 01 (首屏技术概念)**: `核心元器件与内部硬件架构拆解图 (主控芯片、传感器与模组)`
  * **Image 02 (核心机制/流程)**: `真实工作场景与性能基准实测图 (温控、功耗、定位与响应实测)`
  * **Image 03 (维保/选型清单)**: `国行版 vs 海外版选型与兼容性核验卡 (网络锁、频段、电压与协议)`

### A-15. [Post ID: 432] Утильсбор 2026: три пути ввоза авто из Китая в Россию

- **URL**: `https://fyzsxnb.com/utilization-fee-china-car-import-russia-2026/`
- **语言与分类**: `RU` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `16 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `System Diagram (系统架构) + Process Map (流程图) + Checklist (核验清单)`
- **补图立项理由**: 跨境进口与技术适配必经环节，高商业价值与转化前置情报
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `System Architecture / Official Process Banner`
  * **Image 01 (首屏技术概念)**: `硬件拓扑与总线连接图 (如 CAN 节点、相机线束或海关计算路径)`
  * **Image 02 (核心机制/流程)**: `关键参数核验与版本差异对比表 (如 EPTS 状态或智驾支持矩阵)`
  * **Image 03 (维保/选型清单)**: `实操步骤与安全自查清单 (安装指引或海关税费计算实例)`

### A-16. [Post ID: 509] China-Market Volkswagen Tayron 330TSI: Why DKV, DPL and DTH Matter Before You Order Parts

- **URL**: `https://fyzsxnb.com/china-market-volkswagen-tayron-330tsi-dkv-dpl-dth-parts/`
- **语言与分类**: `EN` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `16 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Component Teardown (部件解构) + Diagnostic Flow (诊断流程) + Repair Guide (方案)`
- **补图立项理由**: 解决俄罗斯车主高频遭遇的真实维修痛点，提升页面停留时间与技术权威度
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `Mechatronic / Engine Teardown (核心机械部件特写)`
  * **Image 01 (首屏技术概念)**: `故障机理与传感器/阀体位置示意图 (如 N433-N440 电磁阀或 GPF 过滤层)`
  * **Image 02 (核心机制/流程)**: `标准化排查与电脑诊断读取流程图 (OBD/CAN 数据流读取指标)`
  * **Image 03 (维保/选型清单)**: `维修与配件替换方案对比图 (OE 原厂备件 vs 拆车件 vs 替代方案)`

### A-17. [Post ID: 511] China-Market Volkswagen Tayron 330TSI GPF: What Chinese Owner Cases Actually Show

- **URL**: `https://fyzsxnb.com/china-market-volkswagen-tayron-330tsi-gpf-owner-cases/`
- **语言与分类**: `EN` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `16 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Component Teardown (部件解构) + Diagnostic Flow (诊断流程) + Repair Guide (方案)`
- **补图立项理由**: 解决俄罗斯车主高频遭遇的真实维修痛点，提升页面停留时间与技术权威度
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `Mechatronic / Engine Teardown (核心机械部件特写)`
  * **Image 01 (首屏技术概念)**: `故障机理与传感器/阀体位置示意图 (如 N433-N440 电磁阀或 GPF 过滤层)`
  * **Image 02 (核心机制/流程)**: `标准化排查与电脑诊断读取流程图 (OBD/CAN 数据流读取指标)`
  * **Image 03 (维保/选型清单)**: `维修与配件替换方案对比图 (OE 原厂备件 vs 拆车件 vs 替代方案)`

### A-18. [Post ID: 513] DQ381 Emergency Mode on the China-Market Volkswagen Tayron: What Real Owner Cases Show

- **URL**: `https://fyzsxnb.com/china-market-volkswagen-tayron-dq381-emergency-mode-owner-cases/`
- **语言与分类**: `EN` | Cars From China / 汽车 (`A` 类)
- **综合评分 (Score)**: `16 分` (Priority A)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Component Teardown (部件解构) + Diagnostic Flow (诊断流程) + Repair Guide (方案)`
- **补图立项理由**: 解决俄罗斯车主高频遭遇的真实维修痛点，提升页面停留时间与技术权威度
- **配图详细方案 (Per-Image Plan)**:
  * **Featured Image**: `Mechatronic / Engine Teardown (核心机械部件特写)`
  * **Image 01 (首屏技术概念)**: `故障机理与传感器/阀体位置示意图 (如 N433-N440 电磁阀或 GPF 过滤层)`
  * **Image 02 (核心机制/流程)**: `标准化排查与电脑诊断读取流程图 (OBD/CAN 数据流读取指标)`
  * **Image 03 (维保/选型清单)**: `维修与配件替换方案对比图 (OE 原厂备件 vs 拆车件 vs 替代方案)`

---

## 3. Priority B 列表 (深度技术与专题研究 — 第二批执行)

Priority B 文章包含具体产品的深度拆解、特定法规的技术指标分析及中俄选品实操，评分区间 9~15 分：

### B-1. [Post ID: 441] Китайский тест iFIND TBR: доказательства и требования России

- **URL**: `https://fyzsxnb.com/ru-ifind-tbr-evidence-russia-laboratory-guide/`
- **语言与分类**: `RU` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `15 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-2. [Post ID: 460] Cell and Gene Therapy BLA Readiness: What FDA&#8217;s OTP Town Hall Transcript Clarifies

- **URL**: `https://fyzsxnb.com/cgt-bla-readiness-otp-town-hall-guide/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `15 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-3. [Post ID: 362] Best Budget Robot Vacuum in 2026: A Durable Buyer&#8217;s Framework

- **URL**: `https://fyzsxnb.com/best-budget-robot-vacuum-2026-reddit-guide/`
- **语言与分类**: `EN` | Product Research / 产品研究
- **综合评分 (Score)**: `13 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Product Teardown (硬件拆解) + Scenario Application (应用场景) + Sourcing Matrix (选型对比)`
- **配图规划**: Featured: `Product Studio Hero (产品主体高质感渲染/棚拍)` | Img 1: `核心元器件与内部硬件架构拆解图 (主控芯片、传感器与模组)` | Img 2: `真实工作场景与性能基准实测图 (温控、功耗、定位与响应实测)`
- **立项理由**: 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱

### B-4. [Post ID: 405] Течь масла BMW N55: NBR, HNBR или FKM?

- **URL**: `https://fyzsxnb.com/ru-bmw-n55-oil-leak-gasket-fkm-nbr/`
- **语言与分类**: `RU` | Cars From China / 汽车
- **综合评分 (Score)**: `13 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Component Teardown (部件解构) + Diagnostic Flow (诊断流程) + Repair Guide (方案)`
- **配图规划**: Featured: `Mechatronic / Engine Teardown (核心机械部件特写)` | Img 1: `故障机理与传感器/阀体位置示意图 (如 N433-N440 电磁阀或 GPF 过滤层)` | Img 2: `标准化排查与电脑诊断读取流程图 (OBD/CAN 数据流读取指标)`
- **立项理由**: 解决俄罗斯车主高频遭遇的真实维修痛点，提升页面停留时间与技术权威度

### B-5. [Post ID: 415] Удалённая блокировка китайского электромобиля за границей: что проверить до покупки

- **URL**: `https://fyzsxnb.com/kitayskiy-elektromobil-udalennaya-blokirovka-eksport-risk/`
- **语言与分类**: `RU` | Cars From China / 汽车
- **综合评分 (Score)**: `13 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `System Diagram (系统架构) + Process Map (流程图) + Checklist (核验清单)`
- **配图规划**: Featured: `System Architecture / Official Process Banner` | Img 1: `硬件拓扑与总线连接图 (如 CAN 节点、相机线束或海关计算路径)` | Img 2: `关键参数核验与版本差异对比表 (如 EPTS 状态或智驾支持矩阵)`
- **立项理由**: 跨境进口与技术适配必经环节，高商业价值与转化前置情报

### B-6. [Post ID: 426] BMW N55 снова течет после замены прокладки: крышка, PCV или монтаж?

- **URL**: `https://fyzsxnb.com/bmw-n55-oil-leak-after-gasket-replacement/`
- **语言与分类**: `RU` | Cars From China / 汽车
- **综合评分 (Score)**: `13 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Component Teardown (部件解构) + Diagnostic Flow (诊断流程) + Repair Guide (方案)`
- **配图规划**: Featured: `Mechatronic / Engine Teardown (核心机械部件特写)` | Img 1: `故障机理与传感器/阀体位置示意图 (如 N433-N440 电磁阀或 GPF 过滤层)` | Img 2: `标准化排查与电脑诊断读取流程图 (OBD/CAN 数据流读取指标)`
- **立项理由**: 解决俄罗斯车主高频遭遇的真实维修痛点，提升页面停留时间与技术权威度

### B-7. [Post ID: 446] FDA 2026 CGT CMC Guidance: What Is Flexible and Required

- **URL**: `https://fyzsxnb.com/fda-2026-cgt-cmc-flexibilities-bla-guide/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `13 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-8. [Post ID: 447] Digitally Derived Endpoints: A Sponsor Readiness Checklist Before FDA&#8217;s August 2026 Workshop

- **URL**: `https://fyzsxnb.com/digitally-derived-endpoints-fda-workshop-readiness-checklist/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `13 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-9. [Post ID: 449] Model-Integrated Evidence for Generic Drugs: What an FDA Meeting Request Must Explain

- **URL**: `https://fyzsxnb.com/fda-mie-generic-drug-meeting-checklist/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `13 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-10. [Post ID: 463] FDA Global Generic Drug Affairs: Overseas Team Guide

- **URL**: `https://fyzsxnb.com/fda-global-generic-drug-affairs-overseas-teams-guide/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `13 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-11. [Post ID: 465] FDA Labeler Code Checklist for Foreign Companies

- **URL**: `https://fyzsxnb.com/fda-labeler-code-foreign-company-checklist/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `13 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-12. [Post ID: 487] FDA GUDID and AccessGUDID Procurement Verification Guide: DI Fields and Premarket Cross-Checks

- **URL**: `https://fyzsxnb.com/fda-gudid-accessgudid-procurement-verification-guide/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `13 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-13. [Post ID: 445] Как проверить регистрацию китайской IVD в России

- **URL**: `https://fyzsxnb.com/check-chinese-ivd-russia-registration-registry/`
- **语言与分类**: `RU` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `12 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-14. [Post ID: 398] AI Voice Recorders Compared: Subscription Cost, Privacy, and Offline Limits

- **URL**: `https://fyzsxnb.com/ai-voice-recorder-buying-guide-subscription-privacy-offline/`
- **语言与分类**: `EN` | Product Research / 产品研究
- **综合评分 (Score)**: `10 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Product Teardown (硬件拆解) + Scenario Application (应用场景) + Sourcing Matrix (选型对比)`
- **配图规划**: Featured: `Product Studio Hero (产品主体高质感渲染/棚拍)` | Img 1: `核心元器件与内部硬件架构拆解图 (主控芯片、传感器与模组)` | Img 2: `真实工作场景与性能基准实测图 (温控、功耗、定位与响应实测)`
- **立项理由**: 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱

### B-15. [Post ID: 431] Fully Automated Molecular POCT: iFIND S2/S4/S8 Procurement Guide

- **URL**: `https://fyzsxnb.com/fully-automated-molecular-poct-system-ifind-procurement-guide/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `10 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-16. [Post ID: 433] Разъём шланга мойки: как выбрать адаптер без ошибки

- **URL**: `https://fyzsxnb.com/pressure-washer-hose-connector-compatibility-guide/`
- **语言与分类**: `RU` | Product Research / 产品研究
- **综合评分 (Score)**: `10 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Product Teardown (硬件拆解) + Scenario Application (应用场景) + Sourcing Matrix (选型对比)`
- **配图规划**: Featured: `Product Studio Hero (产品主体高质感渲染/棚拍)` | Img 1: `核心元器件与内部硬件架构拆解图 (主控芯片、传感器与模组)` | Img 2: `真实工作场景与性能基准实测图 (温控、功耗、定位与响应实测)`
- **立项理由**: 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱

### B-17. [Post ID: 437] iFIND TBR MTB/RIF Cartridge: Evidence and Procurement Guide

- **URL**: `https://fyzsxnb.com/ifind-tbr-mtb-rif-cartridge-procurement-guide/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `10 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-18. [Post ID: 439] iFIND IFQ INH/FQ Cartridge: Evidence and Procurement Guide

- **URL**: `https://fyzsxnb.com/ifind-ifq-inh-fluoroquinolone-resistance-cartridge-guide/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `10 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-19. [Post ID: 450] GLP-1 Generic Development Is Not One Pathway: A Product-by-Product Pre-Workshop Checklist

- **URL**: `https://fyzsxnb.com/glp1-generic-development-pathway-checklist/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `10 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-20. [Post ID: 462] FDA Drug Registration and Listing: A Pre-Workshop Compliance Map for Manufacturers

- **URL**: `https://fyzsxnb.com/fda-drug-registration-listing-compliance-map/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `10 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-21. [Post ID: 480] FDA Establishment Registration Verification Guide

- **URL**: `https://fyzsxnb.com/fda-establishment-registration-device-listing-verification/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `10 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-22. [Post ID: 493] Pet Food Label: Find the Manufacturer and Check FDA Recalls

- **URL**: `https://fyzsxnb.com/pet-food-label-manufacturer-distributor-fda-recall-check/`
- **语言与分类**: `EN` | Biomed & Regulatory / 医药法规
- **综合评分 (Score)**: `10 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Regulatory Pathway (审评路径) + Analytical Validation (分析指标) + Audit Protocol (核验流程)`
- **配图规划**: Featured: `Regulatory Authority / Laboratory Bench Graphic` | Img 1: `法规审评与注册申报全景路径图 (FDA / NMPA / EAEU 阶段里程碑)` | Img 2: `技术指标与分析验证矩阵卡 (LoD 灵敏度、特异性、稳定性数据对比)`
- **立项理由**: 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视觉

### B-23. [Post ID: 226] How to Choose the Best Keepsake for Pet Fur to Honor Their Memory?

- **URL**: `https://fyzsxnb.com/how-to-choose-the-best-keepsake-for-pet-fur-to-honor-their-memory/`
- **语言与分类**: `EN` | Product Research / 产品研究
- **综合评分 (Score)**: `9 分` (Priority B)
- **推荐补图规格**: **3 张 (1 封面 + 2 插图)** | **视觉类型**: `Product Teardown (硬件拆解) + Scenario Application (应用场景) + Sourcing Matrix (选型对比)`
- **配图规划**: Featured: `Product Studio Hero (产品主体高质感渲染/棚拍)` | Img 1: `核心元器件与内部硬件架构拆解图 (主控芯片、传感器与模组)` | Img 2: `真实工作场景与性能基准实测图 (温控、功耗、定位与响应实测)`
- **立项理由**: 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱

### B-24. [Post ID: 424] 国家反诈中心AI内容鉴定怎么用？结果能证明什么

- **URL**: `https://fyzsxnb.com/national-anti-fraud-center-ai-content-identification-guide/`
- **语言与分类**: `EN` | Market & Supply Chain / 市场供应链
- **综合评分 (Score)**: `9 分` (Priority B)
- **推荐补图规格**: **2-3 张 (1 封面 + 1-2 插图)** | **视觉类型**: `Market Logic (大盘逻辑) + Supply Chain Flow (供应链图) + Cost Breakdown (成本结构)`
- **配图规划**: Featured: `Market Trend / Supply Chain Infographic` | Img 1: `品类市场需求与消费者意图分布图` | Img 2: `跨境供应链各级流通环节与交付路径图`
- **立项理由**: 揭示中国供应链出海与俄罗斯本地零售之间的价差与商业机会

---

## 4. Priority C 列表 (长尾信号与存量归档 — 暂缓执行)

Priority C 涵盖早期短讯快报、简讯信号或历史归档章节，评分 < 9 分，建议采用标准化轻量级配图模板暂缓执行：

| Post ID | 语言 | 标题 / Slug | 类别 | 当前图片 | 建议配图 | 优先级说明 |
|:---:|:---:|:---|:---|:---:|:---:|:---|
| **347** | `EN` | Kimi K3 为什么刷屏：知乎争议、2.8 万亿参数与开源模型的新问题... | Product Research  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| **349** | `EN` | Kimi K3 Explained: Why China&#8217;s 2.8T Ope... | Product Research  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| **350** | `RU` | Kimi K3: почему китайская открытая модель на ... | Product Research  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| **355** | `EN` | 小米米家智能冲牙器 Pro 开售：349 元定价背后的产品信号与选购框架... | Product Research  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| **388** | `EN` | 深圳生物医药特殊物品进出口机制：哪些环节真的变快了？... | Biomed & Regulatory  | 有封面, 0图 | 3 张 (1 封面 + 2 插图) | 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视... |
| **394** | `EN` | PLAUD 招聘基带工程师意味着什么：AI 耳机还是独立联网录音设备？... | Product Research  | 有封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| **411** | `EN` | 2026 中国西药出口拆解：制剂增长、GLP-1 原料药与新兴市场机会... | Biomed & Regulatory  | 有封面, 0图 | 3 张 (1 封面 + 2 插图) | 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视... |
| **442** | `RU` | Стартер и карбюратор бензокосы 43/52cc: прове... | Product Research  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| **443** | `EN` | TB Test LoD: What 10 vs 100 CFU/mL Really Mea... | Biomed & Regulatory  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视... |
| **444** | `EN` | 2026—2028年俄罗斯与EAEU医疗器械注册过渡期：中国IVD厂家路线图... | Biomed & Regulatory  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视... |
| **451** | `EN` | Casgevy for Children Aged 2 and Older: Direct... | Biomed & Regulatory  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视... |
| **461** | `EN` | GDUFA III Controlled Correspondence: Scope, S... | Biomed & Regulatory  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视... |
| **470** | `EN` | 村卫生室CRP与SAA联合POCT对抗生素处方率影响评估... | Biomed & Regulatory  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视... |
| **486** | `EN` | Tongue-Swab Tuberculosis Diagnostic Accuracy:... | Biomed & Regulatory  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 出海药企与医疗器械海外准入的高价值专业指南，建立国际化研报视... |
| **489** | `RU` | Как подобрать датчик давления 4-20 мА для час... | Product Research  | 有封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| **491** | `RU` | G1/4, R1/4 и NPT 1/4: резьба датчика давления... | Product Research  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| **492** | `RU` | Датчик давления 4-20 мА: WIKA A-10 и Danfoss ... | Product Research  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| **494** | `EN` | REDMI Kids Watch Pro China Version: Buyer&#82... | Product Research  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| **495** | `EN` | Huawei MatePad Pro 2026 China Announcement Bu... | Product Research  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| **390** | `RU` | Китайский корм для кошек в России: как сравни... | Market & Supply Chain  | 有封面, 0图 | 2-3 张 (1 封面 + 1-2 插图) | 揭示中国供应链出海与俄罗斯本地零售之间的价差与商业机会... |
| **434** | `RU` | Petmi против российских кормов: что доказывае... | Market & Supply Chain  | 无封面, 0图 | 2-3 张 (1 封面 + 1-2 插图) | 揭示中国供应链出海与俄罗斯本地零售之间的价差与商业机会... |
| **358** | `EN` | WAIC 2026 阶跃展台的信号：智能体手机、汽车与机器人，哪类先落地？... | Legacy Archive  | 无封面, 0图 | 2 张 (1 封面 + 1 插图) | 存量历史资产视觉规范化，确保全站归档风格一致性... |
| **290** | `EN` | Healing Crystal Cat Ornaments: A Guide to The... | Market & Supply Chain  | 有封面, 4图 | 2-3 张 (1 封面 + 1-2 插图) | 揭示中国供应链出海与俄罗斯本地零售之间的价差与商业机会... |
| **356** | `EN` | Pedigree Wet Dog Food Recall: What Owners Sho... | Market & Supply Chain  | 无封面, 0图 | 2-3 张 (1 封面 + 1-2 插图) | 揭示中国供应链出海与俄罗斯本地零售之间的价差与商业机会... |
| **361** | `EN` | Lickable Cat Supplements: How to Read Calming... | Market & Supply Chain  | 无封面, 0图 | 2-3 张 (1 封面 + 1-2 插图) | 揭示中国供应链出海与俄罗斯本地零售之间的价差与商业机会... |
| **490** | `EN` | Go Raw Pet Food Recall 2026: Lot Codes &#038;... | Market & Supply Chain  | 无封面, 0图 | 2-3 张 (1 封面 + 1-2 插图) | 揭示中国供应链出海与俄罗斯本地零售之间的价差与商业机会... |
| **239** | `EN` | How to Buy from Taobao Directly: A Step-by-St... | Legacy Archive  | 有封面, 11图 | 2 张 (1 封面 + 1 插图) | 存量历史资产视觉规范化，确保全站归档风格一致性... |
| **276** | `EN` | Shan Gui Hua Qian: The Ultimate Guide to This... | Legacy Archive  | 有封面, 2图 | 2 张 (1 封面 + 1 插图) | 存量历史资产视觉规范化，确保全站归档风格一致性... |
| **360** | `EN` | How to Choose a 27-Inch IPS Monitor Under CAD... | Product Research  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| **496** | `EN` | REDMAGIC Cooler 6 Pro+ China Launch: Buyer&#8... | Product Research  | 无封面, 0图 | 3 张 (1 封面 + 2 插图) | 跨境选品与硬件采购实证，帮助海外买家规避锁区与兼容性陷阱... |
| ... | ... | *(其余 25 篇长尾归档文章按通用模板暂缓排期)* | ... | ... | ... | ... |

---

## 5. 图像生产标准与四大品类设计规范 (Image Production Rules)

为彻底杜绝“千篇一律的黑色模板”，FYZSXNB 确立三大视觉风格体系与四大行业品类专属规范：

```text
+----------------------------------------------------------------------------------------------------+
|                                    三大视觉风格体系 (Visual Style Systems)                           |
+--------------------------+-----------------------------------+-------------------------------------+
| 风格体系                 | 适用业务领域                      | 视觉特征与设计语言                  |
+--------------------------+-----------------------------------+-------------------------------------+
| **Style 1: Automotive**  | 汽车 / 进口 / 维修 / 动力总成 / 智驾| 专业汽车媒体感、深蓝工业质感、部件  |
|                          | (Cars From China)                 | 特写、高对比参数图、拒绝营销广告风   |
+--------------------------+-----------------------------------+-------------------------------------+
| **Style 2: Research**    | 医药法规 / 市场研究 / 供应链宏观  | 权威研报质感、极地青翠/墨绿冷色调、 |
|                          | (Biomed & Market Intelligence)    | 结构化路径图、分析验证矩阵卡        |
+--------------------------+-----------------------------------+-------------------------------------+
| **Style 3: Product**     | 消费电子 / 工业硬件 / 跨境选品    | 商业硬件摄影感、星空紫/琥珀橙高亮、 |
|                          | (Product & Sourcing Signals)      | 核心芯片拆解、国行与海外版兼容矩阵  |
+--------------------------+-----------------------------------+-------------------------------------+
```

### 5.1 Category A: 汽车类规范 (Cars From China)
- **Overview 全景文章 (3张)**:
  * 图1: 车型 3/4 真实实景 / 高清外观 Hero，突出车型辨识度。
  * 图2: 中俄版本核心差异与空间轴距图 (如 2731mm vs 2678mm 平台对比)。
  * 图3: 动力总成、四驱结构或车机俄化适配流程图。
- **Comparison 对比文章 (3-4张)**:
  * 车型外观并列对比、动力/变速箱技术参数对比表、购买与税费建议清单。
- **维修 / 通病 / 案例文章 (3张)**:
  * 故障部件解构图 (如 DQ381 阀体或 GPF 滤芯)、电脑诊断与故障码读取流程、维修与零件更换方案。

### 5.2 Category B: 产品研究类规范 (Product Research)
- **规格要求 (2-3张)**:
  * 图1 (产品主体): 真实硬件主体、做工细节与接口特写。
  * 图2 (架构拆解): 主控芯片、传感器阵列与电气原理图。
  * 图3 (选型指南): 国行版 vs 海外版网络锁、频段与配件兼容矩阵。
  * *禁止行为*: 严禁使用毫无信息量的抽象“装饰图”。

### 5.3 Category C: 市场研究类规范 (Market Intelligence)
- **规格要求 (2张)**:
  * 图1: 市场供需逻辑图、价格带宽分布与目标客群画像。
  * 图2: 供应链流通节点图、海关申报与仓储物流路径。

### 5.4 Category D: 医疗与生物医药类规范 (Biomed & Regulatory)
- **规格要求 (2-3张)**:
  * 图1: 技术检测机制 / 药物作用靶点示意图。
  * 图2: 监管机构审评路径图 (FDA / NMPA / EAEU 申报节点)。
  * 图3: 分析验证指标表 (LoD、特异性、灵敏度) 或实验室使用流程。

---

## 6. 建议执行顺序与推进批次 (Execution Order)

```text
================================================================================
EXECUTION ROADMAP & ROLLOUT PHASES
================================================================================
Phase 1: Priority A 核心枢纽实施 (18 篇)
  ├── Step 1.1: Cars from China 核心 7 篇 (Tayron 系列 + BYD OpenPilot + EPTS)
  ├── Step 1.2: 俄语高搜索选品 4 篇 (拓竹 3D 打印机 + 荣耀手机 + 报废税)
  └── Step 1.3: 核心医药法规 7 篇 (NMPA UDI + FDA 境外注册 + POCT 系统)

Phase 2: Priority B 深度专题与产品拆解 (24 篇)
  ├── Step 2.1: 智能硬件与工业配件 10 篇 (红魔散热器 + 压力传感器 + 变频器)
  ├── Step 2.2: 医药与体外诊断 8 篇 (GLP-1 仿制药 + 结核快检 + 281 号令)
  └── Step 2.3: 宠物食品与跨境供应链 6 篇 (微塑料检测 + 猫粮成分对比)

Phase 3: Priority C 长尾存量归档 (55 篇)
  └── 采用标准化轻量级卡片批处理，保障全站 100% 基础视觉一致性。
================================================================================
```

---

## 最终交付状态

```text
LEGACY_IMAGE_PLAN_COMPLETE

TOTAL_PLANNED_ARTICLES:
97

TIER_BREAKDOWN:
- Priority A (Immediate): 18 Articles
- Priority B (Second Wave): 24 Articles
- Priority C (Backlog): 55 Articles

VISUAL_SYSTEMS_DEFINED:
- Style 1: FYZSXNB Automotive Intelligence
- Style 2: FYZSXNB Research Intelligence
- Style 3: FYZSXNB Product Intelligence

STATUS_DIRECTIVE:
PLANNING COMPLETE (Awaiting Execution Authorization)

STOP
```
