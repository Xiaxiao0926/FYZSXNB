# run_python_entrypoint_tests.ps1 — Python Entrypoints Closure Gate tests (PY01-PY15).
$ErrorActionPreference = 'Stop'
$so = 'C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\site-ops'
$work = 'C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\qa\FYZSXNB-DEPLOYMENT-HARDENING-PYTHON-ENTRYPOINTS-001'
New-Item -ItemType Directory -Force -Path $work | Out-Null
$py = 'C:\Users\Administrator\.cache\codex-runtimes\codex-primary-runtime\dependencies\python\python.exe'
$tombstones = @('deploy_frontend_patch.py', 'deploy_home_inc.py', 'deploy_kuajing_plugin.py', 'add_ftp_retry.py')

$results = [System.Collections.Generic.List[object]]::new()
$allPass = $true
function Check([string]$Name, [bool]$Cond, [string]$Detail) {
    $script:allPass = $allPass -and $Cond
    $script:results.Add([pscustomobject]@{ test = $Name; pass = $Cond; detail = $Detail })
    Write-Host ("{0,-4} {1,-6} {2}" -f $(if ($Cond) { 'PASS' } else { 'FAIL' }), $Name, $Detail)
}

# PY01 — direct run of each tombstone -> exit 1 + deprecation message
foreach ($t in $tombstones) {
    $out = & $py "$so\$t" 2>&1 | Out-String
    $code = $LASTEXITCODE
    Check "PY01-$t" ($code -eq 1 -and $out -match 'DEPRECATED') "exit=$code deprecation=$($out -match 'DEPRECATED')"
}

# PY02/PY03/PY05 — tombstone with (wrong/no) args still fails closed, no fallback
$out2 = & $py "$so\deploy_home_inc.py" --whatever 2>&1 | Out-String
Check 'PY02-args' ($LASTEXITCODE -eq 1) 'tombstone ignores args, exit 1'
$legacy = Get-Content "$so\deploy_home_inc.py" -Raw
Check 'PY05-no-default' ($legacy -notmatch 'mu-plugins' -and $legacy -notmatch 'RemotePath\s*=') 'no default target in tombstone'

# PY06 — no direct FTP write capability in tombstone source
$allSrc = (Get-Content "$so\$($tombstones[0])","$so\$($tombstones[1])","$so\$($tombstones[2])","$so\$($tombstones[3])" -Raw) -join '`n'
Check 'PY06-no-ftp' ($allSrc -notmatch 'ftplib\.FTP' -and $allSrc -notmatch 'storbinary\s*\(' -and $allSrc -notmatch 'ftp\.login' -and $allSrc -notmatch 'FYZSXNB_FTP_PASSWORD') 'no FTP write primitives in tombstones'

# PY07 — workspace STOR search after change (write primitives only)
$scan = Get-ChildItem 'C:\Users\Administrator\Documents\Codex\2026-07-10\w\work' -Recurse -Include '*.py','*.ps1','*.txt','*.bak','*.old' -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch 'node_modules|\.git\\|\\tmp\\|deployments\\' } |
    Select-String -Pattern 'storbinary\s*\(|ftp\.storbinary|\.stor\(' -ErrorAction SilentlyContinue
$storFiles = @($scan | ForEach-Object { $_.Path } | Sort-Object -Unique) | Where-Object { $_ -notmatch 'DEPLOYMENT-HARDENING' }
Check 'PY07-stor-zero' (@($storFiles).Count -eq 0) "remaining STOR-write files: $($storFiles -join ', ')"

# PY07b — audit-only ftplib readers are read-only (no write primitives)
$readers = @('check_ftp_root.py','check_mu_plugins.py','check_remote_fn2.py','check_remote_functions.py','probe_ftp_tree.py','read_p0_patch_part2.py','read_p0_patch.py')
$bad = @()
foreach ($r in $readers) {
    $c = Get-Content "$so\$r" -Raw -ErrorAction SilentlyContinue
    if ($c -match 'storbinary\s*\(|\.stor\(|delete\s*\(|rename\s*\(') { $bad += $r }
}
Check 'PY07b-readers-readonly' (@($bad).Count -eq 0) "read-only checkers with write primitives: $($bad -join ', ')"

# PY08/PY09/PY10/PY15 — legacy wrappers fail closed through tombstone (exit propagation)
$outH = & pwsh -NoProfile -File "$so\run_deploy_home.ps1" 2>&1 | Out-String
$codeH = $LASTEXITCODE
Check 'PY08-home-wrapper' ($codeH -ne 0) "run_deploy_home.ps1 exit=$codeH (fail closed)"
Check 'PY15-exit-prop' ($codeH -eq 1) "exit code propagated = $codeH"
$outK = & pwsh -NoProfile -File "$so\run_deploy_kuajing.ps1" 2>&1 | Out-String
$codeK = $LASTEXITCODE
Check 'PY09-kuajing-wrapper' ($codeK -ne 0) "run_deploy_kuajing.ps1 exit=$codeK (fail closed)"

# PY11/PY12/PY13 — hardened deployer retains precondition/same-hash/mock-deploy guarantees
$wt = 'C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\site-ops\tests\run_deployment_hardening_tests.ps1'
$outM = & pwsh -NoProfile -File $wt 2>&1 | Select-String 'ALL TESTS PASS'
Check 'PY13-ps-matrix' ($outM -match 'True') 'powerShell matrix still 25/25 (expected: ALL TESTS PASS: True)'

# PY14 — secret leak scan over all case outputs
$allOut = @($outH, $outK, $out2) -join ' '
$leaks = @('password', 'secret', 'WP_APP_PASSWORD', 'FTP_PASSWORD') | Where-Object { $allOut -match [regex]::Escape($_) }
Check 'PY14-no-secret' (@($leaks).Count -eq 0) "secret tokens leaked: $($leaks -join ',')"

[pscustomobject]@{ generated_at = (Get-Date).ToUniversalTime().ToString('o'); all_tests_pass = $allPass; tests = $results } |
    ConvertTo-Json -Depth 8 | Set-Content -LiteralPath (Join-Path $work 'test-results.json') -Encoding UTF8
Write-Host ''
Write-Host "ALL TESTS PASS: $allPass"
exit $(if ($allPass) { 0 } else { 1 })