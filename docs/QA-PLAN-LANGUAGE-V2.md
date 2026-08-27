# FYZSXNB Language Contract V2 — Comprehensive QA Test Plan

**Document ID:** `FYZ-DOC-20260820-QA-PLAN-044`  
**Stage:** `0.4.4`  
**Author:** Google Gemini Flash 3.7  
**Scope:** Automated Test Cases, Regression Assertions, and Acceptance Gates  

---

## 1. 测试用例矩阵 (Test Suite Matrix)

```text
+---------------------------------------------------------------------------------------------------------+
|                                    LANGUAGE V2 自动化质量门禁矩阵                                        |
+----------+------------------------------------+------------------------------------+--------------------+
| 用例编号 | 测试场景                           | 输入与前置条件                     | 预期断言标准       |
+----------+------------------------------------+------------------------------------+--------------------+
| TC-01    | 存量 EN 文章回归测试               | Post 513 (meta=en, no Cat54)       | lang="en-US", og=en_US, Schema="en-US" |
| TC-02    | 存量 RU 文章回归测试               | Post 484 (meta=ru, Cat54=YES)      | lang="ru-RU", og=ru_RU, Schema="ru-RU" |
| TC-03    | 存量 EN 首页 Feed 回归             | Page 11 (URL: /)                   | 4 Signals + 6 Guides, 0 RU/ZH 泄漏 |
| TC-04    | 存量 RU 首页 Feed 回归             | Page 400 (URL: /ru/)               | 4 Signals + 5 Guides, 0 EN/ZH 泄漏 |
| TC-05    | 新增 ZH 文章标准解析               | Post 479 (meta=zh, no Cat54)       | lang="zh-CN", og=zh_CN, Schema="zh-CN" |
| TC-06    | ZH 内容 Feed 绝对隔离              | 任意 meta=zh 文章                  | 绝不出现在 Page 11 与 Page 400 Feed |
| TC-07    | 结构冲突 A: ZH 带 Cat54            | Mock (meta=zh, Cat54=YES)          | valid=false, conflict=true, 降级 legacy |
| TC-08    | 结构冲突 B: RU 缺 Cat54            | Mock (meta=ru, Cat54=NO)           | valid=false, conflict=true, 降级 legacy |
| TC-09    | 结构冲突 C: EN 带 Cat54            | Mock (meta=en, Cat54=YES)          | valid=false, conflict=true, 降级 legacy |
| TC-10    | 缺失 Meta + 旧 RU ID 兜底          | Mock (meta=empty, ID in legacy)    | resolved=ru, source=legacy, valid=true |
| TC-11    | 缺失 Meta + 普通 ID 默认兜底       | Mock (meta=empty, ID not in legacy)| resolved=en, source=default, valid=true |
| TC-12    | 后台 Meta Box 发布门禁             | REST/Form 创建缺少 language/kind   | 自动回退 pending, 阻断 publish 泄漏 |
+----------+------------------------------------+------------------------------------+--------------------+
```

---

## 2. 自动化执行脚本规划 (Automated Execution Scripts)

1. **`qa/locale_detector_v2_test.py`**：
   - 离线合成单元测试，执行 TC-01 至 TC-11 全部 11 个分支场景，要求 **100% PASS**。
2. **`qa/feed_v2_accept.py`**：
   - 部署后在线验收测试，校验公网 HTTP 状态、Feed parity、HTML lang 属性及 Schema 标签，要求 **0 回退**。
