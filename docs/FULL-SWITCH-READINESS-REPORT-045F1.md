# FYZSXNB 0.4.5-F1 — Resolver V2 Production Full Switch Readiness Report

**Document ID:** `FYZ-DOC-20260821-FULL-SWITCH-PREP-045F1`  
**Stage:** `0.4.5-F1` (PRODUCTION CHANGE PREPARATION ONLY)  
**Status:** `READY_FOR_HUMAN_APPROVAL`  
**Production Write:** `NO`  
**Actual Switch:** `NO` (Feature Flag Remains `false`)  

---

## 1. 生产环境指纹与前置快照 (Pre-Switch Snapshot)

| 资产组件 | 版本 | SHA256 指纹 / 状态 | 备注 |
|:---|:---:|:---|:---|
| **MU-Plugin** | `v1.4.0` | `e31f837b39c8d4989e809a22bc82143d4f85244652f15225bc36f0eb28c8f0fe` | 已植入 V2 分发器与门禁 |
| **Feed Plugin** | `v1.2.5` | `d572e192fc704e2890fb39e41123a18f37fb456f0cd20227d47b4fb50ab354d0` (Live v1.2.4) | 数据层支持 zh 规范清洗 |
| **Theme** | `v0.3.11` | `Neve Child (FYZSXNB Edition)` | **FROZEN (100% 保持原状)** |
| **全站文章资产** | `96 篇` | 58 EN / 25 RU / 13 Unknown | 快照已封存 `LOCALE-PRODUCTION-META-SNAPSHOT-041.json` |
| **Git 状态** | `main` | `9feb0ab` (0.4.1) / `3355f3c` (0.4.0) | 工作区干净，无未追踪脏代码 |
| **时间戳** | - | `2026-08-21T08:55:00+08:00` | 切流前指纹校验通过 |

---

## 2. 特性开关切换与单行回滚指令 (Switch & Rollback)

### 2.1 全量切流指令 (To Enable V2)
在生产环境 `wp-config.php` 顶部（或环境变量）声明：
```php
define( 'FYZ_USE_RESOLVER_V2', true );
```
*(或环境变量: `export FYZ_USE_RESOLVER_V2=1`)*

### 2.2 单行秒级回滚指令 (One-Line Instant Rollback)
一旦出现任何异常，立即执行单行回退：
```php
define( 'FYZ_USE_RESOLVER_V2', false );
```
*(或环境变量: `export FYZ_USE_RESOLVER_V2=0`)*
> **SLA**：无需重新上传大文件，100% 恢复 Legacy Resolver 执行路径（耗时 $< 1$ 分钟）。

---

## 3. 消费端生效与不变式核查 (Consumer Review)

```text
[切换至 V2 后立即生效的消费端]:
  ├── 1. <html> language_attributes  ──► zh 输出 lang="zh-CN", ru 输出 lang="ru-RU", en 输出 lang="en-US"
  ├── 2. OpenGraph og:locale         ──► zh 输出 zh_CN, ru 输出 ru_RU, en 输出 en_US
  └── 3. Schema.org inLanguage       ──► zh 输出 "zh-CN", ru 输出 "ru-RU", en 输出 "en-US"

[100% 保持严格不变的锚定资产 (0 Drift)]:
  ├── 4. Canonical URLs              ──► 30/30 样本 100% 保持自指向原链接 (0 漂移)
  ├── 5. Hreflang Tags               ──► 首页 11 ↔ 400 互链原状保持，单篇文章 0 标签 (0 漂移)
  └── 6. Feed 物理隔离               ──► EN/RU 首页候选池与排序 100% 吻合基线，Unknown 0 暴露
```

---

## 4. 精准缓存刷新方案 (Targeted Cache Strategy)

为了避免对 CDN / 全站静态文件产生不必要的冲击，切流后实施**精准目标页面刷新**：
1. **刷新范围 (Target Scope)**：
   * 96 篇已发布文章页面 URL；
   * Page 11（英文首页 `/`）、Page 400（俄文首页 `/ru/`）、Page 18（博客列表 `/blog/`）。
2. **排除范围**：
   * 静态图片资源（`wp-content/uploads/`）、CSS、JS、字体文件等无需刷新。
3. **校验方法**：
   * 通过 `curl -I https://fyzsxnb.com/{slug}/` 观察 `x-litespeed-cache: miss` $\to$ `hit`，并校验 HTML 首行 `lang` 属性。

---

## 5. 三级监控观察窗口 (3-Tier Monitoring Windows)

```text
+----------------------------------------------------------------------------------------------------------------+
|                                         三级监控观察期与告警阈值                                                 |
+--------------+-------------------+---------------------------------------+-------------------------------------+
| 观察期级别   | 时间跨度          | 监控核心目标                          | 关键操作与巡检工具                  |
+--------------+-------------------+---------------------------------------+-------------------------------------+
| **Tier 1**   | 切流后 0 ~ 30 min | 运行时健康度、PHP 错误、公网标签呈现  | 观察 debug.log，运行 30 样本探针    |
+--------------+-------------------+---------------------------------------+-------------------------------------+
| **Tier 2**   | 切流后 30m ~ 24h  | 搜索引擎爬虫抓取状态、全站 96 篇对拍  | 校验 LiteSpeed 状态，运行全量对拍   |
+--------------+-------------------+---------------------------------------+-------------------------------------+
| **Tier 3**   | 切流后 1 ~ 7 天   | Google Search Console 国际化与结构化  | 监控 GSC 语言报告，统计 Legacy 调用 |
+--------------+-------------------+---------------------------------------+-------------------------------------+
```

---

## 6. 硬性回滚触发条件 (Immediate Rollback Triggers)

出现以下任一情况，立即执行第 2.2 节回滚指令：
1. 公网 HTML `lang` 属性丢失或格式损坏；
2. EN 或 RU 首页 Feed 发生跨语种文章泄漏；
3. Google Rich Results Test 报 Schema 结构化数据解析失败；
4. Canonical URL 偏离原生永久链接；
5. PHP 错误日志出现增量 Fatal 或 Warning。

---

## 7. 审批与门禁判定

```text
READY_TO_SWITCH:
YES (全套切流资产、回滚指令、监控矩阵与精准缓存方案已完备)

ROLLBACK_READY:
YES (单行配置/环境变量秒级回滚，SLA < 1min)

MONITORING_READY:
YES (T+30m / T+24h / T+7d 三级监控检查表已就绪)

ACTUAL_SWITCH:
NO (生产环境未做任何开关修改，当前仍为 Legacy Resolver)

STOP
```
