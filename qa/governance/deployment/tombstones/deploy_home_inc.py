#!/usr/bin/env python
"""DEPRECATED — deploy_home_inc.py (tombstone).

Direct production FTP writes are DISABLED for legacy Python entrypoints.
This script intentionally contains NO network write primitives.

Use the hardened deployment contract instead:
    work/site-ops/run_ftp_deploy_secure.ps1
See work/site-ops/DEPLOYMENT-CONTRACT.md

Historical intent: one-shot theme template deploy (inc/home.php,
inc/cars-from-china.php, template-parts, functions.php, 404.php) + REST cache
purge — all superseded. run_deploy_home.ps1 now fails closed through this
tombstone.
"""
import sys


def main() -> int:
    print("DEPRECATED: deploy_home_inc.py is deprecated.", file=sys.stderr)
    print("Direct production FTP writes are disabled.", file=sys.stderr)
    print("Use run_ftp_deploy_secure.ps1 (see DEPLOYMENT-CONTRACT.md).", file=sys.stderr)
    return 1


if __name__ == "__main__":
    sys.exit(main())
