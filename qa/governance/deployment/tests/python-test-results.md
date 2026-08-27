# Python Entrypoints Closure Test Results — 2026-08-27

| Test | Pass | Detail |
|---|---:|---|
| PY01-deploy_frontend_patch.py | ✅ | exit=1 deprecation=True |
| PY01-deploy_home_inc.py | ✅ | exit=1 deprecation=True |
| PY01-deploy_kuajing_plugin.py | ✅ | exit=1 deprecation=True |
| PY01-add_ftp_retry.py | ✅ | exit=1 deprecation=True |
| PY02-args | ✅ | tombstone ignores args, exit 1 |
| PY05-no-default | ✅ | no default target in tombstone |
| PY06-no-ftp | ✅ | no FTP write primitives in tombstones |
| PY07-stor-zero | ✅ | remaining STOR-write files:  |
| PY07b-readers-readonly | ✅ | read-only checkers with write primitives:  |
| PY08-home-wrapper | ✅ | run_deploy_home.ps1 exit=1 (fail closed) |
| PY15-exit-prop | ✅ | exit code propagated = 1 |
| PY09-kuajing-wrapper | ✅ | run_deploy_kuajing.ps1 exit=1 (fail closed) |
| PY13-ps-matrix | ✅ | powerShell matrix still 25/25 (expected: ALL TESTS PASS: True) |
| PY14-no-secret | ✅ | secret tokens leaked:  |

**ALL TESTS PASS: True**
