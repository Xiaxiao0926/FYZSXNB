# FYZSXNB Resolver V2 — Production Switch & Migration Architecture

**Document ID:** `FYZ-DOC-20260820-SWITCH-DESIGN-045C2`  
**Stage:** `0.4.5-C2`  
**Author:** Google Gemini Flash 3.7  
**Scope:** Production Switch Architecture, Consumer Inventory, and Migration Roadmap  

---

## 1. 现状与准备度评估 (Readiness Assessment)

在 0.4.5-C1 阶段，我们成功完成了 Resolver V2 影子比对审计（Shadow Audit）：
- **83 篇已声明元数据文章（58 EN + 25 RU）**：新旧解析器输出 **100% 逐位匹配（83/83 MATCH）**；
- **13 篇存量 Unknown 文章**：新解析器由盲目默认 'en' 提升为精确 'unknown' 隔离，风险定级全为 **LOW RISK（有益精度提升）**；
- **SEO 影响**：影子阶段 `SEO_TOUCH_COUNT = 0`，零线上冲击；
- **现网 Category 54 冲突**：实测 **0 冲突**（25 RU 全有 Cat 54，58 EN / 13 Unknown 全无 Cat 54）。

> **结论**：Resolver V2 技术可行性、数据完备度与向后兼容性已完全具备上线条件。

---

## 2. MU-Plugin 全消费端审计清单 (Consumer Inventory)

| # | Consumer / Hook | 所在文件 | 目标函数 | 当前解析器 | V2 迁移方案 | 风险评级 |
|---|:---|:---|:---|:---|:---|:---:|
| **1** | `<html>` `language_attributes` | `mu-plugins/fyzsxnb-p0-seo-patch.php` | `fyzsxnb_filter_language_attributes` | `fyzsxnb_is_russian_target()` (Legacy IDs + Cat54) | 切换为 `fyzsxnb_resolve_content_locale()`: `zh` $\to$ `zh-CN`, `ru` $\to$ `ru-RU`, `en` $\to$ `en-US`, `unknown` $\to$ `en-US` (安全兜底) | **LOW** |
| **2** | OpenGraph `aioseo_facebook_tags` | `mu-plugins/fyzsxnb-p0-seo-patch.php` | `fyzsxnb_filter_facebook_tags` | `fyzsxnb_is_russian_target()` (Legacy IDs + Cat54) | 切换为 `fyzsxnb_resolve_content_locale()`: `zh` $\to$ `zh_CN`, `ru` $\to$ `ru_RU`, `en`/`unk` $\to$ 默认 | **LOW** |
| **3** | Schema `aioseo_schema_output` | `mu-plugins/fyzsxnb-p0-seo-patch.php` | `fyzsxnb_filter_schema_output` | `fyzsxnb_is_russian_target()` (Legacy IDs + Cat54) | 切换为 `fyzsxnb_resolve_content_locale()`: `zh` $\to$ `zh-CN`, `ru` $\to$ `ru-RU`, `en`/`unk` $\to$ 保持 Core 默认 | **LOW** |
| **4** | 首页 Hreflang (`neve_head_start`) | `mu-plugins/fyzsxnb-p0-seo-patch.php` | `fyzsxnb_render_home_hreflang` | 显式 Page 11 与 Page 400 ID | **保持完全原状（FROZEN）**，未建独立 `/zh/` 首页前绝不修改 | **ZERO** |
| **5** | Page 400 描述定制 (`aioseo_description`) | `mu-plugins/fyzsxnb-p0-seo-patch.php` | `fyzsxnb_filter_aioseo_description` | 显式 Page 400 ID 检测 | **保持完全原状（FROZEN）**，不受文章 Locale 解析器影响 | **ZERO** |
| **6** | Blog 首页 H1 (`neve_before_posts_loop`) | `mu-plugins/fyzsxnb-p0-seo-patch.php` | `fyzsxnb_maybe_render_blog_h1` | 显式 Page 18 ID 检测 | **保持完全原状（FROZEN）** | **ZERO** |

---

## 3. 生产环境 Resolver V2 终态架构 (Production Architecture)

```php
/**
 * Production Content Locale Resolver V2.
 *
 * @param int $post_id Post ID.
 * @return array {
 *     @type string $locale     'en'|'ru'|'zh'|'unknown'
 *     @type string $source     'meta'|'legacy'|'none'
 *     @type string $confidence 'high'|'medium'|'low'
 *     @type bool   $valid      True if structural rules match
 *     @type bool   $conflict   True if metadata contradicts category
 *     @type string $reason     Traceable explanation code
 * }
 */
function fyzsxnb_resolve_content_locale( $post_id )
```

### 降级优先级 (Fallback Hierarchy)
1. **Priority 1 (唯一事实源)**：`_fyz_content_language`（规范化 `en` / `ru` / `zh`）；
2. **Priority 2 (历史防御兜底)**：无元数据时，若在旧 RU ID 表或包含 Category 54 $\to$ `ru`（`source: legacy`）；
3. **Priority 3 (未定义隔离)**：无元数据且无 Legacy 信号 $\to$ `unknown`（`source: none`，公开 HTML 优雅降级为英文默认，避免报错）。

---

## 4. 迁移策略比选 (Migration Strategy Comparison)

| 评估维度 | 方案 A: 一刀切直接替换 (Big Bang) | 方案 B: Feature Flag 软切换 + 灰度 (推荐) | 方案 C: 永久双解析器共存 |
|:---|:---|:---|:---|
| **实施动作** | 直接修改生产代码覆盖旧函数 | 引入 `FYZ_USE_RESOLVER_V2` 开关，本地/Canary 验证后切主 | 长期维护两套逻辑 |
| **回滚能力** | 差（必须重新 FTP 覆盖大文件） | **秒级（单行 flag false 即可瞬时回退）** | 较差 |
| **故障影响** | 若有边缘 Bug 影响全站 | 零风险（未开 flag 前 100% 走旧代码） | 维护成本极高 |
| **推荐评级** | ★★☆☆☆ (禁止) | **★★★★★ (强烈推荐采纳方案 B)** | ★☆☆☆☆ (遗留负债) |

---

## 5. 灰度发布五阶段规程 (5-Stage Deployment Sequence)

```text
[Stage 0: 影子比对 (Shadow Audit)] ──► (已完成: 83/83 Match, 0 High/Med Risk)
           │
           ▼
[Stage 1: 本地 Feature Flag 编码与 Lint]
  - 在 mu-plugins/fyzsxnb-p0-seo-patch.php 中植入 FYZ_USE_RESOLVER_V2 开关
  - 本地运行 PHP Lint 与单元测试 (100% PASS)
           │
           ▼
[Stage 2: 离线 30 篇重点样本回归测试]
  - 运行 10 EN + 10 RU + 5 Unknown + 5 Synthetic ZH 重点比对
           │
           ▼
[Stage 3: 生产 Canary 预部署 (Flag=false)]
  - 备份生产快照 -> FTP 部署带开关的 MU-plugin (默认 FYZ_USE_RESOLVER_V2 = false)
  - 通过内部参数/管理员会话验证 V2 解析
           │
           ▼
[Stage 4: 正式全量切流 (Flag=true)]
  - 开启 FYZ_USE_RESOLVER_V2 = true
  - 只读扫描 96 篇公网 HTML lang / OG / Schema，确认 0 回退
```
