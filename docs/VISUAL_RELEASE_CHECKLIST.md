# VISUAL_RELEASE_CHECKLIST.md: FYZSXNB 新文章发布前视觉合规检查单

> **使用说明**：任何新文章在执行 WordPress 发布前，必须由责任编辑或自动化 Agent 逐项勾选以下检查项。**存在任何未勾选项，一律驳回发布**。

---

## 一、基础规格与物理指标 (Specs & Formats)

- [ ] **1.1 尺寸严格达标**：分辨率为 `1200 × 675 px`，严格保持 `16:9` 比例。
- [ ] **1.2 体积轻量合规**：文件体积在 `100 KB – 250 KB` 之间，严禁超过 250 KB。
- [ ] **1.3 格式与色彩**：采用渐进式 WebP 或高质量 JPEG，色彩空间为标准 `sRGB`。
- [ ] **1.4 构图黄金比例**：严格遵循 `70% 真实物理场景 + 30% HUD 信息辅助层` 结构。

---

## 二、行业模板与设计规范 (Template & Styling)

- [ ] **2.1 模板归属明确**：正确匹配对应垂直行业模板（Template A / B / C / D / E）。
- [ ] **2.2 色彩主题一致**：
  - [ ] 汽车工程 (Template A): Amber `#f59e0b` / Crimson `#ef4444`
  - [ ] 汽车市场 (Template B): Sky Blue `#0ea5e9`
  - [ ] 工业备件 (Template C): Industrial Orange `#f97316`
  - [ ] 药政合规 (Template D): Ocean Blue `#0284c7` / Indigo `#6366f1`
  - [ ] 先进生物 (Template E): Emerald `#10b981` / Cyan `#0ea5e9`
- [ ] **2.3 品牌水印规范**：右上角包含 `FYZSXNB [VERTICAL] INTELLIGENCE` 标识。
- [ ] **2.4 底部 HUD 芯片**：包含 3 块信息卡片，文字精炼，突出技术参数或法规条款。

---

## 三、真实性与合规红线 (Authenticity & Compliance)

- [ ] **3.1 零抽象科技图**：无发光 DNA 螺旋、无悬浮纯蓝背景、无无场景球棍分子。
- [ ] **3.2 零假商业套图**：无医生摆拍握手、无假会议室大笑、无假医院广告风。
- [ ] **3.3 零官方公文冒充**：严禁伪造 FDA / NMPA / Roszdravnadzor 等官方印章与公文红头。
- [ ] **3.4 真实场景支撑**：具备真实工位、示波器、拆解总成、GMP 车间、冷链箱或检测仪。

---

## 四、正文深度配图 (In-Body Figures for Long-Form)

- [ ] **4.1 正文图数量**：长文（>1500字）已配置至少 2 张正文深度插图（流程图/结构图/参数图）。
- [ ] **4.2 拒绝封面重复**：正文内严禁重复插入 Featured Image 缩略图。
- [ ] **4.3 图题与说明完备**：每张正文图均具备独立 Figure 编号与详细说明文字。

---

## 五、SEO 元数据与系统部署 (SEO & Deployment)

- [ ] **5.1 规范文件名**：文件名为英文短横线命名（如 `fda-establishment-registration-hero.jpg`）。
- [ ] **5.2 ALT 描述客观**：ALT 文本真实客观描述画面，无关键词堆砌。
- [ ] **5.3 Caption 说明清晰**：Caption 说明图片与文章的业务逻辑联系。
- [ ] **5.4 文章成功绑定**：文章 `featured_media` 字段已成功绑定上传的 Media ID。
- [ ] **5.5 生产缓存刷新**：已触发 LiteSpeed / Nginx 缓存清理。
- [ ] **5.6 生产在线验收**：通过 HTTP 200 验证，且移动端 (390px) 响应式渲染完美。

---

**检查结论**：  
若全部勾选完毕，该文章符合 **Visual System 3.0** 发布标准，准予上线！
