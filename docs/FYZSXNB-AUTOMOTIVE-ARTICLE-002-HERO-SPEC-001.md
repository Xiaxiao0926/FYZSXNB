# Article 002 — Hero 制作规格卡（pending image-capable runtime）

**状态：** `HERO_PRODUCTION = DEFERRED` —— 本执行运行时无照片级图像生成能力；3 张概念 Figure 已程序化生成（见 assets/）。Hero 须由具备图像生成的运行环境（如 Gemini 工具链）按本规格生产；本卡与最终 QA 一并入库。

## 规格（源自 Visual Plan v2，Hero = APPROVED）

| 项目 | 值 |
|---|---|
| 场景 | 明亮现代的俄罗斯独立维修车间（СТО）标定工位；一辆**泛型中国品牌 SUV**（无 logo、无可辨识车款）停在 ADAS 标定架前，前方有 target 板；技师手持诊断平板操作多品牌扫描仪 |
| 风格 | 照片写实（photorealistic）；自然光 + 工位照明；东欧车间环境可信感 |
| 明确禁止 | 巨大文字/HUD 风/PPT 信息卡/暗色 CAD；任何"真实案例现场"暗示 |
| 尺寸 | 1200×630（OG card）/ 1200×675（article featured，裁剪友好）双规格 |
| 格式 | PNG/JPEG（quality ≥ 90），≤ 400KB/张 |
| 强制性标注 | alt/caption 必须含："Illustrative image, not a real workshop or vehicle event." |

## 推荐生成 prompt（英文，供图像工具使用）

"Photorealistic wide shot inside a clean, bright independent Russian auto-repair workshop: a generic modern Chinese-brand SUV (no badges, neutral silver-grey body) parked in an ADAS calibration bay facing a white calibration frame with two large target boards; a technician in a plain navy work shirt stands beside the front bumper holding a diagnostic tablet, looking at a multi-brand scanner screen mounted on a cart; daylight from workshop windows mixed with cool LED bay lighting; concrete floor with visible bay markings; believable Eastern-European independent repair shop atmosphere; no text overlays, no logos, no people faces close-up; 16:9 composition."

## 生成后处理（Publish Gate 内）

1. 上传 WP media；alt + title + caption 按 SEO payload 设置。
2. 多 UA 目检（desktop/mobile）确认无文字溢出/无 logo。
3. `_fyz_content` 元数据与 featured image 关联核对。