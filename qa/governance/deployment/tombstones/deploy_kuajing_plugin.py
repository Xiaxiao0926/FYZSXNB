#!/usr/bin/env python
"""DEPRECATED — deploy_kuajing_plugin.py (tombstone).

Direct production FTP writes are DISABLED for legacy Python entrypoints.
This script intentionally contains NO network write primitives.

Use the hardened deployment contract instead:
    work/site-ops/run_ftp_deploy_secure.ps1   (per-file, explicit target,
    preview default, -Execute + -ConfirmProductionWrite, precondition,
    backup, post-upload SHA256 verify)
See work/site-ops/DEPLOYMENT-CONTRACT.md

Historical intent: recursive bulk deploy of the kuajing-persistence plugin
(merged plugin file + dist assets) via FTP mkd/STOR — DIRECT RECURSIVE
PRODUCTION WRITES ARE NO LONGER ALLOWED. Any future kuajing-persistence
release must go through a dedicated approved deployment gate using the
hardened contract per file.
"""
import sys


def main() -> int:
    print("DEPRECATED: deploy_kuajing_plugin.py is deprecated.", file=sys.stderr)
    print("Direct production FTP writes (incl. recursive dist sync) are disabled.", file=sys.stderr)
    print("Use run_ftp_deploy_secure.ps1 per file (see DEPLOYMENT-CONTRACT.md).", file=sys.stderr)
    return 1


if __name__ == "__main__":
    sys.exit(main())
