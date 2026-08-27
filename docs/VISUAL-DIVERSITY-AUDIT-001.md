# FYZSXNB — 视觉多样性审计与 Visual System 2.0 体系构建报告 (Visual Diversity Audit 001)

**文档编号:** `FYZ-DOC-20260821-VISUAL-DIVERSITY-AUDIT-001`  
**任务编号:** `FYZSXNB-VISUAL-DIVERSITY-AUDIT-001`  
**执行角色:** Google Gemini Flash 3.7  
**审计对象:** Batch 1 (9 张) 与 Batch 2-A (18 张) 共 27 张已上线视觉资产  
**阶段状态:** `VISUAL_DIVERSITY_AUDIT_COMPLETE` (审计完成，建立 Visual System 2.0 五大多元内容模板与反同质化规则，等待生成授权)  

---

## 1. 当前视觉同质化问题根因分析 (Current Visual Problems)

```text
================================================================================
CRITICAL PROBLEM: MONOLITHIC BLUEPRINT CARD SYNDROME (单一暗色工程卡片综合症)
================================================================================
在 Homepage 开启缩略图展示后，Latest Signals 与 Latest Guides 暴露出严重的视觉同质化：
1. 视觉模板单一: 全站无论手机评测、变速箱维修、报废税政策还是 FDA 药企合规，100% 采用：
   - 深色背景 (#0f172a / #1e293b)
   - 居中三等分模块 / 左右卡片容器
   - 顶部青色 (Cyan) / 翡翠绿 (Emerald) 栏目标签
   - 底部 FYZSXNB 品牌水印
2. 领域不可区分: 用户无法通过封面一秒识别文章性质（选车、买机、看政策还是修零件混淆不清）。
3. 首页视觉疲劳: 连续 4 篇文章卡片均为暗黑信息图，导致网站缺乏“真实商业媒体/专业智库”的生动感，
   被误认为单一的“汽车技术极客小站”。
4. 点击欲望压抑: 手机、汽车等消费属性强的内容缺乏真实摄影主图，降低读者的点击探索欲。
================================================================================
```

---

## 2. 27 张现有生产资产相似度解构 (Asset Similarity Analysis)

| 资产批次 | 资产文件名 | 关联文章 ID | 当前视觉特征 | 存在的问题诊断 |
|:---|:---|:---:|:---|:---|
| **Batch 1** | `volkswagen-tayron-mqb-platform-hero.jpg` | 640 | 深蓝底 + 3 模块架构卡 | ❌ 作为整车介绍首图过于抽象，缺少实车实景感染力 |
| **Batch 1** | `tayron-engine-transmission-matrix.jpg` | 640 | 深灰底 + 动力矩阵表 | ✅ 正文插图契合度高，保留作为正文技术图表 |
| **Batch 1** | `tayron-buyer-inspection-checklist.jpg` | 640 | 深灰底 + 4 步验车流 | ✅ 正文插图契合度高，保留作为正文核验图表 |
| **Batch 1** | `byd-frigate-07-openpilot-hero.jpg` | 504 | 深色底 + 智驾硬件拆解 | ⚠️ 偏工程风，适合智驾技术文，但缺少实车座舱透视 |
| **Batch 1** | `byd-can-fd-wiring-diagram.jpg` | 504 | 黑色底 + CAN-FD 拓扑 | ✅ 纯硬核线束拓扑，正文工程插图定位完美 |
| **Batch 1** | `bambu-lab-h2d-hotend-extruder-hero.jpg` | 448 | 深蓝底 + 挤出机拆解 | ❌ 消费级 3D 打印机选品首图更需要整机实拍与打印场景 |
| **Batch 1** | `bambu-lab-china-activation-checklist.jpg` | 448 | 深灰底 + 激活步骤卡 | ✅ 正文插图定位准确，保留 |
| **Batch 1** | `russia-utilization-fee-2026-calculation-hero.jpg` | 432 | 深蓝底 + 政策公式卡 | ⚠️ 宏观关税政策首图应采用白底/暖灰智库研究图表风 |
| **Batch 1** | `nmpa-udi-2027-compliance-timeline-hero.jpg` | 479 | 深灰底 + UDI 时间线 | ⚠️ 医疗法规更适合简洁严谨的白底医疗合规路线图 |
| **Batch 2-A** | `epts-vin-verification-portal-hero.jpg` | 484 | 深蓝底 + elpts 界面卡 | ❌ 首图缺少俄罗斯海关/车管所提车验真真实感 |
| **Batch 2-A** | `epts-verification-process-flowchart.jpg` | 484 | 深灰底 + 验证流程图 | ✅ 正文插图流程清晰，保留 |
| **Batch 2-A** | `epts-buyer-four-point-checklist.jpg` | 484 | 深灰底 + 4 点避坑清单 | ✅ 正文插图结构严谨，保留 |
| **Batch 2-A** | `volkswagen-tayron-dq381-mechatronic-hero.jpg` | 514 | 深蓝底 + 阀体爆炸拆解 | ✅ 变速箱故障技术文，极度契合 Template A 工程风 |
| **Batch 2-A** | `volkswagen-dq381-sensor-failure-diagram.jpg` | 514 | 黑色底 + 传感器回路 | ✅ 正文硬核传感器诊断图，保留 |
| **Batch 2-A** | `volkswagen-dq381-repair-service-workflow.jpg` | 514 | 深灰底 + ODIS 维修流程 | ✅ 正文维修工单图，保留 |
| **Batch 2-A** | `chery-tiggo-headunit-firmware-update-hero.jpg` | 485 | 深蓝底 + 车机刷机卡 | ⚠️ 首图可升级为实拍车机中控屏幕 CarPlay 界面 + ADB 提示 |
| **Batch 2-A** | `chery-headunit-usb-adb-installation-flow.jpg` | 485 | 深灰底 + USB 刷机步骤 | ✅ 正文插图定位准确，保留 |
| **Batch 2-A** | `chery-tiggo-mcu-soc-version-matrix.jpg` | 485 | 深灰底 + 芯片矩阵 | ✅ 正文插图定位准确，保留 |
| **Batch 2-A** | `honor-china-vs-global-russia-buyer-hero.jpg` | 420 | 深蓝底 + 15 项手机卡 | ❌ 手机选品首图应为真机实拍 (Magic 系列) + 俄罗斯频段标贴 |
| **Batch 2-A** | `honor-lte-5g-bands-compatibility-matrix.jpg` | 420 | 深灰底 + 频段对比表 | ✅ 正文技术插图定位准确，保留 |
| **Batch 2-A** | `honor-china-buyer-15-point-checklist.jpg` | 420 | 深灰底 + 15 项验机卡 | ✅ 正文插图定位准确，保留 |
| **Batch 2-A** | `pressure-transmitter-4-20ma-vfd-wiring-hero.jpg` | 489 | 深灰底 + 压力传感器卡 | ⚠️ 首图可升级为工业现场传感器 + 控制柜变频器实拍 |
| **Batch 2-A** | `pressure-sensor-two-wire-loop-wiring-diagram.jpg` | 489 | 黑色底 + 2 线制电路图 | ✅ 工业电路原理图，正文完美适用 |
| **Batch 2-A** | `industrial-pressure-thread-types-matrix.jpg` | 489 | 深灰底 + 螺纹剖面图 | ✅ 正文螺纹选型剖面图，保留 |
| **Batch 2-A** | `fda-foreign-drug-registration-roadmap-hero.jpg` | 466 | 深蓝底 + FDA 5 步流程卡 | ⚠️ 医疗首图应使用专业洁净蓝白调 + 药品监管证书质感 |
| **Batch 2-A** | `fda-drug-establishment-registration-process-flow.jpg` | 466 | 深灰底 + DUNS/SPL 流 | ✅ 正文合规步骤图，保留 |
| **Batch 2-A** | `fda-cder-annual-renewal-compliance-timeline.jpg` | 466 | 深灰底 + 年审时间轴 | ✅ 正文合规时间轴，保留 |

---

## 3. FYZSXNB Visual System 2.0 体系定义

Visual System 2.0 确立 **5 种独立内容视觉模板**，彻底打破单一工程卡片范式：

```text
================================================================================
VISUAL SYSTEM 2.0 TEMPLATE MATRIX
================================================================================
┌─────────────┬────────────────────────────────┬─────────────────┬────────────────────────────┐
│ 模板编号    │ 模板名称 (Template Name)       │ 视觉基调 (Base) │ 核心构图与表现手法         │
├─────────────┼────────────────────────────────┼─────────────────┼────────────────────────────┤
│ Template A  │ Automotive Engineering         │ 深色科技蓝/碳灰 │ CAD 爆炸图 / 线束拓扑 / 示波器波形 / 零件回路 │
│ Template B  │ Vehicle Market Intelligence    │ 真实道路/展厅实景│ 真实实车摄影 + 地图/VIN/通关徽章轻量浮层    │
│ Template C  │ Product Intelligence (3C/工业) │ 摄影棚/极简浅灰 │ 真实产品高光特写 + 浮动规格胶囊/防坑检查点 │
│ Template D  │ Market Research & Policy       │ 智库暖白/浅米色 │ 权威研究图表 / 条形统计图 / 宏观政策里程碑 │
│ Template E  │ Biomed / Clinical / Regulation │ 医疗纯白/高冷蓝 │ 实验室场景 / 合规证书 / 严谨药典与 FDA 流程│
└─────────────┴────────────────────────────────┴─────────────────┴────────────────────────────┘
```

### Template A: Automotive Engineering (汽车技术与故障诊断)
- **适用场景**: 变速箱维修 (DQ381)、ECU/MCU 固件逆向、智驾 CAN-FD 硬件抓包、传感器故障定位。
- **视觉风格**: 深色工程底 (`#090d16` / `#0f172a`)，高精度矢量线框、电路针脚定义、故障代码 (DTC) 标签。
- **禁止滥用**: 禁止用于整车购买介绍、二手车验车或进口政策文章。

### Template B: Vehicle Market Intelligence (车型市场与进口选车)
- **适用场景**: 车型全面评测 (Volkswagen Tayron / BYD / Geely)、EPTS 验真、中俄汽车平行进口指南。
- **视觉风格**: **真实车辆摄影**（冬雪场景、俄罗斯公路、4S 展厅、中控实景）作为背景核心，叠加中俄双语车型标牌、VIN/EPTS 验真角标、MQB/e-Platform 平台徽章。
- **禁止反模式**: 严禁整车购买首图使用纯黑底流程图方框。

### Template C: Product Intelligence (消费电子与工业硬件)
- **适用场景**: 智能手机 (HONOR / Xiaomi)、3D 打印机 (Bambu Lab)、压力变送器、AI 录音卡。
- **视觉风格**: **高质感产品实拍特写**（金属质感、按键细节、屏幕亮屏 UI），浅色/灰度科技底，配合悬浮的“15 项验机清单”、“Band 20 缺失警告”、“G1/4 螺纹规格”等透明磨砂标签。

### Template D: Market Research & Policy (宏观政策与产业研究)
- **适用场景**: 俄罗斯报废税 (Utilization Fee)、中俄清关报关、全球供应链风险、市场需求调研。
- **视觉风格**: 权威智库与彭博研报风，浅米白/浅灰底 (`#f8fafc` / `#f1f5f9`)，高对比度双色柱状图、关税递进阶梯图、中俄贸易路线地图。

### Template E: Biomed / Clinical / Regulation (生物医药与全球合规)
- **适用场景**: FDA 境外药企注册 (21 CFR 207)、NMPA UDI 2027、IVD POCT 试剂盒采购评估。
- **视觉风格**: 国际医药期刊与临床标准风，洁净白底 (`#ffffff`) 配医疗蓝 (`#0284c7`)，包含 FDA 官方徽记风格、FEI 注册码图章、合规里程碑时间轴。

---

## 4. 27 张现有资产分类与留存决策 (Asset Reclassification)

```text
================================================================================
RECLASSIFICATION DECISION MATRIX (27 ASSETS)
================================================================================
- 保持原样继续使用 (RETAIN AS-IS): 17 张 (全部为正文专业插图与符合 Template A 的工程图)
- 降级为正文专业插图 (REASSIGN TO BODY): 4 张 (原首图退回正文作为技术对照图)
- 优先重新生成高质量首图 (PRIORITY REPRODUCTION): 6 张 (首页 Featured Image 全面升级)
================================================================================
```

### 分类处置明细表：
1. **Post 640 (Volkswagen Tayron Overview)**:
   - `volkswagen-tayron-mqb-platform-hero.jpg` -> **降级为正文图**
   - **新增重做**: `tayron-exterior-market-intelligence-hero.jpg` (Template B: 探岳实车在雪地/公路实景 + 2.0T/MQB A2 标牌)
2. **Post 484 (EPTS VIN Verification)**:
   - `epts-vin-verification-portal-hero.jpg` -> **降级为正文图**
   - **新增重做**: `epts-customs-vin-verification-hero.jpg` (Template B: 俄海关进口查验实景 + 17 位 VIN / elpts 验真标章)
3. **Post 420 (HONOR China Version)**:
   - `honor-china-vs-global-russia-buyer-hero.jpg` -> **降级为正文图**
   - **新增重做**: `honor-magic-russia-buyer-product-hero.jpg` (Template C: 荣耀 Magic 真机实拍 + Band 20 / Google 状态指示)
4. **Post 448 (Bambu Lab 3D Printer)**:
   - `bambu-lab-h2d-hotend-extruder-hero.jpg` -> **降级为正文图**
   - **新增重做**: `bambu-lab-printer-studio-product-hero.jpg` (Template C: 拓竹 3D 打印机工作室实机工作实拍 + 锁区避坑浮层)
5. **Post 432 (Russia Utilization Fee 2026)**:
   - `russia-utilization-fee-2026-calculation-hero.jpg` -> **新增重做** (Template D: 智库研报浅色风阶梯关税图)
6. **Post 466 (FDA Foreign Drug Registration)**:
   - `fda-foreign-drug-registration-roadmap-hero.jpg` -> **新增重做** (Template E: 蓝白纯净药企合规首图)

---

## 5. Featured Image 核心规则 (Featured Image Hierarchy Rules)

```text
================================================================================
FEATURED IMAGE 3-TIER HIERARCHY
================================================================================
┌──────────────────────────────────────────────────────────────────────────────┐
│ Priority 1 (最高优先级): 真实主体 / 场景摄影 (Real Subject Photography)      │
│ - 汽车类: 实车道路行驶、外观特写、中控实景                                   │
│ - 硬件类: 真实产品摄影、工业现场安装实况                                     │
│ - 医药类: 真实实验室设备、药典检测现场                                       │
├──────────────────────────────────────────────────────────────────────────────┤
│ Priority 2 (次级选择): 概念视觉与场景复合图 (Conceptual Key Visual)           │
│ - 地图与物流路线合成、通关单据与实物结合、宏观趋势概念图                     │
├──────────────────────────────────────────────────────────────────────────────┤
│ Priority 3 (降级备选): 结构化信息卡 / 数据图 (Infographic / Chart)           │
│ - 仅用于纯抽象算法、法规政策对比、DTC 故障排查等无实体对应的话题              │
└──────────────────────────────────────────────────────────────────────────────┘
```

---

## 6. 首页 Feed 连续卡片防碰撞与多样性规则 (Feed Diversity Rules)

为彻底消灭视觉疲劳，今后在首页 Latest Signals 与 Latest Guides 的连续 4 篇文章编排中，必须遵守 **“相邻卡片模板互斥准则 (Adjacent Template Exclusion)”**：

```text
================================================================================
HOMEPAGE FEED RHYTHM BLUEPRINT (首页健康视觉节奏示例)
================================================================================
[Card 1] Template B (Vehicle Market): 探岳实车在公路驰骋实拍 (实景/冷调自然光)
         ↓ (切换视觉形态)
[Card 2] Template A (Auto Engineering): DQ381 阀体 CAD 爆炸拆解 (硬核深色工程风)
         ↓ (切换视觉形态)
[Card 3] Template C (Product Tech): 荣耀 Magic 旗舰机金属机身实拍 (棚拍/科技浅灰)
         ↓ (切换视觉形态)
[Card 4] Template D (Market Research): 报废税 2026 政策阶梯对比图 (权威智库暖白底)
================================================================================
```

---

## 7. 最终交付状态

```text
VISUAL_DIVERSITY_AUDIT_COMPLETE

AUDITED_ASSETS:
27 Total Production Assets across Batch 1 & Batch 2-A

ESTABLISHED_FRAMEWORK:
FYZSXNB Visual System 2.0 (Template A / B / C / D / E)

REPRODUCTION_CANDIDATES:
6 Core Featured Images earmarked for photography-led / domain-native redesign

DELIVERABLE_REPORT:
docs/VISUAL-DIVERSITY-AUDIT-001.md

STATUS_DIRECTIVE:
AUDIT & BLUEPRINT COMPLETE (No assets deleted or overwritten. Ready for Batch 2-B/Redesign execution.)

STOP
```
