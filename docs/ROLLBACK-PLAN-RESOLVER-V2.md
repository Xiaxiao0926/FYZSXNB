# FYZSXNB Resolver V2 — Emergency Rollback Specification

**Document ID:** `FYZ-DOC-20260820-ROLLBACK-045C2`  
**Stage:** `0.4.5-C2`  
**Author:** Google Gemini Flash 3.7  
**Scope:** Instant Killswitch Protocol, Triggers, and Verification Procedures  

---

## 1. 触发回滚的硬性告警指标 (Rollback Triggers)

一旦在灰度或全量上线后观测到以下任一异常，必须**立即触发秒级回滚**：

1. **HTML Lang 属性异常**：公网单页出现 `lang=""`、非法语言标签或格式损坏；
2. **OpenGraph / Schema 结构破坏**：Google 网页结构化数据测试工具（Rich Results Test）报 `inLanguage` 校验失败；
3. **Feed 隔离失效**：非目标语种文章泄漏进入 EN（Page 11）或 RU（Page 400）首页；
4. **Hreflang / Canonical 发生意外漂移**：首页 11 ↔ 400 配对断开，或出现非自指向 canonical；
5. **服务器错误**：PHP Fatal / Warning 错误日志增量非 0。

---

## 2. 秒级开关回滚设计 (Instant Killswitch Mechanism)

### 2.1 单行开关回退 (One Switch Flag)
在 `mu-plugins/fyzsxnb-p0-seo-patch.php` 顶部配置项中：

```php
// 切换为 false 即可瞬间 100% 恢复为 Legacy Resolver
define( 'FYZ_USE_RESOLVER_V2', false );
```

### 2.2 环境变量无触碰回退 (Zero-Deploy Environment Override)
若支持 `getenv('FYZ_USE_RESOLVER_V2')`，可在服务器或 `.env` 层面设置 `FYZ_USE_RESOLVER_V2=0`，实现**零代码修改、零 FTP 上传的瞬时生效**。

---

## 3. 回滚执行流程与校验核对表 (Post-Rollback Verification)

```text
[1. 触发回滚] ──► 将 FYZ_USE_RESOLVER_V2 置为 false
       │
       ▼
[2. 清除瞬态缓存] ──► 触发 wp_cache_flush() 或清理 LiteSpeed 页面缓存
       │
       ▼
[3. 只读健康巡检] ──► 检查 10 EN 篇与 10 RU 篇公网 HTML:
                     * EN 页面必须输出 lang="en-US", og:locale="en_US"
                     * RU 页面必须输出 lang="ru-RU", og:locale="ru_RU"
                     * Page 11 与 Page 400 首页 Hreflang 完好
                     * EN/RU 首页 Feed 文章数量与内容 100% 吻合历史基线
```
