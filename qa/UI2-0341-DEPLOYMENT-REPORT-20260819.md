# FYZSXNB UI V2 0.3.4.1 — legacy cleanup hotfix 部署报告

`package`: fyzsxnb-ui2-0341（主题版本 0.3.7 → **0.3.8**）
`status`: **deployed_and_publicly_verified** — `FINAL=PASS`
`date`: 2026-08-19

## 1. 范围（用户指定 4 项，无新设计）

1. 研究文章评论输出关闭（模板层，非 CSS hide；数据保留）
2. 移除 `Neve | Powered by WordPress` legacy footer branding，用 FYZSXNB 统一 Footer（不改 IA）
3. RU 日期 locale（中心化 helper，非逐篇手写）
4. **Archive 主查询语言隔离**（只在 archive query 层；不碰 homepage feeds plugin）— 本次唯一 blocker

## 2. 实现

| 项 | 方式（theme 层） |
|---|---|
| 评论 | `comments_template` 过滤器 → 空模板 `comments-disabled.php`；`comments_open` → false（is_singular('post')） |
| footer | `theme_mod_footer_copyright_content` 过滤器 → `© YYYY FYZSXNB — Research Desk / — исследовательский деск`（Neve footer builder 渲染时覆盖，无 DB 写） |
| RU 日期 | `fyzsxnb_local_date($ts)`：RU 视图用 `IntlDateFormatter('ru_RU', LONG)` → `18 августа 2026 г.`；EN 视图用 `wp_date`；中心月名回退表；壳内 Published/Updated/Related 三处接入 |
| Archive 隔离 | `pre_get_posts`（main query + is_archive + !admin + !feed）：RU 视图 `category__in=[54]`；EN 视图 `category__not_in=[54]`；分页继承 |

## 3. 验收（单一确定性 UA + cache-busted，实抓）

### 3.1 Article
| 检查 | ru-TAY01 | ru-TAY02 | en-DQ381 |
|---|---|---|---|
| comment strings | 0 | 0 | 0 |
| has comment area | 无 | 无 | 无 |
| Powered by WordPress / Neve \| | 0 | 0 | 0 |
| footer | © 2026 FYZSXNB — исследовательский деск | 同 | © 2026 FYZSXNB — Research Desk |
| **Published 日期** | **18 августа 2026 г.** | **18 августа 2026 г.**（俄文 ✅） | Published: August 18, 2026（EN 不变 ✅） |
| H1 / overflow | 1 / 0 | 1 / 0 | 1 / 0 |

### 3.2 Archive 隔离（真实文章数）
| archive | posts | RU→EN 泄露 | EN→RU 泄露 | 说明 |
|---|---:|---|---|---|
| /category/china-tech-products/ p1 | 17 | **0** | — | ✅ |
| /category/china-tech-products/ p2 | 9 | **0** | — | ✅ 分页 2 |
| /category/product-research/ p1 | 10 | **0** | — | ✅（redmagic 为英文，属合法 EN，非泄露；初测曾误标，已复核为测试清单错误） |
| /category/russian-library/ p1 | 17 | — | **0** | ✅ 全部 RU |

product-research 无第 2 页（page/2 → 404，正常单页）。

### 3.3 回归
- archive URL / canonical / H1 不变（h1=1，URL 原样）；390px 无 overflow；TOC 未动；homepage 未动（feeds plugin 未碰）。

## 4. 部署事实（快照 + 三方哈希，3 文件）

| 文件 | 动作 | 最终 sha256（== source == remote == manifest） |
|---|---|---|
| functions.php | replace（经 2 轮：初始构建→日期锚点修正） | 441FBA91EE2B6F5E1536D9AE6F2A80D53ED4D7105AD232D50B28ED5DFC688FDB（17786B） |
| comments-disabled.php | new | 625E19BA16F1773DF1DCDF4422BE8668C1AC29A37DC074B6CF5A45147D1A6B8D |
| style.css (0.3.8) | replace | 510E77244007B5E3F696CB3B47DBF72E763A709A47A8E9A13E6C106FF2B03C47 |

快照/回滚：`work/deployments/fyzsxnb-ui2-0341/snapshots/`（functions_php、style_css 备份=回滚文件）。

## 5. 过程中发现与修正（如实记录）

- **日期替换首次构建未命中**（锚点不匹配且缺断言）→ 用精确锚点+断言重建、重部署后 RU 日期生效（`18 августа 2026 г.`）。
- **acT 工具「product-research 泄露」为测试清单误标**（把英文 redmagic 写进了 RU 禁止清单）→ 复核为 0 真实泄露。
- **LiteSpeed 按 UA 提供缓存变体**：验收统一用单一 UA + cache-busted 参数重抓，避免变体误导（headless 变体曾显示旧 HTML）。

## 6. FINAL=PASS

四项全过；`archive 隔离` blocker 解除（真实文章数 17/9/10/17，0 泄露）；URL/canonical/H1/390px/TOC/homepage 无回归；未改 feeds plugin、未动 326KB 根因（按前议记录在案）。

## 7. 下一步

- 待用户确认后放行 **0.3.5 Homepage Template Migration**。
- 仓库提交：`00be55c`（functions.php / comments-disabled.php / style.css 0.3.8 / ack-0341.cjs）。

`FINAL=PASS`