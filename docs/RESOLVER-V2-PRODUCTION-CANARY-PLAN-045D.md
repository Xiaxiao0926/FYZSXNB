# FYZSXNB Resolver V2 — Production Canary Deployment Master Plan

**Document ID:** `FYZ-DOC-20260820-CANARY-045D`  
**Stage:** `0.4.5-D`  
**Author:** Google Gemini Flash 3.7  
**Scope:** Production Canary Methodology, Deployment Stages, and Feature Activation  

---

## 1. 核心发布哲学 (Deployment Philosophy)

> **“代码部署 $\neq$ 特性启用” (Deploy Code $\neq$ Enable Feature)**

生产部署必须实施**物理部署与业务生效彻底解耦**的策略：
1. **先部署带有默认关闭开关的代码**：即使生产环境加载了新文件，业务执行路径 100% 维持现有 Legacy 逻辑；
2. **通过只读探针确认零回归**：在开关关闭状态下验证生产 HTML / Feed 0 差异；
3. **金丝雀灰度（Canary）验证**：通过受控内部探针单独验证 Resolver V2 解析行为；
4. **全量切流（Full Enable）**：仅需变更单一配置常数，实现零中断平滑切换；
5. **秒级回退保障（Killswitch）**：任何阶段若有异常，均可在 10 分钟内完成确定性回滚。

---

## 2. 生产灰度六阶段操作规程 (6-Stage Production Roadmap)

```text
[Stage 0: 生产备份与指纹冻结]
  ├─ 1. 触发 WordPress 数据库元数据快照导出
  ├─ 2. 记录当前生产物理文件 SHA256 (fyzsxnb-p0-seo-patch.php)
  └─ 3. Git 打上发布候选 Tag: v0.4.5-rc1
         │
         ▼
[Stage 1: 默认关闭部署 (Flag = false)]
  ├─ 1. FTP 部署包含 Resolver V2 与分发器的 MU-Plugin
  └─ 2. 确认生产 wp-config.php / 常量 FYZ_USE_RESOLVER_V2 = false
         │
         ▼
[Stage 2: 生产被动基线校验 (Passive Verification)]
  ├─ 1. 自动探针抓取 10 篇 EN 与 10 篇 RU 公网页面
  └─ 2. 确认在 Flag 关闭状态下，公网 SEO 标签与基线 100% 逐位一致
         │
         ▼
[Stage 3: 金丝雀灰度验证 (Canary Mode)]
  ├─ 1. 启用管理员内部探针 / HTTP 请求头 (X-FYZ-Resolver-V2: 1)
  ├─ 2. 验证 HTML lang / OG / Schema 升级至 V2 的解析输出
  └─ 3. 确认 13 篇 Unknown 维持安全隔离，无 Feed 泄漏
         │
         ▼
[Stage 4: 全量生产切流 (Full Switch)]
  ├─ 1. 在 wp-config.php 中声明 define('FYZ_USE_RESOLVER_V2', true);
  ├─ 2. 清理 LiteSpeed 与瞬态对象缓存
  └─ 3. 运行 30 篇重点样本公网自动巡检 (全部 PASS)
         │
         ▼
[Stage 5: 长期观测与资产清理 (Post-Switch Window)]
  ├─ 1. 维持 30 ~ 90 天观察期，监控 GSC 搜索表现与爬虫信号
  └─ 2. 待全站 100% 元数据确权且 Legacy 调用为 0 后，退役硬编码列表
```

---

## 3. 部署前检查清单 (Pre-deployment Checklist)

| 检查项目 | 标准与要求 | 状态 |
|:---|:---|:---:|
| **Git 状态** | 工作区干净，提交历史明确 (`9feb0ab` / `3355f3c` / `1d99a60`) | **READY** |
| **测试覆盖** | 0.4.5-B 离线集成 31/31 PASS，0.4.5-C1 影子 83/83 MATCH | **READY** |
| **语法检查** | PHP 8.5.9 CLI Lint 零错误、零 Warning | **READY** |
| **开关默认值** | `FYZ_USE_RESOLVER_V2` 代码硬编码默认值为 `false` | **READY** |
| **回滚包准备** | 生产当前物理文件 `fyzsxnb-p0-seo-patch.php` 本地冷备就绪 | **READY** |
| **数据库变更** | **0 DB Schema 变更，0 数据库写入要求** | **READY** |

---

## 4. 金丝雀策略比选与推荐 (Canary Strategy Selection)

| 方案 | 实现方式 | 优缺点分析 | 推荐度 |
|:---|:---|:---|:---:|
| **Option A (流量百分比)** | 随机 5% 用户切 V2 | 搜索爬虫可能遭遇同一个 URL 频繁抖动，不利于 SEO 信号稳定 | ★★☆☆☆ |
| **Option B (内部请求头/参数)** | `X-FYZ-Resolver-V2: 1` 或 `?fyz_v2_preview=1` | **零影响公网真实流量与爬虫，支持确定性白盒校验** | **★★★★★ (推荐)** |
| **Option C (部分文章试运行)** | 仅对特定 5 篇新文章启用 V2 | 逻辑分支碎片化，难以全局控制 | ★★★☆☆ |
| **Option D (直接全量切流)** | 部署即全量开启 | 无法在生产真实环境中实施灰度缓冲 | ★★☆☆☆ |

> **决议**：采用 **Option B (受控内部探针 Canary)** 结合 **全站开关 (Stage 4)** 进行生产演进。
