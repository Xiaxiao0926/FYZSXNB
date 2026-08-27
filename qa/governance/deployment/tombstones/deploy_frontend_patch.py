#!/usr/bin/env python
"""DEPRECATED — deploy_frontend_patch.py (tombstone).

Direct production FTP writes are DISABLED for legacy Python entrypoints.
This script intentionally contains NO network write primitives.

Use the hardened deployment contract instead:
    work/site-ops/run_ftp_deploy_secure.ps1   (explicit target, preview default,
    -Execute + -ConfirmProductionWrite, -ExpectedRemoteSha256, backup, verify)
See work/site-ops/DEPLOYMENT-CONTRACT.md

Historical intent: one-shot theme patch deploy (design-system.css,
functions.php, research-wire.css) + REST cache purge — all superseded.
"""
import sys


def main() -> int:
    print("DEPRECATED: deploy_frontend_patch.py is deprecated.", file=sys.stderr)
    print("Direct production FTP writes are disabled.", file=sys.stderr)
    print("Use run_ftp_deploy_secure.ps1 (see DEPLOYMENT-CONTRACT.md).", file=sys.stderr)
    return 1


if __name__ == "__main__":
    sys.exit(main())
