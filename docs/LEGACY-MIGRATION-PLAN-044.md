# FYZSXNB Legacy RU Detector Transition & Retirement Plan

**Document ID:** `FYZ-DOC-20260820-LEGACY-PLAN-044`  
**Stage:** `0.4.4`  
**Author:** Google Gemini Flash 3.7  
**Scope:** Strategy for Legacy Russian ID Set Transition and Retirement Metrics  

---

## 1. 现状定位与治理原则 (Status & Strategy)

### 1.1 现状事实
- 历史函数 `fyzsxnb_get_russian_post_ids()` 包含 15 个硬编码对象 ID（Page 400 + 14 篇旧俄语文章）。
- 0.4.1 与 0.4.2 实测已证实：**全部 25 篇在役俄语文章均已具备 Category 54 与 `_fyz_content_language = 'ru'`**。
- 这意味着旧硬编码数组已经**不再是生产正确性的单点瓶颈**，但作为防御性兜底（Fallback），在多语言 V2 体系初期必须保留，杜绝冒进删除引起的未预见风险。

### 1.2 治理原则
> **“保留兜底，下沉优先级；数据 100% 覆盖后，依指标退役。”**

---

## 2. 演进与退役四阶段 (Four-Stage Transition)

```text
[阶段 1: 0.4.1/0.4.4 双读降级]
  - 将 Legacy ID 数组下沉为 Fallback。
  - 单文章优先读取 _fyz_content_language。
  - Page 400 维持 Request 检测。
         │
         ▼
[阶段 2: 0.4.5 全站 100% 元数据确权]
  - 10 篇中文资产完成 zh 确权。
  - 全站文章元数据显式覆盖率达到 100% (96/96)。
         │
         ▼
[阶段 3: 零回退观测期 (Zero Fallback Observation)]
  - 开启内部 diagnostic log (fyzsxnb_get_locale_trace)。
  - 连续 14 天监控，确认 fallback 触发次数严格为 0。
         │
         ▼
[阶段 4: 0.5.0 历史硬编码彻底退役 (Legacy Retirement)]
  - 安全精简: 将 fyzsxnb_get_russian_post_ids() 缩减为仅保留 array(400) 作为 /ru/ hub。
  - 14 篇旧文章 ID 彻底从 PHP 源码中删除。
```

---

## 3. 退役硬性指标门禁 (Retirement Readiness Metrics)

只有当以下 4 项硬性指标**全部达成**时，才允许发起旧文章 ID 列表的物理删除：

1. **元数据显式覆盖率指标**：全站 Published 文章的 `_fyz_content_language` 覆盖率达到 **100%**（且无 UNKNOWN）。
2. **结构契约一致性指标**：所有 `ru` 文章 100% 具备 Category 54，所有 `en`/`zh` 文章 100% 无 Category 54（`STRUCTURAL_CONFLICT = 0`）。
3. **零兜底调用指标**：生产环境文章级 Locale 解析中，`source: legacy` 的调用次数为 **0**。
4. **人工审核门禁**：通过人工安全与架构复核放行。
