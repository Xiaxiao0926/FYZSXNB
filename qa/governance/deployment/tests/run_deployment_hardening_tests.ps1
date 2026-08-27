# run_deployment_hardening_tests.ps1 — Deployment Hardening Gate 001 test matrix
# (T01-T18). All write-path tests use -MockRoot (local fake transport). Every
# case runs as a child pwsh process so exit codes are authoritative.
$ErrorActionPreference = 'Stop'

$wrapper = 'C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\site-ops\run_ftp_deploy_secure.ps1'
$work = 'C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\qa\FYZSXNB-DEPLOYMENT-HARDENING-001'
$mockRoot = Join-Path $work 'mock-remote'
$fixture = Join-Path $work 'fixtures'
New-Item -ItemType Directory -Force -Path $mockRoot, $fixture | Out-Null

$remoteFile = 'wp-content/themes/fyzsxnb-neve-child/style.css'
$remoteFile2 = 'wp-content/themes/fyzsxnb-neve-child/brand-new.php'
$localNew = Join-Path $fixture 'new-style.css'
Set-Content -LiteralPath $localNew -Value '/* v1 */' -Encoding ASCII
$localHash = (Get-FileHash $localNew).Hash.ToLowerInvariant()
$mockRemotePath = Join-Path $mockRoot ($remoteFile -replace '/', '\')
$mock2 = Join-Path $mockRoot ($remoteFile2 -replace '/', '\')
New-Item -ItemType Directory -Force -Path (Split-Path -Parent $mockRemotePath) | Out-Null

function Reset-Remote([string]$content) {
    Set-Content -LiteralPath $mockRemotePath -Value $content -Encoding ASCII
    Remove-Item -LiteralPath $mock2 -Force -ErrorAction SilentlyContinue
}

$results = [System.Collections.Generic.List[object]]::new()
$allPass = $true

function Test-Case {
    param([string]$Name, [string[]]$ArgList, [int]$ExpectedExit, [string]$ExpectLabel)
    $out = & pwsh -NoProfile -File $wrapper $ArgList 2>&1 | Out-String
    $code = $LASTEXITCODE
    $ok = $code -eq $ExpectedExit
    $script:allPass = $allPass -and $ok
    $head = ($out.Trim() -split "`r?`n" | Where-Object { $_ } | Select-Object -First 2) -join ' | '
    $script:results.Add([pscustomobject]@{ test = $Name; expected = "$ExpectedExit ($ExpectLabel)"; exit = $code; pass = $ok; output_head = $head })
    Write-Host ("{0,-4} {1,-6} exit={2} expect={3}  {4}" -f $(if ($ok) { 'PASS' } else { 'FAIL' }), $Name, $code, $ExpectedExit, $head)
    return $out
}

function Assert-True([string]$Name, [bool]$Cond, [string]$Detail) {
    $script:allPass = $allPass -and $Cond
    $script:results.Add([pscustomobject]@{ test = $Name; expected = 'true'; exit = $(if ($Cond) { 0 } else { 1 }); pass = $Cond; output_head = $Detail })
    Write-Host ("{0,-4} {1,-6} {2}" -f $(if ($Cond) { 'PASS' } else { 'FAIL' }), $Name, $Detail)
}

# T01-T07 — argument validation failures
Reset-Remote '/* old remote */'
Test-Case 'T01' @() 1 'FAIL'
Test-Case 'T02' @('-Action', 'deploy', '-RemotePath', '') 1 'FAIL'
Test-Case 'T03' @('-Action', 'deploy', '-RemotePath', $remoteFile, '-MockRoot', $mockRoot) 1 'FAIL'
Test-Case 'T04' @('-Action', 'deploy', '-RemotePath', $remoteFile, '-SourcePath', 'C:\nope\missing.php', '-MockRoot', $mockRoot) 1 'FAIL'
Test-Case 'T05' @('-Action', 'deploy', '-RemotePath', '   ') 1 'FAIL'
Test-Case 'T06' @('-Action', 'deploy', '-RemotePath', 'wp-content/../wp-config.php') 1 'FAIL'
Test-Case 'T07' @('-Action', 'deploy', '-RemotePath', 'wp-content/*/x.php') 1 'FAIL'

# T08 — correct params, no Execute -> PREVIEW, no write
Reset-Remote '/* old remote */'
$before8 = Get-Content -Raw $mockRemotePath
$sha8 = (Get-FileHash $mockRemotePath).Hash.ToLowerInvariant()
Test-Case 'T08' @('-Action', 'deploy', '-RemotePath', $remoteFile, '-SourcePath', $localNew, '-MockRoot', $mockRoot, '-ExpectedRemoteSha256', $sha8) 0 'PREVIEW'
Assert-True 'T08nw' ((Get-Content -Raw $mockRemotePath) -eq $before8) 'mock remote unchanged'

# T09 — Execute without confirmation -> BLOCK exit 2, no write
Test-Case 'T09' @('-Action', 'deploy', '-RemotePath', $remoteFile, '-SourcePath', $localNew, '-MockRoot', $mockRoot, '-ExpectedRemoteSha256', $sha8, '-Execute') 2 'BLOCK'
Assert-True 'T09nw' ((Get-Content -Raw $mockRemotePath) -eq $before8) 'mock remote unchanged'

# T10 — confirmation without Execute -> PREVIEW exit 0, no write
Test-Case 'T10' @('-Action', 'deploy', '-RemotePath', $remoteFile, '-SourcePath', $localNew, '-MockRoot', $mockRoot, '-ExpectedRemoteSha256', $sha8, '-ConfirmProductionWrite', $remoteFile) 0 'PREVIEW'
Assert-True 'T10nw' ((Get-Content -Raw $mockRemotePath) -eq $before8) 'mock remote unchanged'

# T11 — remote precondition mismatch -> exit 3
Test-Case 'T11' @('-Action', 'deploy', '-RemotePath', $remoteFile, '-SourcePath', $localNew, '-MockRoot', $mockRoot, '-ExpectedRemoteSha256', ('0' * 64)) 3 'BLOCK'

# T12 — same local/remote hash -> NO_CHANGE exit 0, no write
Set-Content -LiteralPath $mockRemotePath -Value '/* v1 */' -Encoding ASCII
$sha12 = (Get-FileHash $mockRemotePath).Hash.ToLowerInvariant()
Test-Case 'T12' @('-Action', 'deploy', '-RemotePath', $remoteFile, '-SourcePath', $localNew, '-MockRoot', $mockRoot, '-ExpectedRemoteSha256', $sha12) 0 'NO_CHANGE'
Assert-True 'T12nw' ((Get-FileHash $mockRemotePath).Hash.ToLowerInvariant() -eq $sha12) 'mock remote unchanged'

# T13 — remote absent without AllowCreate -> BLOCK exit 3
Reset-Remote '/* old remote v3 */'
Test-Case 'T13' @('-Action', 'deploy', '-RemotePath', $remoteFile2, '-SourcePath', $localNew, '-MockRoot', $mockRoot, '-ExpectedRemoteSha256', ('1' * 64)) 3 'BLOCK'

# T14 — remote absent WITH AllowCreate, preview only -> exit 0, no write
Test-Case 'T14' @('-Action', 'deploy', '-RemotePath', $remoteFile2, '-SourcePath', $localNew, '-MockRoot', $mockRoot, '-AllowCreate') 0 'PREVIEW'
Assert-True 'T14nw' (-not (Test-Path -LiteralPath $mock2)) 'file not created during preview'

# T15 — backup failure blocks upload -> exit 4, remote unchanged
Reset-Remote '/* old remote t15 */'
$before15 = Get-Content -Raw $mockRemotePath
$sha15 = (Get-FileHash $mockRemotePath).Hash.ToLowerInvariant()
$env:FYZSXNB_MOCK_BACKUP_FAIL = '1'
Test-Case 'T15' @('-Action', 'deploy', '-RemotePath', $remoteFile, '-SourcePath', $localNew, '-MockRoot', $mockRoot, '-ExpectedRemoteSha256', $sha15, '-Execute', '-ConfirmProductionWrite', $remoteFile) 4 'BACKUP_FAIL'
Remove-Item Env:FYZSXNB_MOCK_BACKUP_FAIL -ErrorAction SilentlyContinue
Assert-True 'T15nw' ((Get-Content -Raw $mockRemotePath) -eq $before15) 'mock remote unchanged after backup failure'

# T16 — post-download hash mismatch -> exit 6
Reset-Remote '/* old remote t16 */'
$sha16 = (Get-FileHash $mockRemotePath).Hash.ToLowerInvariant()
$env:FYZSXNB_MOCK_VERIFY_FAIL = '1'
Test-Case 'T16' @('-Action', 'deploy', '-RemotePath', $remoteFile, '-SourcePath', $localNew, '-MockRoot', $mockRoot, '-ExpectedRemoteSha256', $sha16, '-Execute', '-ConfirmProductionWrite', $remoteFile) 6 'VERIFY_FAIL'
Remove-Item Env:FYZSXNB_MOCK_VERIFY_FAIL -ErrorAction SilentlyContinue

# T17 — full positive UPDATE
Reset-Remote '/* old remote t17 */'
$sha17 = (Get-FileHash $mockRemotePath).Hash.ToLowerInvariant()
Test-Case 'T17' @('-Action', 'deploy', '-RemotePath', $remoteFile, '-SourcePath', $localNew, '-MockRoot', $mockRoot, '-ExpectedRemoteSha256', $sha17, '-Execute', '-ConfirmProductionWrite', $remoteFile) 0 'DEPLOYED'
Assert-True 'T17v' ((Get-FileHash $mockRemotePath).Hash.ToLowerInvariant() -eq $localHash) 'mock remote == local after deploy'

# T17b — full positive CREATE
Test-Case 'T17b' @('-Action', 'deploy', '-RemotePath', $remoteFile2, '-SourcePath', $localNew, '-MockRoot', $mockRoot, '-AllowCreate', '-Execute', '-ConfirmProductionWrite', $remoteFile2) 0 'DEPLOYED'
Assert-True 'T17bv' ((Get-FileHash $mock2).Hash.ToLowerInvariant() -eq $localHash) 'created mock remote == local'

# T18 — secret leak scan
$outAll = ($results | ForEach-Object { $_.output_head }) -join ' '
$leaks = @('password', 'secret', 'FYZXSNB_FTP_PASSWORD', 'CredentialPath') | Where-Object { $outAll -match [regex]::Escape($_) }
$results.Add([pscustomobject]@{ test = 'T18'; expected = '0 secrets'; exit = $leaks.Count; pass = $leaks.Count -eq 0; output_head = "secret scan leaks=$($leaks -join ',')" })
if ($leaks.Count -gt 0) { $allPass = $false }
Write-Host ("{0,-4} {1,-6} leaks={2}" -f $(if ($leaks.Count -eq 0) { 'PASS' } else { 'FAIL' }), 'T18', $leaks.Count)

[pscustomobject]@{ generated_at = (Get-Date).ToUniversalTime().ToString('o'); all_tests_pass = $allPass; tests = $results } |
    ConvertTo-Json -Depth 8 | Set-Content -LiteralPath (Join-Path $work 'test-results.json') -Encoding UTF8
Write-Host ''
Write-Host "ALL TESTS PASS: $allPass"
exit $(if ($allPass) { 0 } else { 1 })