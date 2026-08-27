# FYZSXNB Automotive Article 002 — Research & Draft Gate Report

**Task:** `FYZSXNB-AUTOMOTIVE-PHASE2-ARTICLE-002-RESEARCH-DRAFT-001`
**Stage:** RESEARCH + DRAFT ONLY（WORDPRESS/IMG/PROD = NO）
**Executor:** DeepSeek | **Date:** 2026-08-27

## Deliverables（8 份 + 本报告，全部 docs/ 下）

1. `FYZSXNB-AUTOMOTIVE-ARTICLE-002-RESEARCH-REPORT.md`
2. `FYZSXNB-AUTOMOTIVE-ARTICLE-002-SOURCE-LEDGER.md`（21 条来源，Tier 1–4 分层）
3. `FYZSXNB-AUTOMOTIVE-ARTICLE-002-CLAIM-LEDGER.md`（14 条 claim，含 proves/not-proves/confidence）
4. `FYZSXNB-AUTOMOTIVE-ARTICLE-002-OUTLINE.md`
5. `FYZSXNB-AUTOMOTIVE-ARTICLE-002-ADAS-CALIBRATION-DRAFT-001.md`（全稿 EN，21 条编号来源）
6. `FYZSXNB-AUTOMOTIVE-ARTICLE-002-VISUAL-PLAN.md`（Hero + 3 figures，全部 Illustrative 标注）
7. `FYZSXNB-AUTOMOTIVE-ARTICLE-002-SEO-DRAFT.md`（无虚构搜索量声明）
8. `FYZSXNB-AUTOMOTIVE-ARTICLE-002-INTERNAL-LINKING-PLAN.md`（6 个已确认为 200 的 EN 内链目标）

## Compliance checklist（用户红线逐条）

- NO universal calibration parameters — ✅ 全文零 mm/距离/角度
- NO fabricated Russian workshop cases — ✅ 案例均带来源（gipix-lite/autoglass/elektroman/drive2）；Hero 明确 Illustrative
- NO invented search volume — ✅ SEO 文档声明
- NO forum anecdote as OEM procedure — ✅ Claim C12/C5 边界 + Source Ledger 排除规则
- NO step-by-step calibration instructions — ✅（明确"非维修教程"）
- NO publish before GPT review — ✅ 未创建/更新/发布任何 WP 内容；无图片上传

## Evidence discipline

- 来源分层 Tier1（OEM 原则：AAA 行为准则/I-CAR/BMW-MINI 政策示例）→ Tier2（Pilkington/OEM流程方设备厂商 Bosch/Hella/Launch/Autel/UDIAG）→ Tier3（Autostat/RBC/автовзгляд/俄服务实例）→ Tier4 未使用（无中文平台依赖，未来可补充）
- 6% 保有量等数字均绑定来源（Autostat）；"覆盖率无官方统计"作为显式不确定性声明

## 汇报点（交 GPT-5.6 重点审核）

1. C4 数据共享法案：俄法律状态细节是否需要在发布前再核（正文已弱化为"辩论进行中"）
2. 6 个内链目标 slug 在发布时应再验一次 200（当前为 8-26/27 实测）
3. SEO 主关键词词序（"Chinese cars Russia ADAS calibration" vs draft 用词）是否按你偏好调整
4. Hero 视觉方向是否接受（须你批准后才进入图片生成阶段）

## STOP（等待 GPT-5.6 审核）

```text
WORDPRESS_CREATE_ALLOWED = NO（未执行）
WORDPRESS_UPDATE_ALLOWED = NO
WORDPRESS_PUBLISH_ALLOWED = NO
IMAGE_UPLOAD_ALLOWED = NO
PRODUCTION_WRITE_ALLOWED = NO
DEPLOY_ALLOWED = NO

NEXT = GPT-5.6 factual/source/overclaim/Russian-market/visual/metadata review
共 9 份文件（含本报告）待 Git backup 硬规则 commit + push（见下述）。
```