# FYZSXNB TAKEOVER — Resolver Production Forensics Gate 001

**Task ID:** `FYZSXNB-TAKEOVER-RESOLVER-FORENSICS-001`
**执行性质:** `READ-ONLY FORENSICS`（零生产写入；仅 FTP 下载/HTTP GET/REST GET/文件读取/hash/diff）
**执行日期:** 2026-08-26
**执行人:** DeepSeek（接手扫描执行线）

---

## Executive Summary

生产当前运行的 mu-plugin 是 **v1.3.1（`8aa9aa8a`，21,891B）**——最后一次有据可查的更新是 **2026-08-10**（MULTILINGUAL-HOMEPAGE-REVISION-001 经 FTP 部署），文件内容与 **7-31 SITE-P0-TECH-IMPLEMENTATION-001 的 implementation 副本完全一致**。**Resolver V2（v1.4.0）从未上传生产**：全工作区不存在任何 `e31f837b` 对应的真实文件；0.4.5-E2/F2/045G 报告名为"生产验证/切换/观察"，但对应 QA 脚本（`resolver_v2_production_validation_045F2.py`、`resolver_v2_health_check_045G.py` 等）均为**纯本地 Python 模拟（零网络调用），并直接生成报告文本**——报告是自我声明，不是执行记录。0.4.5-F1 报告本身声明 `ACTUAL_SWITCH: NO`。根因分类：**C. FALSE_OR_INCOMPLETE_DEPLOYMENT_RECORD**。生产 Legacy 运行稳定（实测 5 样本 SEO 输出全部正确、200 OK、102 篇文章正常），**推荐暂保 Legacy**，V2 若需上线须走全新、带真实生产证据的 Gate。

---

## 1. Current Production Facts（2026-08-26 实测）

| 资产 | 生产哈希 | 大小 | 版本 | 与本地工作树 |
|---|---|---|---|---|
| `wp-content/mu-plugins/fyzsxnb-p0-seo-patch.php` | `8aa9aa8af1da…` | 21,891B | **v1.3.1（Legacy，无 V2 flag）** | ❌ 本地 `7c042a45`（29,819B，v1.4.0） |
| `wp-content/plugins/fyzsxnb-home-dynamic-feeds/...` | `4997b9697cf7…` | 26,878B | v1.2.5 | ✅ 一致 |
| `wp-content/themes/fyzsxnb-neve-child/functions.php` | `3acf887a8e0d…` | 18,383B | theme 0.3.11 系 | ✅ 一致 |
| `wp-content/themes/fyzsxnb-neve-child/inc/home.php` | `d15f32fb0f68…` | 20,149B | — | ✅ 一致 |
| `wp-content/themes/fyzsxnb-neve-child/inc/cars-from-china.php` | `c86250af8da4…` | 34,043B | — | ✅ 一致 |
| `wp-content/themes/fyzsxnb-neve-child/style.css` | `281199d403fe…` | 228B | Version 0.3.11 | — |
| `fyzsxnb-translation-pairs` 插件 | **不存在（FTP 550）** | — | 0.4.0 LOCAL_PASS 未部署 | — |

内容：**102 published / 0 pending / total 104**（8-21 后 CARS/Automotive 系新增 6 篇）。wp-config.php **未读取**（凭据安全红线），flag 状态记录 UNVERIFIED——但 mu-plugin 无 V2 代码，flag 即使存在也无效果。

---

## 2. Resolver Timeline

| 时间 | 事件 | 来源 | 声称版本/状态 | hash | 生产部署证据 | 生产验证证据 | 回退证据 | 可信等级 |
|---|---|---|---|---|---|---|---|---|
| 07-31 | SITE-P0-TECH-IMPLEMENTATION-001 产出 mu-plugin 实现文件 | `results/FYZ-20260731-.../RESULT.md`（明确 "No production deployment"） | v1.3.1 准备态 | `8aa9aa8a` | **NO**（任务自述未部署） | NO | NO | HIGH（文件存在） |
| 08-10 | MULTILINGUAL-HOMEPAGE-REVISION-001 经 FTP 更新 mu-plugin | `results/FYZ-20260810-MULTILINGUAL-.../RESULT.md`（"updated through the established FTP deployment path"） | v1.3.1（hreflang 增强） | `8aa9aa8a` | **YES**（FTP 路径 + 回滚副本记录） | 部分（result 记录） | 回滚副本存在 | **HIGH** |
| 08-20~21 | 0.4.0/0.4.1 本地冻结并提交（translation-pairs 插件、locale detector） | git `3355f3c` / `9feb0ab` | LOCAL | — | NO（本地） | NO | NO | HIGH |
| 08-21 | 0.4.5-E 生产 canary P1 | `docs/PRODUCTION-CANARY-P1-DEPLOYMENT-REPORT-045E.md` | flag=false，DEPLOYMENT_PREPARATION_PASS | — | **NO 上传动作声明** | 无 | 无 | MEDIUM（“准备”报告） |
| 08-21 | 0.4.5-E2 内部 canary | `docs/PRODUCTION-CANARY-INTERNAL-REPORT-045E2.md` | V2=False，INTERNAL_CANARY_PASS（13/13） | — | 无上传证据 | **NO**（X-FYZ-Resolver-V2 header 机制仅存在于 v1.4.0，生产 v1.3.1 无此机制 → 不可能是生产实测） | NO | **LOW** |
| 08-21 08:55 | 0.4.5-F1 切流准备 | `docs/FULL-SWITCH-READINESS-REPORT-045F1.md` | **`ACTUAL_SWITCH: NO`（生产未做任何开关修改，仍 Legacy）** | 声称 v1.4.0=`e31f837b`、v1.2.5=`d572e192` | **NO** | NO | NO | LOW（指纹存疑：`d572e192` 实为 v1.2.4；`e31f837b` 无对应文件） |
| 08-21 08:58 | 0.4.5-F2 全量切换 | `docs/FULL-SWITCH-EXECUTION-REPORT-045F2.md` | **V2_ACTIVE / FULL_SWITCH_SUCCESS（14/14 live tests）** | 无上传记录 | **NO 文件/上传证据** | **NO**（验证脚本为本地模拟，见 §4） | NO | **LOW**（与 F1 三分钟前 "ACTUAL_SWITCH: NO" 直接矛盾） |
| 08-21 09:00 | 0.4.5-G 稳定观察启动 | `CURRENT_TASK.json` / `tasks/0.4.5-G/` | V2 active_stable，观察中 | 记录生产 mu=`e31f837b` | NO | NO | NO | LOW（记录与 8-26 实测矛盾） |
| 08-21 09:05 | 0.4.5-G 观察报告 | `docs/RESOLVER-V2-STABILITY-REPORT-045G.md` | OBSERVATION_COMPLETE_PASS | — | NO | NO | NO | LOW（本地模拟脚本生成） |
| 08-26 | 本次取证实测 | FTP verify / HTTP GET | **生产 = v1.3.1 Legacy** | `8aa9aa8a` | — | — | — | **HIGH（直接实测）** |

---

## 3. Version & Hash Matrix

| 版本标识 | 文件存在? | SHA256 | 大小 | V2 flag | 位置 |
|---|---|---|---|---|---|
| 生产当前 | ✅ (FTP 实测) | `8aa9aa8af1da6d84dc0362c8497e2804d66202fb0368702886b22c3bdb3ed54d` | 21,891B | 无 | 生产 + 本地 7-31 implementation 副本（hash 一致） |
| 本地工作树 | ✅ | `7c042a451544…` | 29,819B | **有**（`FYZ_USE_RESOLVER_V2`，env 可覆盖，默认 false） | `fyzsxnb-ui-v2/mu-plugins/`（未提交） |
| 历史 e31f837b | ❌ **无任何文件对应** | `e31f837b39c8…`（仅出现在 045F1 报告与 CURRENT_TASK 文本） | — | 声称有 | **HISTORICAL_BINARY_NOT_FOUND**（报告文字引用，未伪造） |
| 8-26 取证快照 | ✅ | `8aa9aa8a…`（下载备份） | 21,891B | 无 | `work/tmp/prod-snapshot-0826-forensics/` |

> 045F1 声称 "v1.4.0 = e31f837b" 且 "工作区干净，无未追踪脏代码"（实际 142 项 dirty）——该报告多项陈述与实际文件不符，可信度 LOW。

---

## 4. Code Diff Findings

### Level A — 文件级
- 生产（Legacy v1.3.1） vs 本地（V2 v1.4.0）：331 行 diff；本地多 7,928B。
- 本地 V2 新增/改造核心：`fyzsxnb_is_resolver_v2_enabled()`、`fyzsxnb_resolve_content_locale($post_id)`（metadata-first + legacy fallback + unknown）、`fyzsxnb_get_locale_trace($post_id)`；`fyzsxnb_is_russian_target` 从无参改为 `$target_id=null`；`fyzsxnb_set_inlanguage_recursive` 增加 `$target_locale` 参数。Hub/feed/blog-h1/hreflang/render_home_hreflang 等其余功能两版一致。

### Level B — Resolver 语义级
| 语义 | Legacy（生产 v1.3.1） | V2（本地 v1.4.0） | 生产实测 |
|---|---|---|---|
| 主判定 | **Hard-coded RU ID map**（`fyzsxnb_get_russian_post_ids()` 白名单，如 {400,448,445,442,441,434,…}） | **metadata-first**（`_fyz_content_language` → legacy 白名单 fallback → unknown） | Legacy（ID map + cat54 类判定） |
| zh 支持 | 无 | 有（zh-CN） | 无（N/A，无 zh 文章） |
| 分发器 flag | 无（不存在 flag 代码） | `FYZ_USE_RESOLVER_V2` + canary header `X-FYZ-Resolver-V2` | 无（flag 机制不存在于生产代码） |
| hreflang 来源 | 仅首页 11↔400（`fyzsxnb_render_home_hreflang`） | 同（文章级 0 标签，与 V2 报告一致） | 与 Legacy 设计一致 |
| 语言切换器 | 无文章级配对切换器 | 无新增切换器逻辑（配对在独立 translation-pairs 插件，未部署） | 无 |
| canonical | 自指向 | 自指向 | ✅ 5/5 自指向 |
| 配对解析（translation pair） | 无（0.4.0 插件未部署） | 依赖未部署的 translation-pairs 插件 | SAMPLE_D: 无配对功能 = **SAMPLE_NOT_AVAILABLE** |

---

## 5. Runtime Behavior Tests（2026-08-26 HTTP GET，5 样本全 200）

| 样本 | 类型 | 结果 |
|---|---|---|
| A1: 513 EN（历史配对 Tayron DQ381） | 配对 EN | lang=en-US ✓ og:locale=en_US ✓ inLanguage=en-US ✓ 无 hreflang、无切换器 |
| A2: 514 RU（历史配对 RU） | 配对 RU | lang=ru-RU ✓ og:locale=ru_RU ✓ inLanguage=ru-RU ✓ 无 hreflang、无切换器 |
| B1: 640 RU（新 CARS overview，metadata 配对单边） | 新 metadata 文 | lang=ru-RU ✓ … ✓ 无配对输出（EN 对应文不存在→SAMPLE_B 配对侧 SAMPLE_NOT_AVAILABLE） |
| C1: 350（Legacy 白名单文，无 cat54） | 仅 legacy map | lang=ru-RU ✓（Legacy map 生效） |
| E1: 未配对 EN 文章 | 未配对 | lang=en-US ✓ … ✓ 无 hreflang/切换器 |

全部样本 HTML 无 `FYZ_USE_RESOLVER_V2` / `X-FYZ-Resolver-V2` 痕迹。

---

## 6. SEO Output Tests

- Canonical：5/5 自指向，无漂移。
- Hreflang：首页级存在（11↔400，此前 0.3.x 基线）；**文章级全部为空**——与 Legacy 设计一致，与 045F2 声称的"文章级 0 漂移"表述相容（它本来就不该有）。
- lang/og/schema：全部正确（en-US/en_US / ru-RU/ru_RU）。
- **结论：CURRENT_SEO_PAIR_OUTPUT = CORRECT（按 Legacy 契约）**；"V2 的配对/zh 能力"在生产从未存在，无回归可言。

---

## 7. Deployment History Evidence（CLAIM vs EXECUTION vs LIVE）

| 证据类型 | 内容 | 判定 |
|---|---|---|
| 高可信（LIVE） | 8-26 FTP 实测生产 mu-plugin=`8aa9aa8a`（v1.3.1）；HTTP 实测无 V2 行为 | 生产从未跑 V2 |
| 高可信（历史文件） | 7-31 任务自述 "No production deployment"；8-10 任务是最后 mu-plugin 真实 FTP 更新（hash 与生产一致） | V2 从未上传 |
| 中可信 | 无 FTP 部署日志/三方哈希 manifest 显示 e31f837b 或 v1.4.0 上传（deployments/ 无 0.4 包） | 无上传记录 |
| 低可信 | 045E2/F2/045G 报告 + CURRENT_TASK（自述 PASS/ACTIVE） | 与文件证据矛盾 |
| **脚本自生成报告** | `resolver_v2_production_validation_045F2.py` 纯本地模拟（无网络），尾部 `f.write(...)` 直接写出 `FULL-SWITCH-EXECUTION-REPORT-045F2.md` 全文；`resolver_v2_health_check_045G.py` 同模式 | 报告=模板声明 |

**V2_WAS_EVER_CONFIRMED_IN_PRODUCTION = NO**（无任何层级的生产部署/验证证据；所谓 "V2_ACTIVE" 全部来自本地模拟脚本自生成文档）。

---

## 8. Potential Overwrite Paths（site-ops 只读审计）

- ⚠️ **`ftp_p0_deployer.ps1` / `run_ftp_deploy_secure.ps1` 默认 `$RemotePath = 'wp-content/mu-plugins/fyzsxnb-p0-seo-patch.php'`** —— 任何调用者若忘记显式传 RemotePath，会**默认覆盖 mu-plugin**。此为**高风险默认参数陷阱**（本次取证全程显式传参，未触发）。
- `snapshot_remote_files.ps1`、`hub_deployer.py`、`deploy_frontend_patch.py`、`deploy_home_inc.py`、`deploy_ui_fixes.ps1` 等 8-21+ 脚本未见 mu-plugin 上传逻辑（grep 无命中）。
- **不存在"全量部署包拖带 mu-plugin"的已发生路径**：生产 hash 与 8-10 一致，说明 8-21 后没有任何东西成功改写 mu-plugin。
- 结论：**V2 从未上线，不存在"被旧文件覆盖"事件；覆盖路径仅是未来风险（默认参数），非历史成因。**

---

## 9. Root Cause Classification

```
A. INTENTIONAL_ROLLBACK        → 排除（无任何人工回退指令/记录）
B. ACCIDENTAL_OVERWRITE        → 排除（V2 从未上传，无从覆盖；生产 hash 与 8-10 部署一致）
C. FALSE_OR_INCOMPLETE_DEPLOYMENT_RECORD → ✅ 成立
D. UNRESOLVED                  → 不适用（证据充分）
```

**根因结论：C。** 0.4.5-E2/F2/G 的"V2_ACTIVE/PASS"来自**本地模拟脚本 + 自生成文档**，无真实生产部署或验证；F1 自己声明未切换；生产自 8-10 起一直是 v1.3.1。8-21 记录的 `e31f837b` 无任何文件对应（可能为报告作者虚构/误记的指纹）。"8-21→8-26 状态冲突"的真相：**不是生产状态变了，而是 8-21 的记录本就与生产无关。**

---

## 10. Recommended Target State

**`KEEP_LEGACY_TEMPORARILY`**（依据）：
1. 生产 Legacy 实测稳定：102 篇文章、5 样本 SEO 输出全部正确、无死链/无泄漏。
2. V2 增量价值（zh、metadata-first）当前无实际消费（无 zh 文章；13 unknown 已安全降级）。
3. V2 从未通过真实生产验证——所有"验证"为本地模拟；045F1 指纹错误 → V2 资产基线不可信。
4. 工作树 142 项 dirty（V2 代码未提交）+ CURRENT_TASK 过期 → 任何 V2 推进前必须先重建基线并落库。
5. 备选（不自动执行）：`REDEPLOY_V2_AFTER_NEW_GATE` —— 仅当用户决定启用 zh/unknown 显式化时，按全新 Gate（真实 FTP 部署+三方哈希+生产 HTTP 验证+canary+观察）执行。

---

## 11. Next Safe Action

1. 本报告交由人工/GPT-5.6 审核（§20 STOP Gate）。
2. 获批后按序：a) 重建完整生产基线（FTP 只读快照全部关键文件 → `BASELINE-20260826`）；b) 提交工作树（0.4.x 代码 + 文档，标注"本地冻结/未上线"）；c) 重写 CURRENT_TASK；d) 更新 feed 基线；e) 对 045E2/F2/G 文档加"本地模拟、未作用于生产"更正注记；f) 若启用 V2 → 全新 Gate。
3. 修复 `run_ftp_deploy_secure.ps1` 默认 RemotePath 陷阱（须人工批准后）。

---

## 终值（§19）

```text
CURRENT_PRODUCTION_RESOLVER = LEGACY
CURRENT_PRODUCTION_CODE = LEGACY (v1.3.1)
CURRENT_PRODUCTION_RUNTIME = LEGACY_ACTIVE（V2 代码不存在；wp-config/env flag 未读取验证 → UNVERIFIED，但无 V2 代码则 flag 无效）
CURRENT_SEO_PAIR_OUTPUT = CORRECT（按 Legacy 契约；V2 配对能力从未存在，无回归）

PRODUCTION_MU_PLUGIN_VERSION = v1.3.1
PRODUCTION_MU_PLUGIN_SHA256 = 8aa9aa8af1da6d84dc0362c8497e2804d66202fb0368702886b22c3bdb3ed54d

LOCAL_MU_PLUGIN_VERSION = v1.4.0（含 Resolver V2 flag，默认 false）
LOCAL_MU_PLUGIN_SHA256 = 7c042a451544…

HISTORICAL_V2_SHA256 = UNVERIFIED（报告所称 e31f837b 无文件对应 → HISTORICAL_BINARY_NOT_FOUND）

V2_WAS_EVER_CONFIRMED_IN_PRODUCTION = NO
ROLLBACK_OR_OVERWRITE_CAUSE = NONE（V2 从未上传；生产自 8-10 部署后未变）
ROOT_CAUSE_CLASSIFICATION = C. FALSE_OR_INCOMPLETE_DEPLOYMENT_RECORD

CURRENT_TASK_STALE = YES
FEED_BASELINE_STALE = YES（96→102 published，基线文件为 8-20 快照）
TRANSLATION_PAIRS_PLUGIN_DEPLOYED = NO（FTP 550 实测）

RECOMMENDED_TARGET_STATE = KEEP_LEGACY_TEMPORARILY（备选：REDEPLOY_V2_AFTER_NEW_GATE，不自动执行）
RECOMMENDED_NEXT_ACTION = 人工审核本报告 → 重建生产基线 → 提交工作树（标注未上线）→ 重写 CURRENT_TASK → 若启用 V2 走全新真实 Gate

PRODUCTION_WRITE_ALLOWED = NO
0.4.X_CHANGE_ALLOWED = NO
GIT_COMMIT_ALLOWED = NO
NEXT_ACTION_REQUIRES_APPROVAL = YES
```

---
`FORENSICS_COMPLETE` — 本任务零生产写入、零 git 变更。等待审核。