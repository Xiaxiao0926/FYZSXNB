# Article 002 — Pre-Publish QA Checklist（2026-08-27）

**Baseline:** Editorial Signoff `00f0b3c`（FINAL PASS）→ Visual/Publish-Prep Gate

## HTML QA（FINAL-HTML-001.html）

| Item | Result |
|---|---|
| HTML 语义标签（h1×1 / h2×8 / h3×4 / table / figure） | ✅ |
| 图片 img 3 处 + alt + width/height + loading=lazy | ✅ |
| 无脚本、无外链 JS/CSS、无注入风险 | ✅ |
| 引用编号 [1]–[28] 与 Sources 列表一一对应（无 29/30 引用） | ✅ |
| 边界句保留（model/OEM-specific、not repair instruction、equipment coverage≠procedure coverage、No Chinese-OEM parameter） | ✅ |
| 红线扫描（mm/角度/步骤/road-test/coding/service-menu） | 0 ✅ |
| 字数 | ~2,100（含 sources 2,300 内）✅ |

## 图片 QA（3 Figures + Hero）

| Item | Result |
|---|---|
| Figure 1/2/3 程序化 PNG（1280×800，内容渲染验证 13.5/17.5/35.2% 非白） | ✅ 生成本地 assets/ |
| alt/caption 含 "Illustrative / Potential / Directional" 标注 | ✅ |
| Figure 2 无红 "GAP" 框（amber "Potential constraint" + not-quantified 注） | ✅（按图形脚本） |
| Figure 3 无 vendor endorsement 暗示（注已标） | ✅ |
| Hero（照片级） | **DEFERRED**（本运行时无图像生成；规格卡 HERO-SPEC-001 已备，Publish Gate 前置条件） |

## 链接 QA（Closeout 更新 2026-08-27）

| Item | Result |
|---|---|
| 内部链接 **实际嵌入正文**（WP-CONTENT-FRAGMENT-001.html）：Article001 / Article004 / Case001 / Case003 / Hub / Case002 = 6 contextual anchors | ✅ 6/6 已嵌入（非仅验证可访问性） |
| 嵌入链接目标 HTTP 200 复验（closeout 日实测） | ✅ 6/6 |
| 无跨语言链接（EN⇄EN only） | ✅ |
| 外部来源 URL 28 条全部可点击 `<a href>` + `target="_blank" rel="noopener noreferrer"`（BMW/MINI primary PDF 直链） | ✅（fragment 内 91 处安全属性外链） |

## 发布载荷约束（Closeout 更新）

| Item | Result |
|---|---|
| WP-CONTENT-FRAGMENT-001.html：无 doctype / html / head / title / h1（扫描 5 项全 clean）→ public page 单 H1（theme 标题） | ✅ |
| `{MEDIA}` placeholders | 3（figure 1/2/3）——发布前替换并扫描 `PLACEHOLDER_COUNT=0` |
| S1–S28 引用编号 ↔ Sources 列表对应 | ✅ |
| Hero（照片级） | PENDING（发布唯一前置；HERO-SPEC-001 已 PASS） |

## Meta / Taxonomy QA

| Item | Result |
|---|---|
| SEO payload（JSON 有效） | ✅ |
| slug 提案 `chinese-cars-russia-adas-calibration`（发布时碰撞检查） | 提案 ✅ |
| category ru-auto (id 56) 与既有 EN 汽车文章一致（只读核验） | ✅ |
| `_fyz_content_language=en` / `_fyz_content_kind=guide` | ✅ |

## 结论

```text
HTML_QA = PASS
FIGURE_1_2_3_QA = PASS
HERO_QA = DEFERRED（规格卡就绪，非 blocker 之外的前置项）
LINK_QA = PASS
META_QA = PASS
PRE_PUBLISH_QA_HEADLINE = PASS_WITH_HERO_PENDING
WORDPRESS_PUBLISH = NOT_EXECUTED（本 Gate 禁止）
```