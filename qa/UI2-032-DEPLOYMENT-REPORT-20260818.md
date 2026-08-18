# FYZSXNB UI V2 0.3.2 — CSS Hygiene 部署报告

`package`: fyzsxnb-ui2-032（主题版本 0.3.3 → **0.3.4**）
`status`: **deployed_and_publicly_verified** — `FINAL=PASS`
`date`: 2026-08-18

## 1. 范围与原则

仅 CSS 底层整理，**视觉零改动**（"页面截图前后几乎一样"）。未动：颜色值、字体、字号、spacing、radius、动画、Header/Footer、首页布局、模板、feeds、mu-plugin、页面内容、SEO 输出。新增 `!important` = **0**。

## 2. 实际改动清单（证据驱动）

### 2.1 死 token（生产 + 仓库）
| token | 位置 | 证据 | 动作 |
|---|---|---|---|
| `--fyz-shadow` | design-system.css `:root` | 全仓库（3 CSS + previews + 页面内容 11/400）引用 **0** | **删除**（生产已部署） |
| `--teal` / `--orange` / `--red` / `--blue-wash` / `--muted` | research-wire.css `.fyz-home` | research-wire 内引用 0；页面内联样式自持同名变量（已验证 page 11/400 defs+uses 自包含） | **删除**（仓库侧，随 0.3.4 阶段部署） |

### 2.2 blue/teal 收敛
审计结论：语义 alias 结构**已存在**（`--cfc-accent: var(--fyz-blue)`、`--blue: var(--fyz-wire-blue)`、`--ink/--line/--paper` 同型）；无同值重复变量可合并；不同功能色（--fyz-teal vs --fyz-blue vs --fyz-wire-*）按边界保留。**本版无需新增 alias**（未暴力合并）。

### 2.3 Archive 规则收敛
- 生产（0.3.4 之前与之后）：archive 样式唯一入口 = **design-system.css**（research-wire.css 未部署，404 实测）。
- 仓库侧：research-wire.css 的 archive 块已加注释标记为 **V2 入口**（0.3.4 阶段部署时以其覆盖 design-system 同特异性规则——加载顺序 20→21 保证）。无视觉变化。

### 2.4 selector 化简（集合等价，零视觉影响）
| 位置 | Before | After |
|---|---|---|
| research-wire L144 | `.fyz-signal, .fyz-signal:first-child, .fyz-signal:last-child` | `.fyz-signal` |
| research-wire L228 | `.fyz-desk, .fyz-desk:first-child` | `.fyz-desk` |
| research-wire media 块 | 7 项 mega-selector（signal/desk 全变体） | `.fyz-signal, .fyz-desk` |
- 保留真实规则：`.fyz-signal:nth-child(even)`、`.fyz-desk:not(:first-child)`、`:first-child { border-top:0 }`。
- `.fyz-design-system .container` / `.fyz-design-system a` 判定为**有意的 body-class 作用域**（非冲突），记录不动作。

### 2.5 !important 审计
Before 2+3+0=5（design-system 2 均在 prefers-reduced-motion；wire 3 为 header/导航覆盖），After 同 5，**本版 0 新增、0 删除**。

## 3. CSS 审计差异表

| 项目 | Before | After | 理由 |
|---|---:|---:|---|
| CSS custom properties（定义数） | 15+18+5=**38** | 14+13+5=**32** | 删除 6 个确认 dead token（--fyz-shadow、--teal、--orange、--red、--blue-wash、--muted） |
| archive 规则组 | 2 组（design-system + research-wire[未部署]） | 2 组（生产入口=design-system；wire 块标注为 V2 入口） | 收敛文档化；生产单一入口不变 |
| `!important` 数 | 2+3+0=**5** | **5** | 本版 0 新增（reduced-motion/导航覆盖保留） |
| 重复 selector | 3 组冗余变体列表 | 0 | 集合等价化简 |
| CSS 文件大小 | 11419+11151+3458=**26028 B** | 11367+10792+3458=**25617 B** | 仅记录，非成功标准 |

## 4. 部署事实（快照 + 三方哈希）

| 文件 | 动作 | 新 sha256（== source == remote == manifest） | 基线 sha |
|---|---|---|---|
| assets/css/design-system.css | replace | FC60EEAA5B288B5419E70F2607E5DDFFA00B12B1A183BD2973B0C362C3F65CB5（11367B） | 82f9d286… |
| style.css (0.3.4) | replace | 08B5C9AA3A6B516FE1AFBA575A973A2F515A2C4A4CD58A6148CEB841A4217D84 | b6c162b2… |

快照/回滚：`work/deployments/fyzsxnb-ui2-032/snapshots/{design-system_css,style_css_root}/before-*.php`
（design-system.css 远端为 LF 行尾，已按 LF 构建部署；style.css 根路径快照。）

## 5. 视觉回归验收（Before/After 截图 + DOM 指标，playwright 实页）

- 11 个目标页（EN/RU 首页×1440/390、RU hub、RU brand、RU model、RU archive×1440/390、EN/RU Article×1440）：**Before==After** —— 均单 H1、390 无横向溢出、console 错误 **0→0**。
- 截图：`work/fyzsxnb-ui-v2/qa/screenshots/ui2-032/{before,after}/`（可人工逐对比对）。
- 部署后 CSS 实抓：sha == manifest；`--fyz-shadow` defs=0/refs=0；!important=2（design-system）。
- 资产：cars-from-china.css 200；research-wire.css 404（未部署，不变）。
- SEO 抽查：EN/RU 文章 lang 正确、canonical 自指、robots indexable；RU hub noindex,follow 保持。

## 6. 已知问题记录（非本版回归）

- `/ru/cars-from-china/` 的 `html lang=en-US`：**mu-plugin 层既有已知问题**（审计 §6.1、BASELINE.json:27），本版仅改 CSS，定义上不受影响；按红线"不得顺手修复"，留待 0.4.0 语言层治理。

## 7. REST 写入 / 缓存 / 回滚

- REST 写入：无。缓存：版本 0.3.4 → 一次性 LiteSpeed purge（已执行）。rewrite 未动。
- 回滚未使用；回滚文件齐备（§4 快照），命令按 runbook §3.4。

## 8. 下一步

- 待用户基于本报告（Before/After 审计 + 删除/alias/selector 修复清单）放行 **0.3.3 Design System V2**（字体/配色/token 统一，不提前混入）。
- 仓库提交：`5540257`（repo 与生产一致；research-wire 改动在仓库侧待 0.3.4 阶段部署）。

`FINAL=PASS`