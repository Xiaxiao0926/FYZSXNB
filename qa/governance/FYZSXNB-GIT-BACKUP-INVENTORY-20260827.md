# FYZSXNB Git Backup Inventory — 2026-08-27

**Policy:** ALL_RECOVERABLE_PROJECT_PROGRESS_MUST_BE_COMMITTED_AND_PUSHED_TO_REMOTE
（本地文件不得作为可恢复成果的唯一副本）

## MAIN BACKED UP（生产事实 + 治理状态 + 正式内容资产）

| 区块 | 覆盖 | Git 锚点 |
|---|---|---|
| Production code baseline | theme/plugin/mu-plugin（生产字节级） | `a4c87bc`（tag `prod-baseline-20260826`） |
| Governance state | CURRENT_TASK / 7 份 Gate 报告 / staging plan / 状态首页 | `2baedec`（tag `takeover-governance-20260827`） |
| Feed baseline | FEED-BASELINE-20260826-R1 核心 7 文件 | `2baedec` qa/governance/feed-baseline/ |
| Deployment hardening | 契约 / 硬化脚本 / tombstones / 测试 / QA 证据 | `2baedec` qa/governance/deployment/ |
| D1-KEEP 内容资产 | CARS/AUTOMOTIVE 研究/草稿/发布报告、视觉/图片报告、Hub 文档（~82 项 docs/qa） | closeout commit（本任务） |

## DEV BRANCH BACKED UP（未部署开发，不污染 main）

| 分支 | 内容 | 锚点 |
|---|---|---|
| `dev/resolver-v2` | mu-plugin v1.4.0（Resolver V2）+ 25 份 V2 设计/报告文档 + 21 个 V2 QA 脚本 + fixtures + shadow 数据 60 + reports 9（121 文件） | `87de9ee` |
| `dev/translation-pairs` | translation-pairs 0.4.0 隔离声明（代码已在 main 历史 `3355f3c`；生产 ABSENT） | `87ae7ca` |

## INTENTIONALLY NOT BACKED UP（可重建/无长期恢复价值）

- feed raw/ 抓取与 HTTP 原文（baseline JSON 已保存机器事实）
- tmp 备份（deployment-hardening-pre-*、prod-snapshot-*、forensics 快照）
- 一次性 probe/捕获脚本（forensics_001_*）、截图（qa/screenshots/）
- mock fixtures 运行时产物（work/qa/... mock-remote 等）

## REMOTE 状态

```text
GIT_REMOTE_AVAILABLE = NO（本机未配置 remote）
REMOTE_BACKUP_GATE = BLOCKED_PENDING_REMOTE_URL
本地 Git 备份完成；远端同步待配置 remote 后执行（git push + 反向验证）
```

## UNKNOWN = 0（全部可恢复成果已在本机 Git 内分类归档）