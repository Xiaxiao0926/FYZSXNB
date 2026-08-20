# FYZSXNB 0.4.0 — Translation Pair Source-of-Truth Audit

**Task ID:** `FYZ-20260820-TRANSLATION-PAIR-040`  
**Date:** 2026-08-20  
**Status:** `STAGE_1B_AUDIT_COMPLETE`  
**Finding:** `CONFIRMED_PAIR = 0` | `ARTICLE_TRANSLATION_DATA_MIGRATION = NOT_APPLICABLE`

---

## 1. 审计概述与方法

根据 `FYZSXNB 0.4.0 — SCOPE CORRECTION 01` 及 `0.4.0-A.1 Foundation Hardening` 要求，本审计对全站项目资料进行只读扫描，寻找是否存在权威、经过人工审校的 `EN ↔ RU` 文章翻译配对记录。

扫描范围覆盖：
- `work/agent-handoff/`（含全部历史 `TASK.md`, `acceptance.json`, `PENDING_TASKS.json`）
- `work/site-ops/tasks/`（含全部发布器脚本、验证脚本与 Markdown 源稿）
- `work/fyzsxnb-ui-v2/qa/`（含 `feed_036_inventory_report.json` 等 96 篇发布清单）
- `work/fyzsxnb-ui-v2/mu-plugins/fyzsxnb-p0-seo-patch.php`

---

## 2. 审计发现与证据事实

1. **MU-Plugin 现状**：
   - `fyzsxnb_get_russian_post_ids()` 仅为语言检测器（15 个对象：Page 400 + 14 篇 RU 文章），非配对字典。
   - `fyzsxnb_render_home_hreflang()` 仅配对首页（`/` [ID 11] ↔ `/ru/` [ID 400]）。
   - 源码明确注明：*“Article pages are intentionally not paired here because only reviewed translations may receive hreflang links.”*
2. **发布脚本显式声明（Cluster Only）**：
   - 在 `fyz_publish_tay01.py`、`fyz_publish_tay02.py`、`fyz_publish_tay03.py`、`fyz_publish_byd_openpilot*.py` 等脚本中，作者明确记录：
     > *“Identical research base, deliberately NOT translations of each other.”*
     > *“No EN<->RU cross links (no companion mechanism yet).”*
   - 即：虽然 6 组文章基于相同研究背景分别以 EN/RU 撰写，但历史生产线上明确将其定性为**主题集群独立文章（Cluster Only），并非直译译文**。除非未来人工重新制作并批准真正语义等价的译文，否则严禁代码将其作为 Translation Group 配对。
3. **零元数据关联**：
   - 项目资料中不存在任何已有的 `translation_of`、`translation_group` 或权威审校互译配对清单。

---

## 3. 统计汇总（精确对齐 96 篇全量发布数据）

| 类别 | 计数 (文章篇数) | 统计明细与说明 | Auto-write 授权 |
|:---|:---:|:---|:---:|
| **CONFIRMED_PAIR** | **0 对 (0 篇)** | 存在明确审校翻译记录的配对 | **NO** (无数据) |
| **CLUSTER_ONLY_NOT_TRANSLATION** | **6 对 (12 篇)** | 6 组同主题独立撰写文章，历史明确定性非互译 | **NO** (禁止自动配对) |
| **UNPAIRED** | **71 篇** | 独立单语文章（**52 EN / 19 RU**，合计 71 篇） | **NO** |
| **EXCLUDED_UNKNOWN** | **13 篇** | 13 篇 unknown metadata 文章（完全排除） | **NO** |
| **总计** | **96 篇** | 生产全量 96 篇已发布文章 ($0 + 12 + 71 + 13 = 96$) | **NO** |

---

## 4. 6 组 Cluster-Only 历史文章清单（明确标记为非互译）

以下 6 组在历史发布脚本中明确声明为“共享研究背景但非直译”，严禁分配 `_fyz_translation_group`：

| EN ID | RU ID | EN Slug / Title | RU Slug / Title | Evidence / 历史定性 | Status | Auto-write Allowed |
| ----: | ----: |:----------------|:----------------|:-------------------|:------:|:------------------:|
| 509 | 510 | `china-market-volkswagen-tayron-330tsi-dkv-dpl-dth-parts` | `volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay` | `fyz_publish_tay01.py`: "deliberately NOT translations" | `CLUSTER_ONLY` | `NO` |
| 511 | 512 | `china-market-volkswagen-tayron-330tsi-gpf-owner-cases` | `volkswagen-tayron-330tsi-kitay-gpf-opyt-vladeltsev` | `fyz_publish_tay02.py`: "no EN<->RU cross-links" | `CLUSTER_ONLY` | `NO` |
| 513 | 514 | `china-market-volkswagen-tayron-dq381-emergency-mode-owner-cases` | `volkswagen-tayron-kitay-dq381-avariynyy-rezhim-realnye-sluchai` | `fyz_publish_tay03.py`: "no EN<->RU cross-links" | `CLUSTER_ONLY` | `NO` |
| 501 | 502 | `byd-han-dmi-openpilot-can-bus-hardware-guide` | `byd-han-dmi-openpilot-can-shina-oborudovanie` | `fyz_publish_byd_openpilot.py`: "cluster only" | `CLUSTER_ONLY` | `NO` |
| 503 | 504 | `byd-frigate-07-openpilot-adaptation-data-requirements` | `byd-frigate-07-openpilot-dannye-dlya-adaptacii` | `fyz_publish_byd_openpilot_2.py`: "cluster only" | `CLUSTER_ONLY` | `NO` |
| 505 | 506 | `byd-song-plus-dmi-openpilot-firmware-guide` | `byd-song-plus-dmi-openpilot-proshivka-rukovodstvo` | `fyz_publish_byd_openpilot_3.py`: "cluster only" | `CLUSTER_ONLY` | `NO` |

---

## 5. 结论

- **数据层迁移状态**：`ARTICLE_TRANSLATION_DATA_MIGRATION = NOT_APPLICABLE` (因为不存在历史文章映射)
- **基础设施定位**：0.4.0-A.1 仅建立安全受控的基础设施（插件、受控 API、Ops 运维工具、补偿回滚、Invariant 检验）。
- **零破坏保证**：公开站点的 SEO 输出（Canonical / Hreflang / Switcher / Robots / Sitemap）保持 100% 零变化。
