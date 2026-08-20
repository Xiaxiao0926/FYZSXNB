# FYZSXNB 0.4.0 — Translation Pair Contract Specification

**Version:** `1.1.0` (0.4.0-A.1 Foundation Hardening)  
**Status:** `FROZEN_SPEC`  
**Scope:** Local Infrastructure & Metadata Contract Hardening Only (Zero Public SEO Output Changes)

---

## 1. 核心定位与三层模型

FYZSXNB 站点内容模型确立为清晰的三层架构：

```text
第 1 层：文章固有属性 (Content Attributes)
├── _fyz_content_language = 'en' | 'ru'
└── _fyz_content_kind     = 'signal' | 'guide'

第 2 层：翻译配对关系 (Translation Relation - 0.4.0 新增)
└── _fyz_translation_group = 'fyz-tp-{UUID}' (可选，稳定，受控 REST 管理，不替代语言字段)

第 3 层：首页动态 Feed (Feed Consumption - 0.3.6 冻结)
├── explicit locale
├── explicit type
├── transient cache
└── precise invalidation
```

---

## 2. 字段规范与安全访问契约

- **字段名**：`_fyz_translation_group`
- **类型**：`string`
- **格式**：`fyz-tp-[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}`
- **安全隔离契约**：
  - 标准 WordPress Posts REST (`/wp/v2/posts/{id}`) 对该 meta 设置 `show_in_rest => false` 或只读，**严禁普通 REST 接口直接写入该字段**以绕过配对校验。
  - 所有配对变更必须通过专用受控 REST 接口：
    * `GET  /wp-json/fyzsxnb/v1/translation-pairs/inspect?post_id={id}`
    * `POST /wp-json/fyzsxnb/v1/translation-pairs/pair` (`en_id`, `ru_id`, 可选 `group_id`)
    * `POST /wp-json/fyzsxnb/v1/translation-pairs/unpair` (`post_id`)
  - 接口强制检查 `current_user_can('edit_posts')`，返回结构化错误码与 `no-store` 头。

---

## 3. 七大核心不变量 (Invariants)

1. **INV-01 (唯一性)**：同一个 valid translation group 内，最多包含 1 篇已发布的 EN 文章与 1 篇已发布的 RU 文章。
2. **INV-02 (无重复语言冲突)**：同一 group 内严禁出现 2 篇及以上相同语言的已发布文章。若出现冲突，resolver 必须返回 `null`，严禁猜测。
3. **INV-03 (RU 结构约束)**：Group 内的 RU 成员必须同时满足 `_fyz_content_language = 'ru'` 且属于 `Category 54`（Russian Library）。
4. **INV-04 (EN 结构约束)**：Group 内的 EN 成员必须满足 `_fyz_content_language = 'en'` 且严禁属于 `Category 54`。
5. **INV-05 (发布状态约束)**：配对操作仅接受 `post_status === 'publish'` 的已发布文章；草稿/待审文章禁止配对以防未来制造隐藏冲突。
6. **INV-06 (Unknown 排除)**：13 篇 unknown metadata 文章严禁分配 translation group，resolver 严禁将其作为有效配对返回。
7. **INV-07 (无 Heuristic)**：严禁基于标题相似度、Slug 正则、Cyrillic 匹配或机翻相似度进行任何自动配对。

---

## 4. 目标 Group 预检与补偿回滚设计 (Atomic Compensatory Rollback)

### 4.1 写入前目标 Group 成员模拟预检
在写入前，系统使用 `posts_per_page => -1` 全量读取目标 group 现有成员，模拟加入候选 EN 与 RU 文章：
- 若目标 group 中已存在其他 EN 文章或其他 RU 文章，立即返回 `duplicate_locale_en` / `duplicate_locale_ru` 并阻断写入（`NO_WRITE`）。

### 4.2 两阶段补偿回滚机制
Pair 操作涉及两篇文章的元数据更新，通过应用层补偿机制保证原子性：
```text
1. 记录初始状态：old_group_a, old_group_b
2. 更新 Post A -> 校验写入结果 (若失败 -> 退出)
3. 更新 Post B -> 校验写入结果 (若失败 -> 回滚 Post A 至 old_group_a，校验回滚后退出)
4. 全量验证目标 Group 不变量 (若校验失败 -> 回滚 Post A 和 Post B 至初始状态)
5. 返回成功
```

### 4.3 语法错误拒绝与解绑明确性
- 对非法 UUID 格式输入，Sanitizer/Validator 必须返回明确错误并拒绝操作，**严禁静默转为空字符串**。
- 空字符串（`''`）仅由显式的 `unpair` 接口产生。

---

## 5. Ops 工具契约 (`manage_translation_pair.py`)

- **内建 Guard 门禁**：在执行 `--apply` 前自动调用 `codex_usage_guard.mjs 5`，若 `pause_required=true` 或状态异常则立即退出，禁止发起写请求。
- **安全凭据集成**：通过 `run_translation_pair_secure.ps1` 注入受保护的 CLIXML 凭据，禁止在 Python 工具中长期保存明文密码，禁止打印 Auth header。
- **Server-Side Inspect**：`inspect` 模式直接调用插件 `/translation-pairs/inspect` 端点，由服务端单一事实源返回状态，Python 侧不重复实现 Group 查询。
- **纯显式 ID 模式**：仅支持 `inspect`, `pair` (`--en-id`, `--ru-id`), `unpair` (`--post-id`)，绝无 `--auto-detect`/`--guess` 模式。

---

## 6. 演进与部署红线

- **0.4.0-A.1（当前）**：基础架构加固、本地 QA 验收。
- **生产写入红线**：当前 `CONFIRMED_PAIR = 0`，因此生产环境**暂不部署插件、暂不写入元数据、暂不修改 MU-plugin**。生产站点保持 100% 零变化。
