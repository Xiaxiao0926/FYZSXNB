# FYZSXNB UI V2 0.3.4 — Article / Desk / Archive V2 部署报告

`package`: fyzsxnb-ui2-034（主题版本 0.3.6 → **0.3.7**，阶段内一次修正性 bump 用于强制重建 LiteSpeed 合并 CSS）
`status`: **deployed_and_publicly_verified** — `FINAL=PASS`
`date`: 2026-08-19

## 1. 范围与原则

Article/Desk/Archive 呈现层 V2；**未改动**：首页 Custom HTML、feeds、mu-plugin、查询、URL、permalink、sitemap、canonical/hreflang、CFC `lang=en-US`、文章正文、H2 增删、俄文改写、WP 字体注册根因（326KB 未在本版动）。

## 2. 交付内容

1. **Article V2 shell**（functions.php，钩子 `neve_before_post_content` / `neve_after_post_content`）：
   - 面包屑 + Desk eyebrow（H1 上方）；meta 行（Published/Updated/研究类型/编辑部/语言，仅真实数据）；deck（真实 excerpt）。
   - Neve 默认 meta 列表隐藏（显示层）；Related Research（同 locale 严格隔离，不足 3 篇就少显示—实测 2 篇）；Research CTA ×1（文章底部，EN/RU 本地化，链接 /contact/）。
2. **Article 布局**（design-system.css）：阅读列 **660px（≈68–72ch）**；TOC 左列 220px + 正文右列（仅当 JS 构建了 TOC，`fyz-has-toc` 类触发 grid）→ **JS-off 无空白侧栏**；390px 单列、TOC 折叠块（`details`，JS 按宽度设 open）。
3. **TOC（research-wire.js 正式部署）**：vanilla、feature-scoped（`[data-fyz-toc]`→判定 `.single-post .nv-content-wrap`）、footer 内联（见 §5）、H2+H3（H4 不进）、anchor 去重修复、Back to top；双语：EN `Contents / On this page / Back to top`，RU `Содержание / На этой странице / Наверх`。
4. **Desk/Archive**：research-wire.css 上线（archives 转为研究索引列表、TOC/sources/evidence 组件样式）；Archive/Desk 内容结构未改。
5. **首页冻结守护**：wire css 的 `.fyz-design-system.home` 暖纸背景被高特异性覆盖回 `--fyz-bg`（白）——0.3.4 不改首页；0.3.5 迁移时再定。
6. **CFC**：进入统一 token/字体体系（car-from-china.css 已 token 化）；数据/SEO/URL/locale 未动。

## 3. Neve remnants 对照（验收表）

| Page | Before | After | Result |
|---|---|---|---|
| EN Article（513） | 默认 Neve meta/title/body，无 TOC/无壳 | FYZ article shell（面包屑+meta+deck+双列 TOC+related+CTA） | **PASS** |
| RU Article（510） | 默认 single template | FYZ article shell（RU 文案、Содержание、Наверх） | **PASS** |
| Desk（category 50） | 默认 archive | FYZ Archive/Desk 列表（wire 样式+本地化 eyebrow） | **PASS** |
| Archive（54） | 默认 archive | FYZ Archive V2 列表 | **PASS** |

核心研究页已脱离默认 Neve 主导 → **达成**。

## 4. TOC 专项验收（toc-acceptance.cjs，exit=0）

| 用例 | TOC | 链接 | 标签 | 布局 | anchor 破损/重复 | overflow |
|---|---|---|---|---|---|---|
| EN 长文 1440/390 | ✅ | 13 | Contents / On this page / Back to top | 220+366 / 360 | 0/0 | 0 |
| RU 超长标题 1440 | ✅ | 10 | Содержание / На этой странице / Наверх | 220+366 ✅（长 RU 标题未裁切） | 0/0 | 0 |
| EN 含表文章 1440 | ✅ | 10 | Contents | 220+366 | 0/0 | 0 |
| RU 文章2（GPF）1440/390 | ✅ | 14 | Содержание | 220+366 / 360 | 0/0 | 0 |
| **JS-off（模拟）** | ❌（无空侧栏、正文完好） | 0 | — | 单列 | 0 | 0 |

复测 EU/RU 双语、Cyrillic/型号标题、重复 id 规避、Back to top 目标有效均已覆盖。

## 5. 重要发现与处理（LiteSpeed JS defer）

- 该主机 LiteSpeed "Load JS Deferred" 把**所有 JS（外部+内联）**改成 `type="litespeed/javascript"` 并在**用户交互（滚动/点击）后**执行（QA headless 空闲时 jQuery 10s 不执行）。这是宿主/插件级既有行为。
- 处理：TOC 改为 **footer 内联打印 research-wire.js 文件内容**（文件仍是唯一源；`__fyzTocLoaded` 幂等守卫）；真实用户必然交互 → 正常；headless 验收注入交互模拟。外部 enqueue 已移除，避免受 defer 影响。
- 该行为导致全站 defer JS（含 jQuery/Neve 菜单）依赖交互——**记录在案**，不属本版范围，建议后续宿主/插件配置核查（记入 0.3.6/治理）。

## 6. 性能差异（同页，playwright；本地样本仅相对参考）

| 指标 | Before (0.3.5) | After (0.3.7) | 说明 |
|---|---:|---:|---|
| CSS 合并文件 | ~200KB 级 | 增加（wire 上线 + article 壳） | 无 404；合并文件正常 |
| JS 字节（页面内联 TOC） | 0 | ~3.5 KB（inline，footer） | vanilla/defer 语义 |
| 请求数（文章） | 基础 | +0（TOC 内联无额外请求；wire css 入合并文件） | research-wire.js 200 ✅ |
| CLS（EN 长文 1440） | 0.0005 | 0.0006 / 0.081（TOC 注入后） | TOC 注入产生小幅 CLS；测试含交互时序 |
| Console error | 0 | **0** | 全部用例 |
| 字体 | 同 0.3.3（见 0.3.3 报告） | 不变 | — |

## 7. 部署事实（快照 + 三方哈希，5 文件）

| 文件 | 动作 | 最终 sha256（== source == remote == manifest） |
|---|---|---|
| functions.php | replace（2 轮：shell+enqueue → 内联 TOC） | 1CE798BE727D718ED348E9C98AE93635DE0F52AD3A12CAE1E32AEDFA8CD3E772（14337B） |
| assets/css/design-system.css | replace（含 home-bg 冻结覆盖） | A9BF4AF9C3ED531319A7E59F4FD29B739EA3EB44EA5BAB272D8380A391EFE782（18624B） |
| assets/css/research-wire.css | new | DF8C608D56EA59C7E92641F8C31699A9E911379B9DD13854ACFE75A30A58F703（10836B） |
| assets/js/research-wire.js | new→replace（守卫） | A3944B3AC7487C789C36740AFE0E20568A5AE54638FD7D71F71282A877E136B8（3559B） |
| style.css | replace ×2（0.3.6→0.3.7 purge 触发） | 25ECC268B73694DD7A51D9234B0111F99DDCD9170C6A79B320EA9CAF89EC8334 |

快照/回滚：`work/deployments/fyzsxnb-ui2-034/snapshots/`（functions_php、design-system_css×2、style_css×2、research-wire_js 等，备份=回滚文件）。

## 8. FINAL=PASS 条件核对

- ✅ Article/Desk/Archive V2 生产生效；核心页不再默认 Neve 主导
- ✅ EN/RU typography 正常；RU 超长 H1 无裁切（92 字符 H1 未截断）
- ✅ TOC 双语 + JS-off 可读 + anchor 唯一 + Cyrillic/型号标题正常
- ✅ Related 不跨 locale（RU 页实测 2 篇均 RU）；Header/Footer IA 未动
- ✅ canonical/hreflang/robots/sitemap 不变；CFC noindex 契约不变；CFC `lang=en-US` 记录为 pre-existing
- ✅ research-wire.js 200；console error=0；JS/CSS/font 404=0
- ✅ 390px 页面级 overflow=0（table 局部滚动）；homepage 冻结（白底恢复）
- ✅ 生产 hash / Git / manifest 一致；回滚文件齐备（未用回滚）
- ⚠️ CLS：TOC 注入带来小幅 CLS（0.0–0.11 区间，受字体/交互时序影响）——记入，正式 Lighthouse 复查建议在 0.3.5 前补跑一次

## 9. 下一步

- 待用户审阅 Article 阅读体验 / Desk 入口 / TOC 过度设计三项后放行 **0.3.5 Homepage Template Migration**。
- 仓库提交：`3849645`（70 files；toc-acceptance + ui2-034-shots + 截图）。
- 宿主级 JS defer（交互触发）与 326KB Inter：记录在案，归入后续治理。

`FINAL=PASS`