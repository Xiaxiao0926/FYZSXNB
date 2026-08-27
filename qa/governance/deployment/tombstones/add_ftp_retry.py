#!/usr/bin/env python
"""DEPRECATED — add_ftp_retry.py (tombstone).

This helper previously rewrote deploy_home_inc.py to add FTP retry loops.
Rebuilding legacy direct-FTP deployers is no longer allowed: direct production
writes must go through the hardened deployment contract only.

Use work/site-ops/run_ftp_deploy_secure.ps1
See work/site-ops/DEPLOYMENT-CONTRACT.md
"""
import sys


def main() -> int:
    print("DEPRECATED: add_ftp_retry.py is deprecated.", file=sys.stderr)
    print("It must not be used to rebuild legacy FTP deployers.", file=sys.stderr)
    return 1


if __name__ == "__main__":
    sys.exit(main())
