# FYZSXNB Resolver V2 — Production Monitoring & Observability Checklist

**Document ID:** `FYZ-DOC-20260820-MONITORING-045D`  
**Stage:** `0.4.5-D`  
**Author:** Google Gemini Flash 3.7  
**Scope:** Telemetry, SEO Verification, and Health Inspection Protocol  

---

## 1. 三维可观测性监控矩阵 (Monitoring Matrix)

| 监控维度 | 监测目标 | 检查方法 / 工具 | 合格基准 (SLA) | 告警级别 |
|:---|:---|:---|:---|:---:|
| **技术层** | PHP 运行时错误 | `debug.log` / Nginx error.log | 增量 **0 Warning, 0 Error, 0 Notice** | **P0** |
| **技术层** | 数据库查询开销 | Query Monitor / Debug Bar | 单次请求新增 SQL 查询数 = **0** | **P1** |
| **技术层** | 缓存命中率 | LiteSpeed Cache Status Header | 命中率稳定 $\ge 90\%$，无缓存雪崩 | **P2** |
| **SEO 层** | HTML Lang 标签 | `curl -s` 提取 `<html>` 属性 | EN=`en-US`, RU=`ru-RU`, ZH=`zh-CN` | **P0** |
| **SEO 层** | OpenGraph Locale | 检查 `<meta property="og:locale">` | EN=`en_US`, RU=`ru_RU`, ZH=`zh_CN` | **P1** |
| **SEO 层** | 结构化数据校验 | Google Rich Results Test API | 核心节点 `inLanguage` 100% 格式合法且通过 | **P1** |
| **SEO 层** | 搜索引擎收录 | Google Search Console 国际化报告 | 0 语言信号冲突，0 结构化数据错误 | **P1** |
| **内容层** | 首页 Feed 完整度 | 自动化比对 Page 11 与 Page 400 | 文章数量与排序 100% 吻合基线 | **P0** |
| **内容层** | 语种物理隔离 | 扫描 EN/RU 首页卡片 locale | 目标语种纯度 **100%**，0 跨语种泄漏 | **P0** |
| **内容层** | Unknown 隔离态 | 检查 13 篇存量未打标文章 | 绝不出现在任何公开首页 Feed | **P0** |

---

## 2. 灰度与全量期间周期性巡检频次 (Inspection Cadence)

```text
[上线后 0 ~ 1 小时 (高频监控期)]
  └─► 每 15 分钟运行一次 30 篇重点样本公网探针扫描
  └─► 实时观察 LiteSpeed 错误日志与访问日志

[上线后 1 ~ 24 小时 (日间观察期)]
  └─► 每 2 小时执行一次全站 96 篇全量只读元数据与 HTML 对拍
  └─► 抽检 GSC 抓取状态与 Googlebot 响应日志

[上线后 1 ~ 7 天 (稳定观察期)]
  └─► 每日一次 SEO 健康巡检
  └─► 统计 Category 54 Legacy 兜底命中次数 (目标收敛至 0)
```
