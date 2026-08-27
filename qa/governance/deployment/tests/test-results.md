# FYZSXNB Deployment Hardening Test Results — 2026-08-27

| Test | Expected | Exit | Pass |
|---|---:|---:|---:|
| T01 | 1 (FAIL) | 1 | ✅ |
| T02 | 1 (FAIL) | 1 | ✅ |
| T03 | 1 (FAIL) | 1 | ✅ |
| T04 | 1 (FAIL) | 1 | ✅ |
| T05 | 1 (FAIL) | 1 | ✅ |
| T06 | 1 (FAIL) | 1 | ✅ |
| T07 | 1 (FAIL) | 1 | ✅ |
| T08 | 0 (PREVIEW) | 0 | ✅ |
| T08nw | true | 0 | ✅ |
| T09 | 2 (BLOCK) | 2 | ✅ |
| T09nw | true | 0 | ✅ |
| T10 | 0 (PREVIEW) | 0 | ✅ |
| T10nw | true | 0 | ✅ |
| T11 | 3 (BLOCK) | 3 | ✅ |
| T12 | 0 (NO_CHANGE) | 0 | ✅ |
| T12nw | true | 0 | ✅ |
| T13 | 3 (BLOCK) | 3 | ✅ |
| T14 | 0 (PREVIEW) | 0 | ✅ |
| T14nw | true | 0 | ✅ |
| T15 | 4 (BACKUP_FAIL) | 4 | ✅ |
| T15nw | true | 0 | ✅ |
| T16 | 6 (VERIFY_FAIL) | 6 | ✅ |
| T17 | 0 (DEPLOYED) | 0 | ✅ |
| T17v | true | 0 | ✅ |
| T17b | 0 (DEPLOYED) | 0 | ✅ |
| T17bv | true | 0 | ✅ |
| T18 | 0 secrets | 0 | ✅ |

**ALL TESTS PASS: True**

执行方式：pwsh -File work/site-ops/tests/run_deployment_hardening_tests.ps1（子进程退出码 + mock 传输）。
