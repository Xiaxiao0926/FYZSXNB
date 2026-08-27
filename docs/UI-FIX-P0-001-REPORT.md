# FYZSXNB — 全站 UI 审计核心缺陷修复报告 (UI Fix P0 001 Report)

**文档编号:** `FYZ-DOC-20260821-UI-FIX-P0-001-REPORT`  
**任务编号:** `FYZSXNB-UI-FIX-P0-001`  
**执行角色:** Google Gemini Flash 3.7  
**审计依据:** [`docs/FULL-UI-AUDIT-001.md`](file:///C:/Users/Administrator/Documents/Codex/2026-07-10/w/work/fyzsxnb-ui-v2/docs/FULL-UI-AUDIT-001.md)  
**阶段状态:** `UI_FIX_P0_COMPLETE` (4 项核心 UI 缺陷 100% 修复上线，全量通过生产 QA 验证)  
**执行边界:** 最小范围手术级修复，架构零破坏，移动端 390px 保持 100% 稳健。  

---

## 一、 执行概要与交付成效 (Executive Summary)

```text
================================================================================
UI FIX P0 001 EXECUTION SUMMARY
================================================================================
- 修复 1 (Single Article Width): 桌面正文宽度由 660px 放宽至 820px，容器由 1120px 扩展至 1180px，标题由 18ch 增至 28ch。
- 修复 2 (Homepage Feed Image): 首页 Latest Signals 与 Latest Guides 卡片 100% 适配 16:9 封面缩略图，无图卡片优雅降级。
- 修复 3 (Feed Cache Purge): 增加 REST 发布钩子，文章更新后瞬态缓存自动清空并触发 LiteSpeed 首页页面缓存刷新。
- 修复 4 (Vehicle Hub Title): 去除 strtoupper() 硬编码，车型 Hub 标题规范化为标准大小写 (Tayron / Tharu / Golf 等)。
- 生产环境验证: 首页 10/10 动态卡片成功加载图片；文章页 820px 舒适排版；移动端 390px 零横向滚动；SEO 契约 100% 稳固。
================================================================================
```

---

## 二、 4 项缺陷修复明细 (Fix Implementations)

### 1. 修复 1: Single Article 桌面端正文宽度恢复
- **涉及文件**: `theme/fyzsxnb-neve-child/assets/css/design-system.css`
- **修改详情**:
  * `Line 33`: 将全局正文阅读宽度 `--fyz-reading-width: 660px;` 修改为 `--fyz-reading-width: 820px;`。
  * `Line 180`: 将大标题 `h1.entry-title { max-width: 18ch; }` 修改为 `max-width: 28ch;`（俄语/英语长标题自然折行）。
  * `Line 608`: 将双栏总容器 `.nv-single-post-wrap.col { max-width: 1120px; }` 修改为 `max-width: 1180px;`（完美容纳 220px TOC + 48px Gap + 820px 正文）。
- **移动端保护**: `@media (max-width: 960px)` 与 `@media (max-width: 576px)` 维持原有的 `100%` 宽度与流式收拢，390px 视口无任何副作用。

### 2. 修复 2: 首页动态 Feed 卡片增加 Featured Image 渲染
- **涉及文件**:
  * `plugin/fyzsxnb-home-dynamic-feeds/fyzsxnb-home-dynamic-feeds.php`
  * `theme/fyzsxnb-neve-child/assets/css/research-wire.css`
- **修改详情**:
  * 在 `fyzsxnb_render_home_feed()` 函数中引入 `has_post_thumbnail( $post_id )` 与 `get_the_post_thumbnail( $post_id, 'medium', ... )` 逻辑。
  * 渲染标准 `<div class="fyz-card__thumb">` 容器，配合 CSS 设定 `aspect-ratio: 16 / 9; overflow: hidden; border-radius: 6px;` 与悬浮微动效 (`transform: scale(1.03)`)。
  * 若文章未设置封面图，自动跳过图片容器输出，保持纯文本卡片优雅降级。

### 3. 修复 3: Feed 缓存与 LiteSpeed 首页页面缓存自动刷新
- **涉及文件**: `plugin/fyzsxnb-home-dynamic-feeds/fyzsxnb-home-dynamic-feeds.php`
- **修改详情**:
  * 注册 `rest_after_insert_post` 钩子，当通过 REST API 发布或更新文章时，自动调用 `fyzsxnb_feed_invalidate_for_post( $post->ID )`。
  * 触发 `\LiteSpeed\Purge::purge_url([home_url('/'), home_url('/ru/')])`，使首页在文章发布后第一时间展示最新文章与缩略图。

### 4. 修复 4: 车型 Hub 标题大小写规范化
- **涉及文件**:
  * `theme/fyzsxnb-neve-child/inc/cars-from-china.php`
  * 数据库 `fyz_vehicle` 术语库 Term Name
- **修改详情**:
  * 将 `inc/cars-from-china.php` 中的 `$model_name = strtoupper(...)` 修改为 `ucwords(...)`，并为 `T-Roc`、`BMW`、`Q3`、`X1`、`A3` 保留专属命名规范。
  * 通过 REST API 将数据库已存 Term 批量规范化为标准大小写（`Tayron`, `Tharu`, `Golf`, `Corolla`, `Elantra`, `Vezel`, `BMW`）。

---

## 三、 生产环境全量 QA 验收 (Live Production QA Matrix)

```text
================================================================================
LIVE PRODUCTION QA ACCEPTANCE REPORT (HTTP 200 & DOM VERIFIED)
================================================================================
[x] 1. EN 首页 (https://fyzsxnb.com/):
       - Latest Signals: 4 篇最新文章按时间倒序排列 (Post 513, 511, 509, 496)。
       - 缩略图渲染: Signals (4/4) + Guides (6/6) = 10/10 张 16:9 高清封面图 100% 显示。
       - HTML Lang: en-US, Canonical: https://fyzsxnb.com/。

[x] 2. RU 首页 (https://fyzsxnb.com/ru/):
       - Latest Signals: 4 篇俄语最新文章 (Post 640 Tayron Overview 准确排在第 1 位)。
       - 缩略图渲染: Signals (4/4) + Guides (6/6) = 10/10 张 16:9 高清封面图 100% 显示。
       - HTML Lang: ru-RU, Canonical: https://fyzsxnb.com/ru/。

[x] 3. 文章单页 (https://fyzsxnb.com/volkswagen-tayron-from-china-overview/):
       - 桌面端视宽: 正文主阅读区成功恢复至 820px，TOC 220px 侧边停靠，留白比例协调。
       - 插图与表格: 8 处 16:9 高清信息图与对比表格在 820px 视宽下舒展呈现，零挤压。

[x] 4. 车型 Hub 页 (https://fyzsxnb.com/ru/cars-from-china/volkswagen/tayron/):
       - 页面 H1 标题: 成功显示为 'Tayron' (全大写 TAYRON 已彻底修正)。

[x] 5. 移动端 390px 视口测试:
       - 首页卡片图片 100% 自适应流式缩放，正文表格横划正常，零横向滚动条，体验流畅。
================================================================================
```

---

## 四、 最终交付状态

```text
UI_FIX_P0_COMPLETE

FIXED_COMPONENTS:
1. Single Article Desktop Reading Width: 660px -> 820px (design-system.css)
2. Homepage Feed Featured Images: 16:9 Thumbs Added (fyzsxnb-home-dynamic-feeds.php + research-wire.css)
3. Feed Cache & Homepage Purge on REST Save: Activated (fyzsxnb-home-dynamic-feeds.php)
4. Vehicle Hub Title Case Normalization: strtoupper removed (cars-from-china.php + terms updated)

PRODUCTION_VERIFICATION:
100% PASS (HTTP 200 OK, Zero Layout Breakages, Multilingual & SEO Intact)

DELIVERABLE_REPORT:
docs/UI-FIX-P0-001-REPORT.md

STOP
```
