# FYZSXNB UI V2 0.3.3 — Design System V2 部署报告

`package`: fyzsxnb-ui2-033（主题版本 0.3.4 → **0.3.5**）
`status`: **deployed_and_publicly_verified** — `FINAL=PASS`
`date`: 2026-08-18

## 1. 范围

唯一品牌视觉底座（token/字体/字重/RU 排版 + 微交互）；**未改变任何页面结构/DOM/内容层级**；未动 feeds/mu-plugin/页面内容/canonical/hreflang；未部署 research-wire.js；未做 0.3.4 工作。

## 2. Design Token 最终映射表（现在到底有几套颜色：**1 套品牌 + 1 个 Desk 次级 + 2 个功能色**）

| Role | Canonical token | Final value | Legacy alias / 说明 |
|---|---|---|---|
| Page BG | `--fyz-bg` | `#ffffff` | — |
| Surface | `--fyz-surface` | `#ffffff` | `--fyz-paper` |
| Surface soft | `--fyz-surface-soft` | `#f7f5ef` | `--fyz-warm` |
| Primary ink | `--fyz-ink` | `#17202b` | — |
| Body text | `--fyz-text` | `#283441` | （原字面量） |
| Muted | `--fyz-muted` | `#596675` | — |
| Border | `--fyz-border` | `#dce3e8` | `--fyz-line` |
| Border strong | `--fyz-border-strong` | `#b9c3cd` | — |
| **Brand accent** | `--fyz-accent` | **`#174bb8`** | `--fyz-blue`, `--fyz-wire-blue`（收敛为同一深蓝） |
| Accent hover | `--fyz-accent-hover` | `#113d94` | （原 hover 字面量） |
| Accent soft | `--fyz-accent-soft` | `#e7edf9` | `--fyz-sky` |
| Success | `--fyz-success` | `#217a54` | — |
| Warning | `--fyz-warning` | `#9a6b1f` | — |
| Risk | `--fyz-risk` | `#b3403a` | `--fyz-wire-red`（仓库） |
| Desk 次级（eyebrow/细线/小标记） | `--fyz-teal` | `#168c7b` | 仅小面积使用 |
| 功能 wash | `--fyz-mint` | `#eef8f4` | blockquote 背景 |
| 深色功能块 | `--fyz-dark` | `#1b252e` | hero-story 覆盖层 |

- 三套蓝（2563eb / 1769aa / 174bb8-hover）→ **1 个 `--fyz-accent`**；RU 独立色板不存在（0.3.3 前 RU 首页内联样式仍自持色值——页面内容，0.3.5 迁移时收敛）。
- research-wire.css（仓库侧）：wire token 全部改为 canonical alias（`--fyz-wire-blue → var(--fyz-accent)` 等），随 0.3.4 阶段部署生效。

## 3. 字体（自托管，全部 `.woff2`）

| 文件 | 子集 | 大小 | 用途 |
|---|---:|---|---|
| inter-latin.woff2 | Latin | 48,256 B | UI/正文 |
| inter-cyrillic.woff2 | Cyrillic | 18,748 B | RU 正文 |
| noto-serif-latin.woff2 | Latin | 36,756 B | H1/H2/editorial |
| noto-serif-cyrillic.woff2 | Cyrillic | 24,572 B | RU 标题 |

- @font-face：`font-display: swap`、unicode-range 分片、`assets/fonts/` 自托管；**外部字体请求 = 0**。
- 字重体系：**仅 400/500/600/700 被调用**（750/800/850 调用 = 0，已归一为 700/600/500）；@font-face 声明范围 400–900（可变字体能力范围，用于覆盖 Neve/WC 遗留的 800 权重调用，见 §6）。
- H1/H2 → Noto Serif；H3/H4/card 标题、body、meta/eyebrow → Inter。

## 4. Network 数据（playwright 实测，EN/RU 文章页）

| 指标 | Before (0.3.4) | After (0.3.5) | 说明 |
|---|---:|---:|---|
| 字体请求数（EN 文章） | 1 | 2 | WC Inter + 我们的 Noto Serif latin |
| 字体请求数（RU 文章） | 1 | 3 | WC Inter + Noto Serif latin/cyrillic |
| 字体字节（EN 文章） | 326,628 B | 363,384 B | +36,756 B（Noto Serif latin，新增标题字体） |
| 字体字节（RU 文章） | 326,628 B | 387,956 B | +61,328 B（Noto Serif latin+cyrillic） |
| 字体 404 | 0 | **0** | 4/4 woff2 全部 200 |
| 外部字体域名 | 0 | **0** | 全同源 |
| CLS（EN/RU 文章） | 0.00006 / 0.00011 | 0.0111 / 0.0112 | 见 §7 说明 |
| LCP（EN 文章） | 2596 ms | 1768 ms | 本地网络样本，仅相对参考 |

**重要如实说明（Inter 326KB 未消除）**：每页 326 KB 的 `Inter-VariableFont` 来自 **WooCommerce 插件通过 WP 7.0 字体库（wp-fonts-local）注册的 Inter 300–900 全变量脸**，先于我们声明且 Chrome 级联优先采用它；我们的 Inter 子集已注册（CSS 文件 + `wp_head` 999 晚发标签），实测在 WC 脸缺席时会生效（阻断实验 + 本地 CORS 测试均证明文件与声明有效）。dequeue API 对 WP 字体库注册项无效。**正文 Inter 渲染已达成**（WC 脸与我们的子集同为 Inter 字型）；326KB 为插件既有行为（浏览器缓存后首访成本一次），消除它需要插件层授权（记入 0.3.6 候选）。

## 5. 视觉变化清单（本版"改漂亮"内容）

1. 品牌 accent 统一为深蓝 `#174bb8`（链接/焦点/表格头/归档卡片边线），hover `#113d94`；焦点环改用 accent-soft。
2. 文章标题 H1/H2/H3 从 Georgia/系统衬线 → **Noto Serif**（latin+cyrillic）；正文/导航/meta 从 Segoe UI → **Inter**（RU 页自动加载 cyrillic 子集）。
3. 字重归一：750→700（site-title）、800→600（eyebrow/error-code/TOC）、650→600、550→500。
4. 微交互 150ms：链接颜色、卡片 `translateY(-1px)`+边框、focus-visible；`::selection` accent 底白字；`prefers-reduced-motion` 保持关闭。
5. RU 排版：`html[lang^="ru"]` 下 body `hyphens:auto`、H1/H2 `manual`；代码/长 URL `overflow-wrap:anywhere`。
6. CFC 卡片/矩阵链接加 150ms 过渡（无布局变化）。
7. **首页视觉保持原样**（页面内联样式驱动，0.3.5 迁移前不改——其 Georgia 标题/Inter 元素在 Inter 就位后自动受益）。

## 6. 部署事实（快照 + 三方哈希，8 文件）

| 文件 | 动作 | 新 sha256 | 基线 sha |
|---|---|---|---|
| assets/css/design-system.css | replace | ECC007743B5A3E6C3C1546B1898DEF1F8C73B4DB5821A30B73688ACF8122C836 | fc60eeaa… |
| assets/css/cars-from-china.css | replace | 286B0D372EAFB7928263460F14250AC01992064342C89325369EB07B93742F7F | 1264cf7f… |
| style.css (0.3.5) | replace | 7534C12D934F33A8A992B0F710792A21D2DC1ECDA1F66D8EEAC648CFDDC50D65 | 08b5c9aa… |
| functions.php | replace | A145B9CF857D7A2BA70F0374F84AD5E279F057B6F9B96DA343B3A9C98475332E | d7c47040… |
| assets/fonts/*.woff2 ×4 | new | 3100E775… / 71D5EE93… / 46281456… / 54E3BD0C… | —（new） |

快照/回滚：`work/deployments/fyzsxnb-ui2-033/snapshots/`（含 functions_php、style_css、design-system_css、cars-from-china_css + 字体 stub）。
说明：functions.php 经历两轮补丁（晚发字体标签 → +dequeue 尝试），最终 A145B9CF… 为生产实际哈希。

## 7. 验收（FINAL=PASS 逐项）

- ✅ Inter/Noto Serif self-host 成功（4/4 200）；外部字体请求 = 0
- ✅ 750/800/850 调用 = 0（design-system/cars-from-china 实测；research-wire 仓库侧同 0）
- ✅ EN/RU 共用一套 token（无独立 RU 色板）；Desk 色仅 teal 小标记
- ✅ Cyrillic 无 fallback 跳变（Noto Serif/Inter cyrillic 子集按需加载）
- ✅ 长标题无裁切（H1/H2 不强制 hyphen、无固定高度）；390px overflow = 0（11/11 页）
- ✅ 字体 asset 404 = 0；CSS asset 404 = 0；Console error = 0（11/11）
- ✅ `!important`：design-system 2（不变）、cars-from-china 0、research-wire 3（仓库，不变）——scope 说明：审计表 5 = 三文件合计；生产实抓按文件分列
- ✅ canonical/hreflang/robots 不变（抽查通过）；RU hub noindex,follow 保持
- ✅ DOM/section 层级未重构（仅 CSS；截图结构指标 h1=1 全页一致）
- ✅ `/ru/cars-from-china/ lang=en-US` 记录为 **pre-existing mu-layer defect**（非本版新增，本版未修）
- ✅ Before/After 截图 11 页 ×4 组（before/after/after2/after3）已存 `qa/screenshots/ui2-033/`
- ⚠️ CLS 采样：文章页 0.0001→0.011（本机 cold 样本含未优化字体时序；LiteSpeed 合并 CSS + 本地网络波动，非结构性回归证据；font-display:swap 下无布局回退动画——以正式 Lighthouse 复查为准，若需可在放行前补跑）

## 8. 已知项 / 后续

- WC Inter 326KB 级联问题：记入 **0.3.6/插件授权**候选（消除需插件层或 WP 字体库层处理）。
- RU 首页内联色板与首页 Georgia 标题：待 0.3.5 首页迁移时收敛。
- research-wire.css（仓库侧）已同步 canonical alias，随 0.3.4 阶段部署。

## 9. 下一步

- 待用户审阅 Token 表 / Network 数据 / Before-After 后放行 **0.3.4 Article/Desk/Archive V2**。
- 仓库提交：`448a1e1`（53 files：tokens/fonts/functions/style.css/QA/截图）。

`FINAL=PASS`