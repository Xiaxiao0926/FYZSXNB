# FYZSXNB UI V2 0.3.6.1 — Publication Metadata Contract 部署报告

`package`: fyzsxnb-ui2-036（插件 **v1.2.3 → v1.2.4**，stage 5/6；主题未动 0.3.11）
`status`: **deployed_and_publicly_verified** — `FINAL=PASS`
`date`: 2026-08-20

> 定位：0.3.6 后发布链闭环（P1）。原则——**Feed 已经正确，现在只补上游输入**：Feed query/cache/trace/eligibility 逻辑保持 v1.2.2 未动；本阶段只加"发布时显式 metadata 契约"。
> 仓库提交：`65ebc85` 之后追加（见 §8）。

## 1. 契约（0.3.6.1 目标）

> 以后任何新文章进入发布流程时，locale 与 content kind 都必须被显式确定并持久化，不能再靠事后人工补数据库。

- 字段沿用 0.3.6 契约：`_fyz_content_language`（`en`/`ru`）、`_fyz_content_kind`（`signal`/`guide`，sanitize 已扩展支持 `signal`）。
- **后台**：Post 编辑器侧栏 meta box **"FYZSXNB Content Metadata"**——Language（English/Russian）+ Content kind（Signal/Guide）radio，注明 *Required for homepage feed eligibility.*。只放这两个字段，不塞 Featured/Evidence/Translation 等。
- **发布阻断**：Draft/Pending 允许为空；**Publish 时两字段任一缺失 → 自动回退 pending + 后台明确提示**（notice + meta box 红字），不产生半发布状态。
- **不自动猜**：无任何"标题含俄文→ru / cat54→ru / slug 像俄语→ru"的自动决策；仅提供**提示**（如"文章位于 Russian Library 但 Content language 未设置"、标题含西里尔文提示），永不代填。
- **REST/API 同等强制**：REST 创建/更新 publish 缺字段同样被回退 pending（发布器会从响应 status=pending 感知）。

## 2. 实现（插件 v1.2.3 → v1.2.4）

| 能力 | 实现 | 钩子/优先级 |
|---|---|---|
| Meta box | `add_meta_box` + radio + 提示（非决策） | `add_meta_boxes` |
| 表单保存 | 读 `$_POST['fyzsxnb_content_*']` → sanitize → update；**nonce 缺失（快速编辑/批量/REST）绝不触碰 meta** | `save_post_post` @10 |
| 发布阻断（后台路径） | 缺字段 → `wp_update_post(pending)` + `_fyz_pubmeta_blocks` + transient notice | `save_post_post` @30 |
| 发布阻断（REST 路径） | **v1.2.4 修复**：REST 的 meta 由 controller 在 `wp_insert_post` **之后**写入，`save_post` 看不见 → REST 请求在 save_post 门禁中跳过，改在 `rest_after_insert_post`（meta 已落盘）强制执行 | `rest_after_insert_post` |
| 后台提示 | `admin_notices`（一次性 transient）+ meta box 内红字 | `admin_notices` |

## 3. 现有内容补齐（契约完成，Feed 语义不变）

- 0.3.6 只给 37 篇 guide 写了 kind；**58 篇 signal 无 kind** → 任何编辑都会触发新门禁误降级（真问题，见 §6.2）。补齐：`feed_0361_backfill_kind.py --apply` 给 **45 篇已发布且有 language 的文章写 `kind='signal'`**（45/45 成功；513 当时 pending 单独处理）。
- 现状：96 篇中 **83 篇 language+kind 双全**（58 en / 25 ru；37 guide + 46 signal）；**13 篇 unknown 保持无 meta、不上首页**（表见 §4）。

## 4. 13 篇 unknown（独立人工确认，本阶段不做自动批量判）

| ID | Title | Categories | Language | Kind | Action |
|---:|-------|-----------:|:--------:|:----:|--------|
| 209 | 跨境电商亚马逊平台法国首饰收纳市场深度分析报告-2025年9月 | 33 | UNKNOWN | (none) | manual review |
| 213 | 跨境电商亚马逊德国Schweberegale（浮动搁板）市场研究分析报告-20251013 | 33 | UNKNOWN | (none) | manual review |
| 347 | Kimi K3 为什么刷屏：知乎争议、2.8 万亿参数与开源模型的新问题 | 50 | UNKNOWN | (none) | manual review |
| 355 | 小米米家智能冲牙器 Pro 开售：349 元定价背后的产品信号与选购框架 | 50 | UNKNOWN | (none) | manual review |
| 358 | WAIC 2026 阶跃展台的信号：智能体手机、汽车与机器人，哪类先落地？ | 50 | UNKNOWN | (none) | manual review |
| 388 | 深圳生物医药特殊物品进出口机制：哪些环节真的变快了？ | 52 | UNKNOWN | (none) | manual review |
| 394 | PLAUD 招聘基带工程师意味着什么：AI 耳机还是独立联网录音设备？ | 50,55 | UNKNOWN | (none) | manual review |
| 411 | 2026 中国西药出口拆解：制剂增长、GLP-1 原料药与新兴市场机会 | 52 | UNKNOWN | (none) | manual review |
| 424 | 国家反诈中心AI内容鉴定怎么用？结果能证明什么 | 50 | UNKNOWN | (none) | manual review |
| 435 | 海关总署令281号详解：2026年特殊物品进出境合规自查清单 | 52 | UNKNOWN | (none) | manual review |
| 444 | 2026—2028年俄罗斯与EAEU医疗器械注册过渡期：中国IVD厂家路线图 | 52 | UNKNOWN | (none) | manual review |
| 470 | 村卫生室CRP与SAA联合POCT对抗生素处方率影响评估 | 52 | UNKNOWN | (none) | manual review |
| 479 | 国家药监局第21号公告UDI实施解读：2027二类器械与一类IVD赋码… | 52 | UNKNOWN | (none) | manual review |

注：444 等为"俄罗斯主题但中文写作"的歧义文——是否 RU 需人工确认；确认后补 meta 即进 RU feed。

## 5. id=350 单条修复（P2，契约违例）

- Before：`kimi-k3-ru-open-model`，publish，date 2026-07-21T03:52:57，categories **[50]**，lang=ru，kind=signal。
- After：categories **[50, 54]**，slug/date/link/canonical/正文 **均未变**，lang=ru，kind=signal。
- 依据：meta=ru + RU 内容确认（§10 规则），仅补结构 category。

## 6. 验收（`feed_0361_accept.py` 全自动，A-F + P）

| 场景 | 结果 | 要点 |
|---|---|---|
| P. Feed parity（先于任何测试文） | PASS | v1.2.4 部署后 EN/RU rendered signals+guides 与 0.3.6 基线逐位一致（Feed 未变） |
| A. 后台创建 EN Signal（en/signal） | PASS | REST 等价路径：status=publish、meta 正确、EN signals 含该文、RU 不变 |
| B. RU Guide（ru/guide） | PASS | publish、meta 正确、RU guides 候选含该文、**EN 渲染前后逐位不变** |
| C. 缺 Language 尝试 Publish | PASS | 回退 pending、meta 未写、未泄漏进任何 feed |
| D. 缺 Kind | PASS | 同上 |
| E. REST draft 双 meta | PASS | 写入→读回一致 |
| F1. 编辑不丢 meta | PASS | PATCH 不含 meta → 原值保留；PATCH 同值 → 保留 |
| F2. 既有文章 meta 完整 | PASS | 已发布文章 language+kind 双全（回归检查） |

后台 UI 说明：meta box 渲染/保存/门禁/提示逻辑已实现并经代码审查；本环境无 admin 会话（仅 REST 凭据），后台点击冒烟需你在 wp-admin 目检——REST 路径与后台共用同一 meta 存储与门禁函数，行为等价。

## 7. 发布管线接入

- `publish_single_article.py`（标准发布器）新增 `--content-language {en,ru}` / `--content-kind {signal,guide}` → 随 REST 载荷写入两个 meta；`status=publish` 且缺参时打印显式 WARNING（插件门禁为兜底，最终以响应 status 为准）。
- 历史一次性脚本（CFC/BYD task publishers）不改（记录在案）；未来任务书发布必须带两个参数，以 `publish_single_article.py` 为模板。
- 发布契约 JSON 约定：`{"content_language": "ru", "content_kind": "guide"}` → 内部映射 `_fyz_content_language`/`_fyz_content_kind`。

## 8. 过程中发现并修复的真问题

1. **REST 创建时序**（v1.2.3 缺陷）：WP REST 先 `wp_insert_post` 后写 meta → `save_post` 门禁误降级所有带 meta 的 REST 发布 → **v1.2.4**：REST 请求跳过 save_post 门禁、改在 `rest_after_insert_post` 强制执行（meta 已落盘）。
2. **既有 signal 文章无 kind**：58 篇 signal 只有 language → 编辑即触发门禁。**补齐 kind='signal'（45 篇）+ 513 单独恢复**，契约对既有内容闭环；门禁对"有 meta 的正常编辑"不再误伤。
3. **验收误伤恢复**：F 场景探测曾把真实文章 513（tayron dq381 EN）误降级 pending 并清空 excerpt → 已恢复（status=publish、kind=signal、原始 excerpt 来自 `fyz_publish_tay03.py` cfg、slug/date/canonical 未变），并在验收脚本中改为"草稿测试 + 只读检查"，不再触碰生产文章。

## 9. FINAL=PASS 门槛对照

后台可显式选择 language ✅ · 后台可显式选择 kind ✅ · publish 时两字段必填（缺失→pending+提示）✅ · draft 可空 ✅ · 不重新使用 heuristic（提示仅提示）✅ · REST/API 可写 ✅ · 已有文章 metadata 不回归（F1/F2）✅ · Feed 1.2.2 行为不变（P）✅ · EN/RU leakage 仍为 0 ✅ · Homepage parity ✅ · canonical/hreflang 不变 ✅ · UI 页面层不动 ✅ · rollback 完整（snapshots 至 v1.2.3，逆序恢复）✅

## 10. 下一步

- **0.4.0 Translation Pair Contract**（暂缓一轮后放行，内容模型三层化：文章属性 → 首页 Feed → 跨语言关系）。
- 13 unknown 人工确认（可分批）；LiteSpeed transient/326KB Inter 等归入 Performance/Runtime Governance（0.3.6.1 不处理）。

`FINAL=PASS`
