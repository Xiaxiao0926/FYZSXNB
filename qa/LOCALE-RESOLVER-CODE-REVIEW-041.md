# FYZSXNB 0.4.1 — Locale Resolver Code Review

**Task ID:** `FYZ-20260820-LOCALE-DETECTOR-041`  
**File Modified:** `work/fyzsxnb-ui-v2/mu-plugins/fyzsxnb-p0-seo-patch.php`  
**Target Version:** `1.4.0` (from `1.3.1`)  
**Status:** `CODE_REVIEW_PASS`  

---

## 1. 代码改动清单 (Functions Added & Modified)

### 1.1 Functions Added
1. **`fyzsxnb_resolve_content_locale( $post_id )`** (Lines 57–134):
   - **职责**：针对单个文章对象的 Central Content Locale 解析器。
   - **逻辑**：
     * Step 1: 优先读取 `_fyz_content_language`。
     * Step 2: 严格执行 Category 54 结构契约（`ru` 必带 54；`en` 严禁 54）。
     * Step 3: 若 metadata 缺失/未知，优雅降级至 legacy fallback（`fyzsxnb_get_russian_post_ids()` + Cat 54）。
     * Step 4: 默认返回 `en`（`source: default`）。
2. **`fyzsxnb_get_locale_trace( $post_id )`** (Lines 764–783):
   - **职责**：内部只读诊断辅助函数，输出文章的元数据、结构、Legacy 状态及 Resolver 解析明细。

### 1.2 Functions Modified
1. **`FYZSXNB_P0_SEO_VERSION`** (Line 22):
   - 从 `1.3.1` 递增至 `1.4.0`。
2. **`fyzsxnb_is_russian_target( $target_id = null )`** (Lines 144–178):
   - **重构逻辑**：
     * 文章上下文（`'post' === get_post_type()`）：委托给 `fyzsxnb_resolve_content_locale()` 判定。
     * 非文章/页面上下文（如 Page 400 `/ru/`）：保留原有显式 ID 集合与 Cat 54 请求检测。
3. **`fyzsxnb_get_russian_post_ids()`** (Lines 37–39):
   - **完全保留** 15 个对象硬编码数组（Page 400 + 14 篇 RU 文章），作为 Fallback 事实源，绝不删除。

---

## 2. 消费者与 Hook 影响范围 (Hooks & Consumers Affected)

| Hook / 消费者 | 改动前数据流 | 改动后数据流 | 公开输出行为变化 |
|:---|:---|:---|:---:|
| `language_attributes` | `fyzsxnb_is_russian_target()` (Legacy IDs + Cat54) | `fyzsxnb_is_russian_target()` $\rightarrow$ Content Resolver (Meta primary, Legacy fallback) | **100% Parity (无变化)** |
| `aioseo_facebook_tags` | `fyzsxnb_is_russian_target()` (Legacy IDs + Cat54) | `fyzsxnb_is_russian_target()` $\rightarrow$ Content Resolver (Meta primary, Legacy fallback) | **100% Parity (无变化)** |
| `aioseo_schema_output` | `fyzsxnb_is_russian_target()` (Legacy IDs + Cat54) | `fyzsxnb_is_russian_target()` $\rightarrow$ Content Resolver (Meta primary, Legacy fallback) | **100% Parity (无变化)** |

---

## 3. 零回退与非目标域边界证明 (Zero Regression Proof)

| 核心策略 / 模块 | 是否发生任何修改 | 证明与事实 |
|:---|:---:|:---|
| **Canonical Policy** | **NO** | `fyzsxnb-p0-seo-patch.php` 未触碰任何 canonical filter，AIOSEO 默认 canonical 机制保持 100% 原状。 |
| **Hreflang Policy** | **NO** | `fyzsxnb_render_home_hreflang`（Page 11 ↔ Page 400）未作任何字符改动；文章级 hreflang 保持留白。 |
| **Robots / Noindex** | **NO** | 未添加或修改任何 robots filter。 |
| **Sitemap** | **NO** | 未触碰 sitemap 输出。 |
| **Cars from China (CFC)** | **NO** | 未触碰 CFC 路由、SEO 或展示逻辑。 |
| **Page 400 Description Special-case** | **NO** | `fyzsxnb_filter_aioseo_description` 保持 Page 400 硬编码定制描述不变。 |
| **Homepage Pair (11 ↔ 400)** | **NO** | 首页配对与 Switcher 行为 100% 保持现状。 |
| **Theme 0.3.11** | **NO** | 主题层完全冻结，未触碰任何 theme 文件。 |
| **Feed Plugin 1.2.4** | **NO** | Feed 插件完全冻结，未触碰任何 feed 文件。 |
| **Translation Pair Plugin** | **NO** | Translation Pair 插件保持本地隔离冻结。 |

---

## 4. 真实生产数据验证结论

基于当前生产环境 REST `context=edit` 抓取的 96 篇真实数据（`qa/LOCALE-PRODUCTION-META-SNAPSHOT-041.json`）：
- `ACTUAL_META_RU = 25` 全部包含 Category 54，且 11 篇新 RU 与 14 篇旧 RU 均经实测验证无误。
- `ACTUAL_META_EN = 58` 全部无 Category 54。
- `ACTUAL_META_UNKNOWN = 13` 全部维持 Legacy 兼容行为。
- `STRUCTURAL_CONFLICT = 0`，`PARITY_MISMATCH = 0`。
- 本地 Patch 实现了 100% 字节级输出对齐与零风险演进。
