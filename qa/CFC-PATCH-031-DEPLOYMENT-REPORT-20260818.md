# FYZSXNB CFC Patch 0.3.2 — 部署报告（RU Hub launch-gate guard）

`task_id`: FYZ-20260818-CFC-PATCH-031
`status`: **deployed_and_publicly_verified**
`FINAL=PASS`

## 1. 执行依据与边界

- 依据：`agent-handoff/DEEPSEEK-SITE-DEPLOYMENT-RUNBOOK.md` +
  `DEEPSEEK-SITE-DEPLOYMENT-COMMANDS.json`（CURRENT_TASK = 0.3.x 补丁包）。
- 用量守卫：`codex_usage_guard.mjs 5` 返回 `threshold_reached/pause_required`
  （剩余 5%）；**用户明确指令直接部署**（覆盖 pause），报告中如实记录。
- 写入范围：仅 2 个文件（`inc/cars-from-china.php` 替换、
  `style.css` 版本提升）；无 REST 写入、无文章/分类/taxonomy/DB/URL 改动。
- 凭据：全程经 `run_ftp_deploy_secure.ps1`（加密 clixml 注入），未输出任何凭据。

## 2. 部署前基线核查（重要发现）

快照显示生产 `inc/cars-from-china.php`（24243 B，sha `aa9b4c0f…`）与本地
0.3.0（23337 B）**sha 不同但内容逐行相同**（git 归一化 diff 为空）→ 仅
LF/CRLF 行尾差异（FTP 上传者以 CRLF 落盘）。结论：0.3.0 内容未漂移，
0.3.1/0.3.2 以**远端 CRLF 基线 + 追加行**构建，避免行尾噪音。

## 3. 实际部署（时间线 + 快照 + 三方哈希）

| 时间(UTC) | 文件 | 动作 | 远端 sha256（== source == manifest） | 快照/备份（回滚文件） |
|---|---|---|---|---|
| 09:08 | inc 0.3.0(CRLF) | snapshot | aa9b4c0f…658c | `snapshots\inc__cars-from-china.php\before-…php` |
| 09:10 | inc 0.3.1 (wp_robots) | deploy | d7048c00…ADFF8 | — |
| 09:11 | inc | FTP verify | d7048c00… ✅ | — |
| 09:11 | style.css 0.3.0 | snapshot | f72f0826…40f1 | `snapshots\style_css\before-…php` |
| 09:12 | style.css 0.3.1 | deploy | e057adf4…814B | — |
| 09:15 | inc 0.3.1 / style.css 0.3.1 | snapshot v2 | d7048c00… / e057adf4… | `snapshots\inc__…-v2\`、`snapshots\style_css-v2\` |
| 09:16 | inc 0.3.2 (wp_robots + wp_head meta) | deploy | f49c51a1…3B777 | — |
| 09:16 | style.css 0.3.2 (purge 触发器) | deploy | 0e38ae4f…2DA9D9 | — |

## 4. 发现与修复过程（差异记录）

1. 0.3.1 上传后公开页 robots 仍无 noindex → 判定：AIOSEO 生成的
   `max-image-preview:large` meta 不受 `wp_robots` 增补影响 + LiteSpeed
   桌面 UA 桶缓存时序干扰。
2. 修复：守卫升级为**双通道**——`wp_robots` 过滤（core 机制）+
   `wp_head` priority 999 显式输出 `<meta name="robots" content="noindex, follow">`
   （多 meta 并存时爬虫取最严格；AIOSEO 无法覆盖）。
3. style.css 版本 0.3.0→0.3.1→0.3.2：两次触发
   `fyzsxnb_purge_design_cache_once`（一次性 LiteSpeed purge，设计内机制）。
4. 代码增量 = 44 行（0.3.1）+ 21 行（0.3.2 wp_head 通道），共 65 行新增，
   无既有代码修改；lint（PHP 8.5.9）全部通过。

## 5. 公开页验收（2026-08-18，多 UA + 随机参数缓存对照）

| URL | 状态 | robots | 判定 |
|---|---|---|---|
| /ru/cars-from-china/（含 ?cb/?rnd） | 200 | `max-image-preview:large` + **`noindex, follow`** | ✅ 保留 200 + noindex |
| /cars-from-china/volkswagen/ | 200 | 无 noindex | ✅ 守卫仅限 RU hub |
| /cars-from-china/volkswagen/tayron/ | 200 | 无 noindex | ✅ |
| /（首页）/ru/ | 200 / 200 | 无 noindex，h1=1，无 fatal | ✅ 无回归 |
| /cars-from-china/（EN hub draft） | 404 | noindex | ✅ 仍 draft |
| /sitemap.xml | 200 | 不含 cars-from-china | ✅ |

## 6. 缓存/rewrite 状态

- LiteSpeed：版本触发式 purge 已执行两次（0.3.1、0.3.2）；桌面 UA 桶
  在新 purge 后重渲染带 noindex（初测延迟为缓存沉降，已复测通过）。
- rewrite 未变动（RU hub 200 依赖既有 flush，未重新 flush）。

## 7. REST 写入

无（本补丁纯文件替换；此前 TAY-01/02 发布不在本报告范围，见
`agent-handoff/results/FYZ-20260818-CFC-DEPLOY-001/RESULT.md`）。

## 8. 回滚

- 回滚文件（备份）：
  - `snapshots\inc__cars-from-china.php-v2\before-fyzsxnb-p0-seo-patch.php`（0.3.1）
  - `snapshots\style_css-v2\before-fyzsxnb-p0-seo-patch.php`（0.3.1）
  - `snapshots\inc__cars-from-china.php\before-…`、`snapshots\style_css\before-…`（0.3.0）
- 回滚命令：`run_ftp_deploy_secure.ps1 -Action rollback -SnapshotPath <对应
  snapshot.json> -RemotePath wp-content/themes/fyzsxnb-neve-child/<path>`
  （按部署相反顺序），随后清缓存并复核首页（runbook §3.4）。
- 本部署**未使用回滚**。

## 9. 分级

- P0：无（首页/ru/ 全程 200，无 fatal，无 canonical/lang 回归）
- P1：无
- P2：桌面 UA 桶在版本 purge 后需短暂沉降（缓存时序，非代码缺陷）；
  建议后续版本 bump 后探测带随机参数 URL 先行。

## 10. 下一步

- 解锁 gate 时：将 `FYZSXNB_CFC_LAUNCH_GATE_OPEN` 置 true（或删除守卫），
  并重测 RU hub robots。
- 仓库同步：repo `inc/cars-from-china.php` 与 `style.css`（0.3.2）已同步提交，
  与生产一致。

`FINAL=PASS`（部署+验收完成；回滚文件齐备；无生产范围外变更）