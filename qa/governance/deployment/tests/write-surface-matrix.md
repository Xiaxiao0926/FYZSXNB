# FYZSXNB Production Write Surface Matrix — 2026-08-27

| Entrypoint | Direct write | Hardened contract | Status |
|---|---:|---:|---:|
| ftp_p0_deployer.ps1 | YES | YES | APPROVED |
| run_ftp_deploy_secure.ps1 | wrapper | YES | APPROVED |
| deploy_frontend_patch.py | NO | deprecated tombstone | CLOSED |
| deploy_home_inc.py | NO | deprecated tombstone | CLOSED |
| deploy_kuajing_plugin.py | NO | deprecated tombstone | CLOSED |
| add_ftp_retry.py | NO | deprecated tombstone (anti-rebuild) | CLOSED |

UNHARDENED_KNOWN_PRODUCTION_WRITE_ENTRYPOINTS = 0
PRODUCTION_WRITE_IMPLEMENTATION_COUNT = 1
