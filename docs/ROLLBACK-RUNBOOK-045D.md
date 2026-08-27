# FYZSXNB Resolver V2 — Production Emergency Rollback Runbook

**Document ID:** `FYZ-DOC-20260820-ROLLBACK-045D`  
**Stage:** `0.4.5-D`  
**Target SLA:** `< 10 minutes` (Mean Time to Rollback)  
**Author:** Google Gemini Flash 3.7  
**Scope:** Production Incident Triggers, Instant Mitigation, and Recovery Verification  

---

## 1. 硬性回滚触发条件 (Hard Trigger Conditions)

若在灰度或全量上线后观测到以下任一现象，操作员**无须申请，必须立即执行回滚**：

1. **SEO 结构损坏**：Google 富媒体测试工具（Rich Results Test）报 `inLanguage` 校验失败，或 HTML 缺少 `lang` 属性；
2. **首页 Feed 泄漏**：EN 首页（Page 11）或 RU 首页（Page 400）出现语种不匹配的文章；
3. **Canonical 意外漂移**：任何文章页面的 canonical URL 偏离其原生永久链接；
4. **服务器运行时告警**：PHP 错误日志出现与 `fyzsxnb_resolve_content_locale` 相关的 Warning 或 Error；
5. **搜索引擎信号异常**：Google Search Console 报告国际化与多语言定向突发错误。

---

## 2. 10 分钟三步回滚操作规程 (3-Step Emergency Protocol)

```text
[Step 1: 关闭特性开关] (预计耗时: 1 分钟)
  └─► 在 wp-config.php 中将常量声明置为 false:
      define( 'FYZ_USE_RESOLVER_V2', false );
      (或在服务器环境设置 FYZ_USE_RESOLVER_V2=0)

[Step 2: 全局清除缓存] (预计耗时: 2 分钟)
  └─► 触发 LiteSpeed 全局缓存清理及 WordPress 瞬态缓存清理:
      wp litespeed-purge all (或后台点击 Purge All)

[Step 3: 自动化回退验证] (预计耗时: 3 分钟)
  └─► 运行生产只读验证脚本:
      py -3 qa/production_seo_verify.py
      确认 10 篇 EN 恢复 lang="en-US", 10 篇 RU 恢复 lang="ru-RU"
```

---

## 3. 终极物理兜底回滚 (Secondary Hard Fallback)

若代码本身发生未知语法致命错误导致开关无法读取：
1. **立即通过 FTP / SSH 将备份文件覆盖回生产**：
   * 恢复源：`work/agent-handoff/backups/fyzsxnb-p0-seo-patch-v1.3.1.php`
   * 目标路径：`wp-content/mu-plugins/fyzsxnb-p0-seo-patch.php`
2. **校验 SHA256** 与上线前快照一致；
3. 清理全站页面缓存。
