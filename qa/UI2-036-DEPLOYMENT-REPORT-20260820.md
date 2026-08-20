# FYZSXNB UI V2 0.3.6 — Feed Hardening 部署报告

`package`: fyzsxnb-ui2-036（插件 1.0.0 → **1.2.2**，四阶段部署；主题无改动，保持 0.3.11）
`status`: **deployed_and_publicly_verified** — `FINAL=PASS`
`date`: 2026-08-20

> 范围定性：0.3.6 为**内容数据层治理**（feeds 插件/数据层），未改动任何 UI/模板/mu-plugin/SEO/Featured 机制。
> 仓库提交：`52895f8`（v1.1.0/v1.2.0 + 工具链）→ `b132e14`（v1.2.2 + QA 证据）。

## 1. 旧逻辑 → 新逻辑

| Dimension | Before (v1.0.0) | After (v1.2.2) |
|---|---|---|
| Locale | 启发式：meta→cat54→西里尔标题→CJK→拉丁标题（PHP `strtolower` 不转西里尔，隐含大小写坑） | **显式**：`_fyz_content_language` meta 为主，cat54 为结构确认；无确认→`''`（不上首页） |
| Guide 类型 | 启发式：标题/slug 正则（EN/RU 两套，含西里尔大小写歧义） | **显式**：`_fyz_content_kind === 'guide'` |
| 数量不足 | 主题路径显示实际数；dormant marker 路径 <minimum 隐藏区块 | 一律显示实际数量（不足自然缩减）；**跨语言 fallback = 0、旧快照补位 = 0** |
| 缓存 | 无（每次首页渲染 WP_Query 80 篇） | transient `fyzsxnb_home_feed_{locale}_{type}_h3`（locale+type+query version，TTL 15min，存候选 ID 列表，调用方自行 exclude+limit） |
| 失效 | save_post/trash/untrash/delete + `litespeed_purge_url`（实测 URL 级不可靠） | 上述 + restore + `set_object_terms`（category）+ `added/updated/deleted_post_meta`（language/kind）；LiteSpeed purge 改 **类 API `\LiteSpeed\Purge::purge_url`**（etag 实测生效） |
| 决策可观测 | 无 | **Feed Decision Trace** + QA 端点（edit_posts 门禁 + no-store，匿名 401） |
| 发布管线 | 不写 meta（新文靠启发式进首页） | 0.3.6 起无 meta 默认不上首页并进审计清单；**发布线需在后续补写 meta**（见 §9） |

## 2. Feed 数量（候选/渲染，迁移后实测）

| Feed | Expected max | 候选数 | 渲染数 | Locale leakage |
|---|--:|--:|--:|--:|
| EN Signals | 4 | 44 | 4 | 0 |
| EN Guides | 6 | 21 | 6 | 0 |
| RU Signals | 4 | 25 | 4 | 0 |
| RU Guides | 6（theme 部件请求 6；你草稿写 5，因 template-parts 冻结本阶段保持 6） | 12 | 6 | 0 |

leakage 验证：feed-trace 逐 ID 断言 `locale==feed locale`（B 场景 + F 场景）。渲染顺序与迁移前逐项一致（precheck `all_match=true`，见 §3）。

## 3. 迁移与 Parity（表 4）

- 盘点：96 篇已发布（RU 25 / EN 58 / unknown 13）。
- **自动确认补齐**：83 篇写 `_fyz_content_language`（58 en + 25 ru）；37 篇写 `_fyz_content_kind='guide'`；**13 篇 unknown（CJK/双语）未写任何 meta** → 不上首页 + 进审计清单。
- backfill 复核：dry-run `needs_write=0`，`already_correct=83`，`unknown_untouched=13`。
- **误分类 = 0**：parity 预检（`feed_036_precheck.py`，字节级镜像 PHP legacy）先复算 → 与现网 EN/RU signals+guides **逐项一致**；backfill 后与 final 部署后复验均 `all_match=true` → **首页 DOM/视觉零变化**。
- 期间捕获真 BUG：PHP `strtolower` 不转西里尔 → `proverka-epts`（"Проверка…"大写 П）在 PHP 里不是 guide、我的 Python `.lower()` 误判为 guide → 修正为 ASCII-only lower 后 parity 成立（该文保持 signal，与现网一致）。
- **契约违例标记（不修改）**：id=350 `kimi-k3-ru-open-model` 为 RU 内容但无 cat54（会进 EN 归档视图）→ P2 数据卫生项，转文章发布线补 54。

## 4. 缓存失效矩阵（表 3）

| 事件 | 触发 | 清空 key | 验证 |
|---|---|---|---|
| publish | `save_post_post` | 该文 locale 的 signals+guides | C：EN signals `cached→false`，新文出现在 EN 首页；RU 内容不变 |
| update | `save_post_post` | 同上 | 同 C/D 路径 |
| delete/trash | `before_delete_post`/`trashed_post` | 同上 | E：删除后 feed-state 无、公开页无、重填后无复活 |
| restore | `untrashed_post` | 同上 | 与 E 对称（钩子注册） |
| category 变化 | `set_object_terms` | `invalidate_for_post`（locale keys；unknown→双 locale） | 钩子注册 + 语义 |
| meta language/kind 变化 | `added/updated/deleted_post_meta` | 双 locale signals+guides（钩子时机无法读旧值，有意保守；仅这两个 key 触发，**不随任意 save_post 全站清空**） | D：改 kind 后 EN guides 更新、无重复 |
| 页面层 | 同批钩子 | LiteSpeed `/`、`/ru/`、3 个 QA REST URL（action + 类 API） | v1.2.1 类 API etag 实测重新渲染 |

## 5. 验收场景（A–F，`feed_036_accept.py` 全自动，含测试文生命周期）

| 场景 | 结果 | 要点 |
|---|---|---|
| A 正常数量 | PASS | EN/RU signals+guides 渲染 == 迁移前基线（顺序一致） |
| B 内容不足 | PASS | 模拟 exclude 至 3 篇 → 恰好 3 篇，全部同 locale，无跨语言/无快照/无空卡 |
| C 发布 EN 文 | PASS | EN signals 更新 + 缓存失效；**RU 内容不变**；公开 EN 首页出现、RU 无 |
| D 改类型 | PASS | kind→guide 后 EN guides 更新、signals 不重复、RU 无 |
| E 删除 | PASS | 删除后 feed-state 与公开页均无，缓存重填后不复活 |
| F 未识别 locale | PASS | 13 篇 unknown 全部 `eligible=false, reason=locale_unknown`，不进任何 feed |
| X 决策追踪 | PASS | guide 样本 `explicit_locale+kind_guide`；signal 样本 `explicit_locale` |

## 6. 生产健康检查

- **5-UA 一致性**：EN/RU × {chrome/mobile/googlebot/plain/bingbot} × {clean, cache-busted} —— 全部一致（`feed_036_multiua.py` `consistent=true`）。
- **Console / 资源 404**：`feed_036_console.cjs`（playwright）5 视图（EN/RU 首页 1440+390、EN 文章）—— console 0、pageerror 0、失败请求 0、asset 404 0、**390px 无横向滚动**。
- **postdeploy 公开验证**：EN/RU 200、唯一 H1、canonical/hreflang/lang 不变、无 legacy wp:html、统一 footer、QA 端点**匿名 401/403**（修复后）。
- 哈希：4 阶段 source==remote==manifest（151ED643… → CD7C00FF… → AE10E733… → ED9AC65D… → BBDEB76B…）。
- 回滚：snapshots/plugin-v{1.0.0,1.1.0,1.2.0,1.2.1} 齐备，按逆序恢复即可。

## 7. 过程中修复（如实记录）

1. **PHP strtolower 西里尔坑**（§3）——parity 预检捕获，复算修正。
2. **验收脚本断言缺陷**（A 未按渲染语义比较、B 期望值漏 limit、C slug 映射/RU 断言）——修正为渲染语义 + RU 内容不变断言。
3. **LiteSpeed URL purge（action-only）不可靠**：/ru/ 页面对象在 `litespeed_purge_url` 后仍命中旧缓存（etag 停滞）。v1.2.1 增加 `\LiteSpeed\Purge::purge_url()` 类 API → etag 重新生成、页面真实重渲染。**0.3.5 遗留"首页外部视图陈旧"问题的根因定位并修复。**
4. **LiteSpeed 缓存了带鉴权的 REST 响应**：匿名 GET /feed-state 曾返回 200（`X-LiteSpeed-Cache: hit`）。v1.2.2 三个 QA 回调加 `nocache_headers()`+`Cache-Control: no-store`+`litespeed_control_set_nocache`，REST URL 纳入 purge 列表 → 匿名 401 恢复。
5. **transient 跨请求可见性**：v1.2.1 后 transient 在 purge→渲染后偶不可见（LSCWP 7.9 异步 purge/object cache 交互，性能层现象）。**失效语义（cached→false 翻转）已实测正确**；可见性列为 0.3.6.1/运行时治理跟进项。

## 8. FINAL=PASS 门槛对照

locale 主逻辑不再依赖 heuristic ✅ · guide 主逻辑不再依赖 heuristic ✅ · EN/RU leakage=0 ✅ · unknown 默认不上首页 ✅ · 内容不足自然缩减 ✅ · 跨语言 fallback=0 ✅ · 旧快照补位=0（本就不存在，代码审计确认）✅ · publish/update/delete/restore 正确失效 ✅ · taxonomy/meta 改变正确失效 ✅ · Featured 行为不变 ✅ · 首页 DOM/视觉无变化（parity 100%）✅ · canonical/hreflang/robots 无变化 ✅ · 5-UA 一致 ✅ · Console/asset 404 无新增 ✅ · Git/生产/manifest hash PASS ✅ · rollback 完整 ✅

## 9. 下一步（0.3.6 之后）

- **文章发布线补写 meta**（P1）：现有 publisher 不写 `_fyz_content_language`/`_fyz_content_kind`，新文章如不补写将默认不上首页（安全默认，但需发布线跟进）。
- **id=350 补 cat54**（P2，文章线）。
- **0.3.6.1 / 运行时治理**：transient 可见性（LSCWP 对象缓存交互）、326KB Inter（`wp-fonts-local`）、LiteSpeed JS delayed 治理。
- **0.4.0 Translation Pair Contract**（独立阶段）。

`FINAL=PASS`
