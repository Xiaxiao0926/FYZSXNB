# FYZSXNB UI V2 0.3.5 — Homepage Template Migration 部署报告

`package`: fyzsxnb-ui2-035（主题版本 **0.3.11**；阶段内 0.3.9→0.3.10→0.3.11 为纠正性 bump，用于强制 purge 与终版）
`status`: **deployed_and_publicly_verified** — `FINAL=PASS`
`date`: 2026-08-19

## 0. 前置门槛：CACHE-BASELINE-035（P0）

部署前完成（`qa/cache_baseline_035.py`）：6 个代表 URL × 4 种 UA（desktop/mobile/googlebot/无 UA）× {无 query, cache-busted} = 24 视图。

- **powered/Neve 徽标 = 0、EN archive RU 泄露 = 0、RU archive EN 泄露 = 0、RU 日期俄化、新 FYZSXNB footer** —— 全部视图一致（含 no-query vs cache-busted 首段相同 headSame=True）。
- 结论：**0.3.4.1 生产版本对所有主要缓存视图生效**（此前用户外部抓取见旧 HTML 属中间缓存 TTL 时序，已不可复现）。
- 途中发现：acceptance 若用 `?x=` 参数命中旧变体（LiteSpeed 按 URL 缓存），会误判；**此后一律用"bump 版本 → purge → 单一确定性 UA"** 验收。

## 1. 迁移结构（Controller 薄、component 小）

```
front-page.php            ← EN 前端页控制器（调用 fyzsxnb_render_homepage）
page.php (child)          ← /ru/（page 400）分支 → 同一 renderer；其余页面委托 Neve page.php
inc/home.php              ← locale config (EN/RU) + URL resolvers + feed 迁移调用 + renderer
template-parts/home/      ← hero / signals / featured / desks / guides / trust / cta / reading（8 parts）
research-wire.css         ← Home V2 层（原页面内联 CSS 迁移 + canonical token 映射 + 字重 400-700）
design-system.css         ← 退役 0.3.4 临时白底 freeze override
```

- **EN/RU 共用模板**：仅 config/copy/data 差异（hero 文案、nav、desks、trust、cta、reading）；无 front-page-ru / hero-ru 分版。
- 数据解析器 `fyzsxnb_home_target()`：`path/category/page/post/anchor` → `home_url/get_category_link/get_permalink`；**动态文章/Desk/Featured 链接零手写绝对地址**（验收硬编码扫描 = 0）。
- Latest/Signals/Guides：**迁移调用不改算法** —— 复用 feeds 插件 `fyzsxnb_get_home_feed_posts`/`fyzsxnb_render_home_feed`（插件零改动）；内容不足 graceful（0-N 条均可，不造 fallback）。
- Featured：仍为人工精选（post slug 配置 → resolver），**未变 automatic latest**。

## 2. DOM Contract（对照）

| Component | Before | After | Content parity |
|---|---|---|---|
| Hero | Custom HTML | hero.php | PASS（EN H1/deck/topic-links/hero-story；RU h1/lead/promise） |
| Latest/Signals | marker | signals.php | PASS（EN 4 / RU 4，locale-pure） |
| Featured | hardcoded block | featured.php | PASS（EN 6：lead1+small2+compact3） |
| Desks | hardcoded HTML | desks.php | PASS（EN 4 / RU 8，resolver URLs） |
| Guides | marker | guides.php | PASS（EN 6 / RU 6，locale-pure） |
| Trust | static HTML | trust.php | PASS（EN steps 4 / RU method+notice） |
| CTA | static HTML | cta.php | PASS（resolver → /contact/） |
| Reading | static HTML | reading.php | PASS（EN 3；RU 无此模块→整节抑制） |
| Header | 自定义 locale header | hero.php 顶部（locale header） | PASS（Neve header 在 home 隐藏） |
| Footer | 各首页自写 footer | get_footer() 统一 footer | PASS（EN/RU 同源；RU 页禁用 footer 已由 priority-200 过滤器强制开启） |
| 页面 Custom HTML | 巨型 HTML | **占位注释**（已备份+清空） | 退役 |

RU：featured/reading 无数据 → 整节抑制（graceful）；signals/desks/guides/trust/cta 齐备；**结构 parity 达成（不要求数量 parity）**。

## 3. SEO / 结构零迁移

- 唯一 H1（hero `#fyz-home-title`）；section = H2；卡片 = H3。
- canonical 自指（`/` 与 `/ru/`）、hreflang RU/EN、robots、OG、lang（en-US/ru-RU）—— 与迁移前一致（acceptance 三页抽查）。
- structured data 未动；sitemap/URL/permalink 未变。

## 4. 硬指标

- **hardcoded absolute internal URLs in source = 0**（inc/home + front-page + page + 8 parts 扫描）。
- **5-UA 一致性（§22）**：EN/RU 首页 × desktop/mobile/googlebot/无 UA × {无 query, cache-busted} = 8 视图：hero/信号数/footer/canonical 全同 —— **PASS**。
- 390px overflow = 0；console = 0；CSS/JS/font 404 = 0。
- 发布/更新/删除失效机制：feeds 插件 save_post 钩子不变；front-page 直接调用同一数据函数 → 缓存失效链路保持（最小兼容适配：无 page-content marker 依赖；页面内容已清空，插件 marker filter 自动 no-op）。
- Footer 统一：首页/文章/归档/CFC 同源（Neve footer builder + FYZSXNB 版权，无 "Neve | Powered by WordPress"）。

## 5. 部署事实（快照 + 三方哈希）

替换 4 文件：functions.php（DD9FA1C7→追加 require inc/home.php）、assets/css/design-system.css（5A976742，freeze 退役）、assets/css/research-wire.css（EBED604B，Home V2 层）、style.css（0.3.9→0.3.11）。
新增 11 文件：front-page.php / page.php / inc/home.php（终版 A999B9A2…）/ 8 parts。
所有文件 `source==remote==manifest`（终版 manifest 记录 theme_version 0.3.11）。
回滚：快照/备份在 `work/deployments/fyzsxnb-ui2-035/snapshots/`；恢复 4 替换文件 + 删除 11 新文件 + 恢复页 11/400 内容（备份 `backup/`）即回旧 Custom HTML 首页。

## 6. 过程中修复（如实记录）

1. **hero 部分漏传 args** → 补传（曾因 PowerShell 转义写入 `\` 破坏 PHP，改 Python 修复 + lint 把关）。
2. **RU footer 不渲染**：page 400 设置了"禁用 footer"（nv-without-footer）→ 依次修复：`neve_filter_toggle_content_parts` priority 10→200（metabox 在 100 覆盖）仍无效 → 最终 **body_class 剥离 `nv-without-footer`**（priority 200）生效。
3. **验收误报**：LiteSpeed 按 URL 缓存旧变体，`?x=` 不总是绕缓存 → 确立"版本 bump purge + 单一 UA"验收纪律（记录进 CACHE-BASELINE 结论）。

## 7. 备份与退役

- 页面 raw 内容/内联 CSS/正文标记备份：`work/deployments/fyzsxnb-ui2-035/backup/`（page-11-EN / page-400-RU raw + inline css + body markup）。
- 页 11/400 content 已清为占位注释（`<!-- Homepage presentation is controlled by front-page.php (UI V2 0.3.5) ... -->`）；title/meta 未动。
- 防回退注释已写入 front-page.php（"Do not restore the legacy Custom HTML page body"）。

## 8. FINAL=PASS 对照

front-page.php 接管 ✅ · EN/RU 共用结构 ✅ · Custom HTML 不再承担渲染 ✅ · legacy raw 已备份 ✅ · Hero/Featured/Latest/Guides/Desks/Trust/CTA/Reading parity ✅ · Article/Desk/Featured 链接 resolver 化 ✅ · EN feed 无 RU / RU 无 EN ✅ · 内容不足不跨 locale、section 抑制 ✅ · 唯一 H1 ✅ · canonical/hreflang 不变 ✅ · 390 overflow 0 ✅ · console 0 ✅ · 404 0 ✅ · Design System V2 首页生效 ✅ · 临时白底 freeze 退役 ✅ · LiteSpeed 多 UA HTML 一致 ✅ · 发布失效机制保持 ✅ · Git/manifest/production hash PASS ✅ · rollback 可用 ✅

## 9. 下一步

- UI V2 页面层闭环。后续阶段：**0.3.6 Feed Hardening**（locale 显式化/guide meta/cache/invalidation）→ **0.4.0 Translation Pair**；326KB Inter + LiteSpeed JS delayed 归入 Performance/Runtime 治理。
- 仓库提交：`92672b3`（17 files：front-page/inc/home/page/8 parts/2 css/functions/style 0.3.11 + qa）。

`FINAL=PASS`