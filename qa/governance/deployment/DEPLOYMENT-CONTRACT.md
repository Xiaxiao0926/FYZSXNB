# FYZSXNB Deployment Contract

**生效:** 2026-08-27（Deployment Hardening Gate 001 之后）| **范围:** 所有对 fyzsxnb.com 生产环境的文件部署

> 背景：2026-08-26 取证发现历史上出现过"本地模拟被误报为生产验证"的事故；
> 本契约确保部署工具与流程 fail-closed，且生产成功只能来自远端字节观测。

## 十条不可违反规则

1. **No implicit target** — RemotePath 与 LocalPath 必须显式指定，任何"默认 mu-plugin / 默认主题文件"行为已删除。缺失 = 报错退出（非零）。
2. **Default = preview** — 不带 `-Execute` 调用部署脚本只输出部署计划，绝不写生产。
3. **Execute must be explicit** — 只有显式 `-Execute` 才进入潜在写路径。
4. **Production confirmation required** — 写路径必须同时提供 `-ConfirmProductionWrite <exact remote path>`；token 与 RemotePath 不一致即 BLOCK。
5. **Remote precondition required** — 更新已有文件必须提供 `-ExpectedRemoteSha256` 且与远端当前实际 SHA256 一致，否则 BLOCK（防旧状态覆盖新状态）。
6. **Existing file must backup first** — 更新前必须下载当前远端文件到 `work/deployments/backups/<timestamp>/`，备份失败 = 中止上传。
7. **Post-write remote SHA verification required** — 上传后必须重新下载远端并按字节 SHA256（+size）验证；验证失败以非零退出（不得返回成功）。
8. **Reports cannot self-certify deployment** — 只有"远端重下载字节 == 本地字节"才是 DEPLOYED 证据；plan 与 result 分离（`deploy-log/*-plan.json` / `*-result.json`），preview 不产生 result。
9. **Secrets never logged** — 密码/token 绝不进入任何输出、日志、plan/result JSON 或异常转储。
10. **Production evidence = observed remote state** — 判定生产状态一律以生产快照/hash/在线行为为准，历史 Markdown 声明不构成生产证据。

## 执行要点

- 退出码：`0 = PASS/PREVIEW/NO_CHANGE` · `1 = 参数校验` · `2 = preflight/确认缺失` · `3 = 远端前置不匹配` · `4 = 备份失败` · `5 = 上传失败` · `6 = 部署后验证失败`。失败绝不返回 0。
- 新建文件必须显式 `-AllowCreate`；远端缺失而无 `-AllowCreate` = BLOCK。
- 本地/远端哈希一律使用**文件字节 SHA256**（`Get-FileHash`），不使用 git blob hash（避免 CRLF/LF 规整混淆）。
- 同哈希 = NO_CHANGE，跳过上传。
- 测试：`work/site-ops/tests/run_deployment_hardening_tests.ps1`（mock 传输，矩阵 T01-T18）。

## 入口文件

- `work/site-ops/run_ftp_deploy_secure.ps1`（wrapper：读取加密凭据 → 注入环境 → 调用 deployer）
- `work/site-ops/ftp_p0_deployer.ps1`（执行体：snapshot / deploy / verify / rollback；fail-closed）
- 已知其余 FTP 写入口（历史 Python 部署器，未在本 Gate 硬化，见 Hardening 报告）：
  `deploy_frontend_patch.py`、`deploy_home_inc.py`、`deploy_kuajing_plugin.py` — 使用前必须人工核对目标并建议迁移到本契约工具。