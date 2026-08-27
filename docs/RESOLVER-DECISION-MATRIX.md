# FYZSXNB Locale Resolver Architecture — Decision Matrix & Roadmap

**Document ID:** `FYZ-DOC-20260820-RESOLVER-MATRIX`  
**Stage:** `0.4.3`  
**Author:** Google Gemini Flash 3.7  
**Status:** `DESIGN_PROPOSAL`  
**Scope:** Strategy & Timing for Metadata-First Resolver Deployment  

---

## 1. 决策背景 (Background)

在 0.4.1 阶段，我们成功完成了本地 `fyzsxnb_resolve_content_locale()` 双读解析器的开发与离线测试（`LOCAL_PASS`）。但生产数据核验与 0.4.2 审计证实：
1. **现网运行安全稳定**：生产环境现网实际基于 `Legacy RU IDs + Category 54` 运行，25 篇 RU 文章 100% 具备 Category 54，11 篇新 RU 文章已通过 Category 54 被正确识别。
2. **0.4.1 本地版本仅支持二元 (`en`/`ru`)**：若现在将 0.4.1 的二元解析器推上生产，一旦后续正式启用 `zh`（Language Contract V2），又必须对核心 MU-Plugin 进行二次修改与重复部署。

---

## 2. 方案比选矩阵 (Options Comparison Matrix)

| 评估维度 | 方案 A: 立即部署 0.4.1 二元 Resolver | 方案 B: 随 Language Contract V2 统一发布 V2 Resolver (推荐) | 方案 C: 无限期推迟，维持现有 Category 54 逻辑 |
|:---|:---|:---|:---|
| **核心动作** | 立即将 `fyzsxnb-p0-seo-patch.php` v1.4.0（en/ru 双读）部署生产 | 暂缓上线；待 V2 契约审批后，将 Resolver 升级为 `en/ru/zh` 三元并一次性上线 | 不再修改 MU-Plugin，长期依靠 Category 54 辅助识别 |
| **生产写入风险** | 中等（触碰核心 SEO 文件） | **最低**（单次闭环部署，避免二次动刀） | 零（无生产变动） |
| **技术债消除度** | 部分消除（仅覆盖 en/ru） | **彻底消除**（直接建立统一多语言解析标准） | 不消除（旧硬编码 ID 表长期滞留） |
| **对中文资产支持** | 无法支持（仍将 zh 降级为 en/ru 冲突） | **完美支持**（原生识别 zh 并输出 zh-CN SEO） | 无法支持 |
| **工程投入产出比** | 低（重复测试与发布） | **极高（一步到位，架构优雅）** | 低（遗留认知负担） |
| **推荐评级** | ★★☆☆☆ (不推荐) | **★★★★★ (强烈推荐)** | ★★★☆☆ (保守备选) |

---

## 3. 推荐路线规划 (Recommended Execution Roadmap)

```text
[当前阶段: 0.4.3 完成 V2 架构设计]
                   │
                   ▼
[阶段 0.4.4: Language Contract V2 本地实现与测试]
  ├── 插件/发布器 meta box 扩展支持 'zh'
  ├── 将 MU-plugin Locale Resolver 升级为三元 (en / ru / zh)
  └── 编写支持 en/ru/zh 的完整单元测试与 Parity 校验矩阵
                   │
                   ▼
[阶段 0.4.5: 生产平滑发布与 10 篇中文资产确权]
  ├── 严格按照 Guard -> Snapshot -> Deploy -> Verification 发布 MU-Plugin V2
  └── 对 10 篇 ZH_CONTENT 文章执行安全的批量元数据确权写入
```

---

## 4. 最终决策建议

- **0.4.1 Resolver 优先级调整**：由“紧急部署”调整为 **“随 Language Contract V2 统一部署（DEPLOY_DEFERRED）”**。
- **现网稳定性保障**：在 0.4.4 启动前，生产环境保持完全只读与冻结，杜绝无收益的非必要生产写入。
