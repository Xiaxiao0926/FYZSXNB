# FYZSXNB 前端审计报告（FRONTEND-AUDIT-20260818）

> 审计对象：`fyzsxnb-ui-v2` 包内全部前端文件（19 个必读文件，另含包内 RESULT.md / BASELINE.json 上下文）。
> 生产方式：生产站点 https://fyzsxnb.com（Neve 父主题 + 子主题 0.3.2，LiteSpeed Cache，PHP 8.2）。design-system.css 与 cars-from-china.css 已部署；research-wire.css/js 未部署。
> 审计口径：所有论断均来自文件实际内容（文件名 + 行号），不确定处标注 **需核实**。
> 约束红线：只能改子主题；不能动父主题 / Elementor / React / 新 CMS；不能破坏 SEO 契约（lang / canonical / robots / single H1 / category 54）。

---

## 1. 前端架构总览

### 1.1 文件职责清单

| 文件 | 职责 | 备注 |
|---|---|---|
| `theme/fyzsxnb-neve-child/style.css` | 仅主题头（Theme Name / Template: neve / Version **0.3.2**） | 无任何 CSS 规则，样式全部在 design-system.css |
| `theme/fyzsxnb-neve-child/functions.php` | 子主题引导：加载 inc 模块、继承父主题 theme_mods、按版本清缓存、RU 视图检测、三级样式入队（design-system → research-wire → cars-from-china）、body class、byline 替换、归档 intro | 渲染入口 L16、L78-142 |
| `theme/fyzsxnb-neve-child/404.php` | 自定义 404 模板（搜索框 + 三个静态链接） | L11-21 |
| `theme/fyzsxnb-neve-child/assets/css/design-system.css` | 全站设计 token + 文章阅读系统 + 归档/404/contact 页 + 移动断点 | 已部署 |
| `theme/fyzsxnb-neve-child/assets/css/research-wire.css` | V2 "Wire Desk" 呈现层：首页（.fyz-home）、locale header、归档索引化、TOC、callout 样式 | **未部署**（快照） |
| `theme/fyzsxnb-neve-child/assets/js/research-wire.js` | 文章 H2/H3 → 目录（details/summary），仅 single post 加载 | **未部署**（快照） |
| `theme/fyzsxnb-neve-child/assets/css/cars-from-china.css` | CFC desk 作用域样式（matrix/card/crumbs/CTA/响应式） | 已部署 |
| `theme/fyzsxnb-neve-child/inc/cars-from-china.php` | CFC 内容架构：两个分类法、rewrite、RU 语言契约、hub/brand/model 渲染、launch-gate robots | 已部署（0.1.0） |
| `theme/fyzsxnb-neve-child/page-templates/cars-from-china-hub.php` | CFC hub 页面模板（EN 页模板；RU hub 经 template_include 复用） | 已部署 |
| `theme/fyzsxnb-neve-child/taxonomy-fyz_vehicle.php` | fyz_vehicle 归档模板：parent→brand 页，child→model 页 | 已部署 |
| `mu-plugins/fyzsxnb-p0-seo-patch.php` | P0 SEO：RU 语言/OG/schema（AIOSEO）、四个 hub 动态 feed 短代码+注入、首页 hreflang、blog H1 | 生产快照（1.3.1），**不在可改范围** |
| `plugin/fyzsxnb-home-dynamic-feeds/fyzsxnb-home-dynamic-feeds.php` | 首页 signals/guides 双语言动态 feed（marker 替换 + 缓存失效） | 生产快照（1.0.0），**不在可改范围** |
| `preview/home-en.html` / `home-ru.html` | 首页静态预览：模拟"页面内容 = Custom HTML 块（内联 CSS + 硬编码区块 + feed marker）"的渲染结果 | 是**页面内容形态的直接证据** |
| `preview/cars-from-china-model-preview.html` | CFC model 页 1440/1024/768/390 视觉预览（noindex mock） | QA 用途 |

### 1.2 渲染链路

```
首页 EN（page 11）/ RU（page 400）
  WP page → Neve single-page 模板（子主题只加 body class，functions.php:150-157）
  → 页面内容 = 巨型 Custom HTML 块：内联 <style> + 硬编码区块 + feed marker（preview/home-en.html:1、home-ru.html:1 的 <!-- wp:html --> 即证据）
  → the_content 过滤器链：
     · fyzsxnb-home-dynamic-feeds（:103-125）用正则替换 <!-- fyzsxnb-home-feed:start locale=xx type=yy --> ... <!-- end --> 为动态网格
     · mu-plugin fyzsxnb_inject_hub_feed_into_pages（mu-plugin:485-516）只对四个 hub 页（329/328/327/330）注入 [fyzsxnb_hub_feed]
  → 语言：mu-plugin 按 RU ID 表 / category 54 改 lang=ru-RU、og:locale、schema inLanguage（mu-plugin:101-251）
  → 样式：neve-style → design-system.css（已部署）→ research-wire.css（未部署，生产当前只吃前两层）

文章页（single post）
  Neve 模板 + design-system.css 单文章系统（1200px 容器、760px 阅读列、48px 标题、18px/1.78 正文，design-system.css:106-190）
  + research-wire.js：≥2 个非空 H2/H3 时生成 TOC 并插入 .entry-header 之后（research-wire.js:11-62）

归档 / 搜索
  Neve archive 模板 + 子主题 neve_before_loop 注入 .fyz-archive-intro（functions.php:182-200）
  + design-system.css 归档样式（:249-297）+ research-wire.css 归档索引化（150px 缩略图行，research-wire.css:276-309）

CFC hub（/cars-from-china/ EN 页模板；/ru/cars-from-china/ 虚拟页）
  rewrite（cars-from-china.php:220-227）→ template_include（:236-245）→ cars-from-china-hub.php → fyzsxnb_cfc_render_hub()（:533-601）

CFC brand / model（/cars-from-china/{brand}/、/{brand}/{model}/，RU 带 /ru 前缀）
  rewrite → taxonomy-fyz_vehicle.php（:21-25 按 term->parent 分派）→ fyzsxnb_cfc_render_brand() / fyzsxnb_cfc_render_model()

404
  404.php：fyz-error-shell + get_search_form + 三个静态链接（404.php:11-21）

Blog 页（page 18）
  mu-plugin 保证 single H1 "FYZSXNB Blog"（mu-plugin:618-657，neve_before_posts_loop + loop_start 双保险）
```

### 1.3 动态 vs 静态分布

**动态（由查询/数据驱动）**
- 首页 signals / guides 网格（feeds 插件，仅 page 11 / 400 的 marker 区域，插件:103-125）
- 四个 hub 页的 12 篇最新文章 feed（mu-plugin:352-464，category__in 一次查询 + seen 去重）
- CFC：hub 的 Model Matrix（按 term 是否已发布决定链接或纯文本，cars-from-china.php:496-522）+ Latest Research（:557-567）+ brand/model 各研究区段（:643-701，空节抑制 :465-483）
- 归档循环（Neve）+ 归档标题/描述（functions.php:187-196）
- 文章 TOC（research-wire.js，仅 single post）

**静态 / 手写硬编码**
- EN 首页：hero 标题/副题/话题链接（home-en.html:161-169）、hero-story 大图卡（含**具体文章 URL 与文案**，:171-178）、Featured（lead + 2 small + 3 compact，:196-211）、4 个 desk 卡（:213-223）、trust 四步（:241-246）、CTA（:248）、reading 三卡（:250-259）、footer（:261-271）——全部手写 HTML
- RU 首页：intro / promise 三原则（home-ru.html:37-46）、9 张 topics 卡（:48-62）、cars 区含**写死的 АВТОСТАТ 统计数据**（:64-75）、method（:102-106）
- CFC hub 文案：eyebrow/deck/Research Areas/How We Research/CTA 均为 PHP 内 if/else 双写死字符串（cars-from-china.php:538-547、569-598）
- 404 三链接（404.php:17-19）、归档 eyebrow（functions.php:191）、TOC 文案（research-wire.js:52、58）
- 页面内联 `<style>`：EN 首页约 120 行、RU 首页约 30 行 CSS 直接写死在页面内容里（home-en.html:2-146、home-ru.html:1-35）

### 1.4 JS 行为（research-wire.js，未部署）

- 仅 `is_singular('post')` 时入队（functions.php:107），footer=true（:117）
- 逻辑：取 `.single-post .nv-content-wrap` 内非空 H2/H3（research-wire.js:4-14）→ 不足 2 个或已有 `.fyz-article-toc` 则退出（:16-18）→ 由标题文本生成 slug id（正则 `[^a-z0-9\u0400-\u04ff]` **支持西里尔**，:24-27）→ 重名加后缀去重（:30-35）→ 无 id 的标题补 `fyz-{id}-{index}`（:37-39）→ 构建 `<details open>` + `<summary>In this report</summary>`（**英文写死**，:54-59）→ 插入 `.entry-header` 之后（:62）
- **无** scrollspy / active 高亮 / 平滑滚动 / 展开状态记忆——纯锚点目录
- 依赖 Neve 的 `.nv-content-wrap` / `.entry-header` DOM 结构，父主题模板结构一变即失效（**需核实**生产是否已有其他脚本在该位置插入节点）

---

## 2. 自动化展示现状与"弱鸡 / 固化"问题清单

> 用户原话："前端自动化展示太弱鸡和固化了"。以下按证据逐项列出。

### 2.1 首页整体就是两个巨型硬编码 Custom HTML 块

- 证据：preview/home-en.html:1 与 home-ru.html:1 的 `<!-- wp:html -->` 包裹整页；EN 首页约 120 行内联 CSS + 全部区块手写（见 1.3）。
- 后果：**新增文章不会自动进入** hero-story / Featured / desks / trust / reading / footer 任何一处；换任何一篇精选文章都必须人工打开 WP 编辑器改 Custom HTML 块里的 URL 和文案；**两语各改一次**（page 11 与 page 400 是两份独立内容）。
- Featured 被 BASELINE.json:17 明确记为 `featured_is_manual: true`——手动精选是**既定事实**，不是 bug，但没有任何"编辑工作流"支撑（无 meta 配置、无标记系统）。

### 2.2 写死的数字与文案（会过期 / 语言错误）

| 位置 | 证据 | 问题 |
|---|---|---|
| RU 首页 АВТОСТАТ 统计 | home-ru.html:74 "около 38 тыс. … в 4,5 раза … 22,7%" | 时间敏感数据写死在页面内容里，2025/2026 数字会过期且无更新时间戳 |
| CFC RU hub eyebrow | cars-from-china.php:540 `'AUTOMOBILI IZ KITAYA'` | **拉丁转写而非西里尔**（应为 "АВТОМОБИЛИ ИЗ КИТАЯ"），RU 页面出现拉丁大标题，语言质量 bug |
| CFC RU brand/model eyebrow | cars-from-china.php:614、677 恒输出 `'CARS FROM CHINA'` | RU 品牌/车型页眉眉字恒为英文 |
| CFC model deck | cars-from-china.php:679 通用一句话 | 每个车型页 deck 完全一样，无车型差异信息 |
| TOC 文案 | research-wire.js:52 `aria-label 'Article contents'`、:58 `'In this report'` | RU 文章目录显示英文 UI |
| 归档 eyebrow | functions.php:191 `esc_html__('Research archive')`，text domain 无 PO 文件 | RU 归档显示英文 "Research archive" |
| 404 链接 | 404.php:17-19 三个 `home_url()` 硬编码路径 | 静态，无"最近文章"等动态出口 |
| RU 文章 ID 表 | mu-plugin:41-43 硬编码 `array(400,448,445,…,350)` | **每新增一篇俄文文章都要手工加 ID**，漏加则 lang=ru-RU / og:locale / schema inLanguage 全部失效——这是 SEO 契约的最大人工隐患（文件在 mu-plugin，超出可改范围，需专门授权） |

### 2.3 动态 feed 的局限（feeds 插件）

- **覆盖面窄**：marker 替换只对 page 11 / 400 生效（插件:104），首页其余区块全静态。
- **无查询级缓存**：每次未命中页面缓存的渲染都拉最近 80 篇再逐篇过滤（插件:46-75）；页面级缓存由 LiteSpeed 承担，插件自身无 transient。
- **语言判定靠启发式**：meta `_fyz_content_language` → category 54 → 标题西里尔 → 拉丁（插件:13-32）；标题既无拉丁也无西里尔也无汉字时返回 `''` 被直接丢弃（:31）。
- **guide 判定靠正则词表**：插件:34-44——RU 词表 `(guide|check|repair|verification|гайд|руковод|провер|ремонт|совместим|выбор|ввоз|утильсбор)` 是拍脑袋词表，误判/漏判风险高；slug 是英文时 RU 正则可能匹配失败。
- **数量不足回退到静态快照**：`count($posts) < $minimum` 时返回原始 marker 内容（插件:120）——即页面里写死的旧网格，可能展示已过期文章。
- **RU guides 缺项**：生产 RU = 5 guides < limit 6（背景事实），3 列网格末行留白；无"查看全部"入口。
- **排除逻辑缩小池子**：guides 强制排除当前 signals 的 4 篇（插件:116-118）。
- **缓存失效盲区**：purge 挂在 `save_post_post / trashed / untrashed / before_delete`（插件:146-148）；若只改 `_fyz_content_language` / `_fyz_content_kind` meta 而不重新保存文章，是否触发 `save_post` **需核实**——不触发则首页 feed 过期不刷新。
- **无计数、无"更新时间"、无动态徽章**：任何"共 N 篇""最近更新 X"都无处显示。

### 2.4 无动态更新的地方（汇总）

首页 hero-story / Featured / desks / trust / reading / footer（EN）、topics / cars 统计区 / method（RU）、CFC hub 的 Research Areas / How We Research / CTA 文案、404 链接、归档 eyebrow——以上全部需要人工编辑页面或 PHP 文件才能变化。

### 2.5 跨语言重复维护点

- **首页双份 Custom HTML**：EN 用 `.fyz-home` / `.fyz-locale-header`（home-en.html:26、148），RU 用 `.fyz-ru-hub` / `.fyz-ru-nav`（home-ru.html:5、36）——两套完全不同的 class、配色（EN 蓝/红系 vs RU `--accent:#16705a` 绿系 + `--warm:#b5532e`）、字号体系。改首页任何文案 = 改两处。
- **research-wire.css 只覆盖 EN 半边**：wire 层有 `.fyz-locale-header` 与 `.fyz-home` 的完整覆盖（research-wire.css:22-72），RU 只吃到背景色（:17-20 的 `.page-id-400`）；RU 首页视觉几乎全靠页面内联 CSS——**两语首页"看起来是不同网站"**。
- **CFC 文案双写**：research types 的双语在数组里（cars-from-china.php:93-124，良好）；但 hub/brand/model 头部文案是 if/else 硬编码双份（:538-547、613-616、676-680）。
- 中英标题/副题/CTA 各一份，改动需在页面内容 + 可能还要改 wire CSS 的类覆盖两处同步。

### 2.6 缓存 / 脚本依赖风险

- **版本号驱动缓存**：CSS/JS 版本用 `filemtime`（functions.php:81、98、110、133），子主题版本 bump 触发 `litespeed_purge_all` + `wp_cache_flush`（functions.php:40-52）。正确但依赖部署纪律：**不 bump 版本号 → 不 purge**。
- **wire 未部署与 functions.php 快照的错位风险**：本包 functions.php 无条件 enqueue research-wire.css（:100-105），且 cars-from-china.css 声明依赖 handle `fyzsxnb-research-wire`（:138）。若生产 functions.php 已是此版本而 research-wire.css 未上传（生产现状），每个页面会输出一个 404 的 stylesheet 链接并被 LiteSpeed 缓存——**需核实生产 functions.php 实际内容与已部署版本差异**。
- **三套 CSS 层叠竞争**：页面内联 `<style>`（页面内容）vs design-system.css vs research-wire.css，靠选择器特异性取胜。实例：EN 首页内联 `.fyz-signal-grid{grid-template-columns:repeat(4,…)}`（home-en.html:62）被 wire 的 `.fyz-home .fyz-signal-grid{repeat(2,…)}`（research-wire.css:140）以更高特异性覆盖；生产（无 wire）实际是 4 列。这种"主题 CSS 反推页面内容"的写法极脆。
- **TOC 依赖 DOM 结构**：research-wire.js:4-5 依赖 Neve 类名；插入 `.entry-header` 之后（:62）与 Neve/其它脚本的 DOM 操作可能冲突（**需核实**）。
- **RU hub 无页面对象**：`/ru/cars-from-china/` 经 rewrite + template_include 渲染（cars-from-china.php:220-245），无 WP page 对象——**title / canonical / AIOSEO 输出行为需核实**（生产已确认 200 + noindex, follow，但 title/canonical 是否正常未知）。

---

## 3. 字体与排版审计

### 3.1 实际 font-family 栈（含设计 token）

| 用途 | 栈（CSS 声明） | 实际渲染结果 |
|---|---|---|
| 展示字体（标题） | `--fyz-display: "Noto Serif","Source Serif 4",Georgia,serif`（design-system.css:11） | Noto Serif / Source Serif 4 **均未加载**（grep 证实全包无 @font-face / fonts.googleapis / preconnect）→ Windows/macOS 实际 = **Georgia** |
| wire 层展示字体 | `Georgia,"Noto Serif",serif`（research-wire.css:35、161、258、301） | 实际 = Georgia（顺序反了，Georgia 前置） |
| 正文 | `--fyz-body: Inter,"Noto Sans","Segoe UI",Arial,sans-serif`（design-system.css:12） | Inter / Noto Sans 未加载 → Windows=Segoe UI、macOS=Arial、Android=Roboto |
| RU 首页内联 | 无 font-family 声明（home-ru.html:5 起继承 body） | 同上（Segoe UI / Arial 均含西里尔，覆盖 OK） |
| CJK（中文内容） | 栈中无任何 CJK 字体 | 靠系统回退（Windows=Microsoft YaHei、macOS=PingFang），**无指定** |

- **结论：字体加载方式 = 零外部依赖、纯系统栈**。优点：无 FOUT、无第三方性能/隐私负担；缺点：标题气质取决于用户系统（Android/Linux 无 Georgia 时落系统 serif 默认，视觉不可控）；`Inter` 的 750/800/850 数值字重（design-system.css:75、:266；home-en.html:120 的 `font-weight:850`）在 Segoe UI / Arial 上会被浏览器映射到最近的 700/800，**观感与设计稿不一致**。

### 3.2 字号 / 行高 / 字重体系

| 层级 | 桌面 | ≤960px | ≤576px | 行高 | 字重 |
|---|---|---|---|---|---|
| 站点基础 | 17px（design-system.css:23） | — | 16px（:409） | 1.65 | 400 |
| 文章正文 | 18px（:154） | — | 17px（:453） | 1.78 → 1.72 | 400 |
| 文章 H1 | 48px（:131） | 42px（:403） | 34px（:448） | 1.08 → 1.14 | 700 |
| 文章 H2 | 32px（:182） | — | 27px（:458） | 1.2 | 700 |
| 文章 H3 | 24px（:187） | — | 22px（:462） | 1.28 | 700 |
| 归档/404 H1 | 44px（:278） | — | 34px（:492） | 1.1 | 700 |
| 首页 hero H1（wire） | `clamp(2.7rem,4.2vw,3.55rem)`（research-wire.css:91） | 2.55rem（:384） | 2.2rem（:436） | 1.02 | 未声明（继承页面内联 650，home-en.html:47） |
| RU 首页 H1 | `clamp(2.2rem,5vw,3.5rem)`（home-ru.html:1） | — | — | 1.08 | 未声明 |
| 元信息/eyebrow | 13-14px（design-system.css:143、:265；CFC 12.5-13.5px） | — | — | 1.5 | 600-800 |
| figcaption | 13px（design-system.css:200） | — | — | 1.5 | — |

- 标题 vs 正文对比度：文章页 48/18 = 2.67 倍，清晰；H2/H3 阶梯 32/24 合理；`text-wrap: balance`（:135、:177）对标题换行友好。
- 阅读宽度：正文列 **760px**（`--fyz-reading-width`，:14、:116-118），站点容器 1200px（:13），wire 层归档/首页 1220px（research-wire.css:14）；页宽与行宽比例符合长文阅读惯例。

### 3.3 中英俄混排与 Cyrillic 覆盖

- 覆盖本身 OK：Segoe UI / Arial / Roboto 均含西里尔，RU 内容可正常渲染（无需加载新字体）。
- **无语言特异调整**：西里尔字形普遍更高更宽，常见做法是 RU 视图 +5% 字号或 -2px 标题——全包无 `.fyz-lang-ru` 的字体规则。
- **无 `hyphens:auto`**：俄语长词（如 "зарегистрированные"）在窄列靠 `overflow-wrap:anywhere` 兜底（design-system.css:398，576px 断点），断词不优雅。
- `text-wrap:balance` 对 RU 标题生效（好）；标题 `max-width:18ch`（:127）对西里尔词长偏紧（ch 基于 "0" 宽度，西里尔大写更宽），RU 标题可能多折一行——**需核实**实际 RU 文章标题换行观感。
- CFC 模型页/品牌页在西里尔视图的标题（车型名如 "Фольксваген Тайрон"）沿用同一体系，无专门处理。

### 3.4 移动端表现

- 576px 断点字号阶梯完整（见上表）；表格 `display:block; overflow-x:auto` + th/td `min-width:112px`（:465-477）——长表格横向滚动可读。
- wire 层 780px / 420px 断点把 hero、signals、desks、归档全部降为单列（research-wire.css:367-438）；RU 首页 720px 断点同（home-ru.html:34）。
- 已知通过：390px 无横向溢出（RESULT.md:54-56、QA 截图）。
- 隐患：RU 首页导航 `overflow-x:auto`（home-ru.html:2）在窄屏出现横向滚动条观感；hero-story 高度 330px（wire:103）→ 310px（780px 断点）在 390px 下图片文字可能偏挤（有 QA 截图，标注已过）。

---

## 4. 美观度问题清单（基于 CSS 实际值）

1. **三套蓝色并存，品牌色不统一**：`--fyz-blue:#2563eb`（design-system.css:4）vs `--fyz-wire-blue:#1769aa`（research-wire.css:12）vs 页面内联 `--blue:#315efb`（home-en.html:26）——链接、TOC 左边框、desk 顶条、按钮、CTA 各用各的蓝。
2. **绿色/teal 同样三套**：`#168c7b`、`#3b827c`（wire:64）、`#139889`（home-en.html:26）——blockquote 边框、eyebrow、label 条颜色不一致。
3. **RU 首页是完全另一套色板**：`--accent:#16705a`、`--warm:#b5532e`（home-ru.html:5）——与 EN 首页蓝/红系割裂，两语首页观感像两个网站（叠加 2.5 的类名分叉）。
4. **死 token**：`--fyz-shadow:0 12px 30px rgba(23,32,43,.08)`（design-system.css:16）定义后**全包 0 处使用**（grep 证实）——卡片无任何阴影层级，区块靠边框/底色区分。
5. **归档条目双重样式互相覆盖**：design-system.css:290-297 给 `.blog-entry` 蓝顶 3px 无背景卡；research-wire.css:282-291 改为 150px 缩略图 + 1px 线行——两套归档视觉并存，谁后加载谁赢；生产当前（无 wire）= 前者。
6. **等高手写留白**：wire 版 `.fyz-signal` `min-height:146px`（research-wire.css:147）、`.fyz-desk` `min-height:190px`（:230）；RU 首页内联无此约束（home-ru.html:26）——两语卡片高度不一致，内容不足时空洞。
7. **hero 标题两套尺寸**：页面内联 3.85rem（home-en.html:47）vs wire `clamp(…,3.55rem)`（research-wire.css:91）；生产（无 wire）= 3.85rem 大标题，780px 以下骤降到 2.45rem（home-en.html:144）落差大。
8. **动效几乎为零**：无 transition（除 reduced-motion 归零，research-wire.css:440-444）；卡片 hover 仅链接变色（design-system.css:36-39）；CTA 按钮 hover 变橙 `#f0a33a`（home-en.html:121），与全站蓝体系无关联（橙色仅此处 + `--orange` token）；无滚动显现、无骨架屏、无 active 按压反馈。
9. **区块节奏不一致**：wire 版 section 上下 52px（research-wire.css:176-182）vs 页面内联 60-68px（home-en.html:68、89、102、109）vs RU 42px（home-ru.html:14）。
10. **深色区只存在于 EN**：hero-story（`--dark:#121b26`，home-en.html:26）与 cta-band（wire:67）——RU 首页无深色块，结构不对等。
11. **eyebrow 字距体系不统一**：全站 13px/字距 0/uppercase（design-system.css:262-269）vs wire 头 `.72rem/.08em`（research-wire.css:48-51）vs CFC `13px/.14em`（cars-from-china.css:17-24）——三档字距。
12. **首页 signals 两套网格**：页面内联 4 列（home-en.html:62）vs wire 2 列（research-wire.css:140）——resarch-wire 部署后首页视觉会与 QA 预览截图（4 列）完全不同，**部署前必须重拍截图**。
13. **CFC 卡片 meta 字号偏小**：`12.5px`（cars-from-china.css:119）；矩阵品牌列固定 180px（:67）在 768px 断点才塌成单列（:187-191），1024-769px 区间窄屏居中体验一般。
14. **RU 首页导航窄屏滚动条**：`overflow-x:auto`（home-ru.html:2）观感差。
15. **焦点样式统一但缺按压反馈**：`:focus-visible` 3px 蓝 outline（design-system.css:41-44）全站一致（好）；桌面点击无 :active 反馈。

---

## 5. 改进建议（供 ChatGPT 策划用）

> 标注：涉及文件 / 风险（尤其 SEO、缓存、RU 契约）。风险分级：低=视觉层，中=涉及内容/结构，高=触碰契约。

### 5.1 快速可执行（0.3.x 补丁级，风险低~中）

| # | 建议 | 涉及文件 | 风险 |
|---|---|---|---|
| Q1 | **TOC 文案双语化**：`research-wire.js` 读 `body.fyz-lang-ru`（functions.php:153 已加）输出 `'Содержание'`/`'In this report'`，aria-label 同步 | research-wire.js（子主题内，可随 0.3.x 发布） | 低；无 SEO 影响 |
| Q2 | **CFC RU 文案修正**：hub eyebrow 改西里尔 `АВТОМОБИЛИ ИЗ КИТАЯ`（:540）；brand/model RU 视图 eyebrow 改西里尔（:614、:677）；model deck 去掉通用一句话（:679）或按车型/语言生成 | inc/cars-from-china.php | 低；纯文案。需 bump `FYZSXNB_CFC_VERSION`（:25）触发 litespeed purge |
| Q3 | **归档 eyebrow 双语**：按 `fyzsxnb_is_russian_view()` 输出 `'Архив исследований'` | functions.php:191 | 低；注意 RU 归档 `html lang` 已知问题（BASELINE.json:27）属 mu 层，**不要顺手动** |
| Q4 | **404 动态化**：静态三链接（404.php:16-20）之外追加"最近文章"（`wp_get_recent_posts` 或 CFC 最新）；保持搜索框 | 404.php | 低；404 页 robots 不受影响（**需核实** 404 是否被索引） |
| Q5 | **字体栈收敛 + RU 补偿**：统一 display 栈（design-system 与 wire 二选一，建议保留 "Noto Serif","Source Serif 4",Georgia）；`.fyz-lang-ru` 下标题字号 -2px 或 `letter-spacing:0` 维持 | design-system.css / research-wire.css | 中；视觉回归必做（1440/390），无 SEO 影响 |
| Q6 | **死 token 与配色收敛**：删 `--fyz-shadow` 或真正启用；在 wire 层把页面内联 `--blue/--teal` 的消费点（`.fyz-label:before` 等）统一到 wire token | research-wire.css | 中；改配色需截图回归 |
| Q7 | **CFC 矩阵动态计数**：每行/每模型显示已发布文章数（复用 `fyzsxnb_cfc_has_published` 的查询，改返回 count；矩阵 12 模型可接受额外查询，或一次 get_terms + 聚合） | inc/cars-from-china.php | 低；证据优先契约下数字必须真实可算（只显示真实文章数，不编造） |
| Q8 | **RU 导航窄屏换行**：`.fyz-ru-nav nav` 由 `overflow-x:auto` 改 `flex-wrap` | page 400 的 Custom HTML（页面内容） | 中；改页面内容前先备份该页（REST 快照），回归 RU 首页 390 |

### 5.2 结构性（需专门任务立项，先出方案再执行）

| # | 建议 | 涉及文件 | 风险 |
|---|---|---|---|
| S1 | **首页数据驱动化**：把 EN/RU 首页 Custom HTML 里的静态区块（hero-story / featured / desks / reading）改造为子主题 shortcode / 模板片段 + 配置（meta 或 `inc/home-*.php` 数组）驱动，页面内容只留骨架与 feed marker。参考 CFC 现有的 `fyzsxnb_cfc_post_card()`（cars-from-china.php:434-453）模式 | 子主题新增 `inc/home-*.php` + page 11/400 内容迁移 + research-wire.css | **高**：必须保持 single H1、lang、canonical、feed marker 契约；迁移期间 A/B 截图对比；LiteSpeed 全清；回滚=恢复页面内容 + 文件 |
| S2 | **统一两语首页设计系统**：把 RU 的 `.fyz-ru-hub`/`.fyz-ru-nav` 样式迁入 research-wire.css（类名归一），消灭页面内联双份 CSS，两语共用一套 token/断点 | research-wire.css + page 400 内容 | **高**：RU 首页大改；回归 390/768/1440 + RU SEO 检查（lang/canonical/noindex 契约不变） |
| S3 | **feed 插件增强（需单独授权，超子主题范围）**：guide 判定改 meta 显式打标（替代正则词表）；查询加 transient 缓存 + meta 变更也触发失效；marker 支持计数徽章与 "View all" 链接；RU guides 缺项补足机制 | plugin/fyzsxnb-home-dynamic-feeds.php | **高**：生产插件文件；改动前必须快照备份，回滚依赖现有快照；回归 EN 4+6 / RU 4+5 |
| S4 | **动态统计徽章**：hero/desk 区展示可验证的动态数字（如 "X 篇已核查报告"、"Y 个车型档案"、"最近更新：日期"），由子主题 shortcode 从真实查询计数 | 子主题 `inc/` + 页面内容 | 中；**数字必须真实可算**（证据优先契约，禁止编造计数） |
| S5 | **组件化模板片段库**：统一 post-card 样式（feed 插件卡片 / CFC card / wire 归档条目三套并存，见 1.3）为一个主题函数；先只在子主题内部统一 CFC card 与归档条目，插件卡片等 S3 授权 | 子主题 `inc/` + cars-from-china.php | 中；结构改动要过 DOM 截图回归 |
| S6 | **部署与验收工具化**：把 0.3.x 流程（版本 bump → `php -l` → FTP → purge → 验收：public H1 / lang / canonical / feed / 移动端）沉淀为脚本，复用现有 `qa/visual-qa.cjs`、`qa/cfc-visual-qa.cjs`（1440/1024/768/390） | qa/ 脚本 + docs | 低；纯工程化 |
| S7 | **RU ID 表自动化（mu 层，专门授权）**：把 mu-plugin:41-43 的硬编码 RU ID 表改为 category 54 单一权威 + 缓存版动态检测，消除"漏加 ID 则 SEO 契约失效"隐患 | mu-plugins/fyzsxnb-p0-seo-patch.php | **高**：SEO 核心契约；必须跑 lang/og/schema/单 H1 回归 |

### 5.3 自动化展示的总体思路（回应用户"太弱鸡和固化"）

- **数据驱动优先**：凡能由查询得出的（最新文章、车型矩阵、文章计数、更新时间、语言分区数量）一律 shortcode/函数渲染，页面内容只留占位与配置；手工区（Featured/hero-story）用"编辑 meta 打标 + 查询筛选"替代"手改 HTML"。
- **组件化**：post-card / section-head / desk-card 全部收敛为单一模板片段，一处改样式全局生效，天然解决两语双份维护。
- **动态数字**：把"站内有 X 篇报告 / Y 个车型档案 / 最近更新于 Z"做成真实计数 shortcode，替代写死统计（如 home-ru.html:74 的 АВТОСТАТ 数据应改为带日期与来源的"数据卡片"组件，编辑改数据而不是改 HTML）。
- **缓存纪律**：所有动态输出必须声明其失效键（版本 bump / save_post / meta 变更），并纳入验收清单。

---

## 6. 风险与边界

### 6.1 不能动的部分（红线）

- **Neve 父主题**、Elementor、React、新 CMS——一律不改。
- **mu-plugins/fyzsxnb-p0-seo-patch.php 与 plugin/fyzsxnb-home-dynamic-feeds.php**：虽是最"固化"的两处（RU ID 表、hub 分类映射、guide 词表），但**不在子主题可改范围**；任何改动需单独授权 + 快照回滚（S3/S7）。
- **SEO 契约**：lang / canonical / robots（含 RU hub 的 noindex,follow，cars-from-china.php:732-763）/ single H1 / category 54（= FYZSXNB_CFC_CATEGORY_RU，cars-from-china.php:26）——任何改动必须回归。
- **已知未解问题保持原样**：RU 归档 `html lang` 返回 en-US（BASELINE.json:27、RESULT.md:99-101）——属 mu 语言层基础设施问题，视觉分支不得顺手"修复"。
- **页面 slug / 分类 / 规范 URL / sitemap / feed 查询规则**（DEPLOYMENT_MANIFEST.md:10-14 的 deliberately unchanged 清单）。

### 6.2 部署流程（0.3.x 补丁 → FTP → 验收）

1. 改子主题文件（style.css 版本号 bump 至 0.3.3 → 触发 `fyzsxnb_purge_design_cache_once` 的 `litespeed_purge_all`，functions.php:40-52；CFC 相关 bump `FYZSXNB_CFC_VERSION`，cars-from-china.php:25）。
2. `php -l` 全部 PHP（RESULT.md:81-84 记录过 8.5.9 通过）。
3. FTP 上传（仅子主题目录文件；回滚 = 从 `feat/fyzsxnb-v2-research-wire` 基线 commit `58f8167` 恢复四文件，DEPLOYMENT_MANIFEST.md:16-22）。
4. 清 LiteSpeed / object / CDN 缓存（通过既有部署工作流）。
5. 验收清单：public H1（每面 1 个）、lang（RU 页 ru-RU）、canonical、feed（EN 4+6 / RU 4+5 且语言纯净）、RU hub 200 + noindex,follow、移动端 1440/1024/768/390 截图对比（qa/ 脚本已有）。

### 6.3 测试方法

- 四宽度截图回归：1440 / 1024 / 768 / 390（qa/visual-qa.cjs、qa/cfc-visual-qa.cjs 已实现，cfc 预览文件即为此而生，cars-from-china-model-preview.html:6-14）。
- 结构回归：`qa/cars-from-china-structural-checks.ps1`、`qa/cars_from_china_seo_regression.py`（包内已有，可扩展至任意补丁）。
- 内容侧：检查 feed marker 数量与 RU 语言纯净（category 54 契约）；检查单 H1；检查无新增 404 样式链接（wire 部署前重点）。
- LiteSpeed 验证：bump 版本号后 curl 首页确认 asset 版本串变化、无 304 陈旧缓存。

### 6.4 需核实清单（本报告未能从文件确认的生产事实）

1. 生产 functions.php 是否已包含 research-wire enqueue（若已含而 CSS 未上传 → 全站 404 样式链接并被缓存）。
2. `russian-library` category slug 与 ID 54 是否同一分类（functions.php:60、64 用 slug；mu/feeds/CFC 用 ID 54）。
3. RU hub（/ru/cars-from-china/）的 title / canonical / AIOSEO 输出行为（无 page 对象）。
4. research-wire.js 插入 `.entry-header` 后与其它脚本的 DOM 冲突。
5. 仅修改 `_fyz_content_language` / `_fyz_content_kind` meta 是否触发首页缓存 purge（save_post 语义）。
6. EN hub 页（507，draft）当前对外表现（404？预览不可见？）。
7. RU 文章标题在 18ch 约束下的实际换行观感。

---

*报告完。审计依据：fyzsxnb-ui-v2 包内 19 个必读文件 + RESULT.md + BASELINE.json + 针对性 grep（--fyz-shadow 使用、@font-face/字体加载、category 54 引用）。*

---

## 附录：需核实项补充核实（DeepSeek 部署会话证据，2026-08-18）

- **production functions.php 是否 enqueue 未部署的 research-wire.css？** → **否，非风险**。生产 functions.php = 0.2.2 基线（work/deployments/fyzsxnb-neve-child/functions.php，141 行，仅 design-system enqueue）+ CFC 增量 28 行（require inc + cars-from-china.css enqueue，依赖 fyzsxnb-design-system）。research-wire enqueue 只存在于仓库未部署版本（da60f14 系）。实测生产 ssets/css/research-wire.css HTTP 404。
- **RU 首页 АВТОСТАТ 硬编码**、**CFC eyebrow 拉丁转写/恒英文**、**TOC 文案英文**、**归档 eyebrow 未翻译** → 与 0.2.2/0.3.x 部署内容一致，属实（均为页面内容/子主题代码层面问题，可改范围：子主题 + 页面内容编辑）。
- **RU 文章 ID 硬编码表（mu-plugin）** → 属实，mu-plugin 不在子主题可改范围，需单独授权。

