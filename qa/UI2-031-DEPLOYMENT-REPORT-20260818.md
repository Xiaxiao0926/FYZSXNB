# FYZSXNB UI V2 0.3.1 — Language & Content Correctness 部署报告

`package`: fyzsxnb-ui2-031（主题版本 0.3.2 → **0.3.3**）
`status`: **deployed_and_publicly_verified** — `FINAL=PASS`
`date`: 2026-08-18

## 1. 范围（用户定稿 0.3.1 清单）

- CFC RU hub eyebrow `AUTOMOBILI IZ KITAYA` → **АВТОМОБИЛИ ИЗ КИТАЯ**
- RU brand/model 页 eyebrow → 俄语（EN 视图保持 `CARS FROM CHINA`）
- Archive eyebrow 按 locale 输出（`Архив исследований` / `Research archive`）
- 404.php 全量本地化（标题/说明/链接，按 `fyzsxnb_is_russian_view()`）
- 动态计数/时效统计核查（见 §3：无计数问题；АВТОСТАТ 合规，未改）
- **明确不做**：TOC（research-wire.js 未部署）、页面内容大改、0.3.2+ 事项

## 2. 部署事实（快照 + 三方哈希）

| 文件 | 动作 | 新 sha256（== source == remote == manifest） | 基线 sha（快照备份=回滚文件） |
|---|---|---|---|
| functions.php | replace | D7C47040796BCFA1E4313C5730444876588C9D5C0482C567BE846CD461CFEC47 | 11613135… |
| inc/cars-from-china.php | replace | E55FB42F97CC851720B3B2A1317D0C6FDE67CB7E8960906A9892B932BB629380 | f49c51a1… |
| style.css (0.3.3) | replace | B6C162B2A9BACE17665F0D32BC707618BE2B9A11683EDEDF9C4F46684FEFFD1C | 0e38ae4f… |
| 404.php | replace | E61429209FB6C4D349B8AF7E0B8137E07990F1DE887C2E9E63BFE163D8E013EF | b4eb1628… |

快照目录：`work/deployments/fyzsxnb-ui2-031/snapshots/{functions_php,style_css,404_php,inc__cars-from-china.php}/before-*.php`
（每文件独立目录，回滚 = `run_ftp_deploy_secure.ps1 -Action rollback -SnapshotPath <对应 snapshot.json> -RemotePath wp-content/themes/fyzsxnb-neve-child/<path>`，按部署相反顺序）

## 3. 生产事实核查（0.3.1 决策依据）

- **RU 首页 АВТОСТАТ 统计**（page 400）：`"по данным «АВТОСТАТ», за январь–август 2025 года…в апреле 2026 года доля Китая…22,7%"` — **已带时期与来源**，按定稿规则（有可靠时间与来源 → 显示截至 YYYY-MM）**判定合规，未改动**。
- **首页硬编码计数**：EN/RU 首页内容扫描 `top N / N signals/guides/articles` → **0 处**，无计数类问题。
- **RU 404 语言判定**：基于路径前缀（/ru/）；`/ru/*` 下 404 显示俄语、裸路径显示英语 —— 符合既有语言契约，非缺陷。

## 4. 公开页验收（多 UA + 随机参数绕过缓存）

| 页面 | 结果 |
|---|---|
| /ru/cars-from-china/ | 200，eyebrow=АВТОМОБИЛИ ИЗ КИТАЯ，**noindex,follow 保持**，H1=1 |
| /ru/cars-from-china/volkswagen/、.../tayron/ | 200，eyebrow=АВТОМОБИЛИ ИЗ КИТАЯ，H1=1 |
| /cars-from-china/volkswagen/、.../tayron/（EN） | 200，eyebrow=CARS FROM CHINA（不变） |
| /category/russian-library/ | 200，eyebrow=Архив исследований |
| /ru/nonexistent-…/（RU 404） | 404，标题=Страница не найдена |
| /nonexistent-…/（EN 404） | 404，标题=This page could not be found |
| 首页 /、/ru/ | 200，H1=1，无 fatal |
| /feed/ | 200，EN/RU 文章均在 |
| /sitemap.xml | 200，无 cars-from-china |

全部页面无 PHP fatal；canonical/lang/robots 无回归（结构检查 27/27 通过）。

## 5. REST 写入

无（纯主题文件替换；页面内容未改）。

## 6. 缓存/rewrite

- 版本 0.3.3 → `fyzsxnb_purge_design_cache_once` 触发一次性 LiteSpeed purge；
- CFC 常量 `FYZSXNB_CFC_VERSION` 0.1.0→0.1.1（卫生性 bump）；
- rewrite 未动。

## 7. 分级与回滚

- P0：无（首页/ru/ 200，无 fatal，SEO 无回归）
- P1：无；P2：无
- 回滚未使用；回滚文件齐备（§2 快照）。

## 8. 版本说明（编号映射）

用户阶段标签 `0.3.1`（UI V2 路线）与生产主题版本号**独立**：本次部署后主题版本 = 0.3.3（单调递增，0.3.2 为 RU-hub noindex 补丁）。后续阶段 0.3.2→0.3.6 将依次部署为主题 0.3.4→0.3.8。

## 9. 下一步

- 待用户放行 **0.3.2 CSS Hygiene**（死 token/alias/归档样式收敛，不换色）。
- 仓库提交：`16be5de`（repo 与生产一致）。

`FINAL=PASS`