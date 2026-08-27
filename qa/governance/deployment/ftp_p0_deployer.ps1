# ftp_p0_deployer.ps1 — FYZSXNB production FTP deployer (HARDENED, fail-closed).
#
# HARDENING GATE 001 CONTRACT (FYZSXNB-TAKEOVER-DEPLOYMENT-HARDENING-001):
#   1. No implicit target: RemotePath/LocalPath are required explicit arguments.
#      The previous default (wp-content/mu-plugins/fyzsxnb-p0-seo-patch.php) is REMOVED.
#   2. Default mode = PREVIEW/NO WRITE. Only -Execute enables the write path.
#   3. Production write needs BOTH -Execute AND -ConfirmProductionWrite <exact remote path>.
#   4. Remote precondition: UPDATE requires -ExpectedRemoteSha256 to match current remote.
#   5. CREATE requires explicit -AllowCreate; remote absent without it = BLOCK.
#   6. Same local/remote hash = NO_CHANGE, no upload.
#   7. UPDATE backs up the current remote file first (work/deployments/backups/<ts>/).
#      Backup failure aborts before any write.
#   8. After upload the remote file is re-downloaded and verified (SHA256 + size).
#      Verification failure exits non-zero; success is only reported from observed bytes.
#   9. Secrets are never printed/logged (password used only in network credentials).
#  10. machine-readable plan (deploy-log/*-plan.json) and result (deploy-log/*-result.json)
#      are separated; status=DEPLOYED only after remote byte verification.
#
# EXIT CODES: 0 = PASS/PREVIEW/NO_CHANGE · 1 = argument validation · 2 = preflight fail
#             3 = remote precondition mismatch · 4 = backup failure · 5 = upload failure
#             6 = post-deploy verification failure
#
# TESTING: -MockRoot <dir> maps all "remote" operations onto a local directory
#          (no network). Intended for the hardening test matrix only.

[CmdletBinding()]
param(
    [Parameter(Mandatory)]
    [ValidateSet('snapshot', 'deploy', 'verify', 'rollback')]
    [string]$Action,

    [Parameter(Mandatory = $true)]
    [AllowEmptyString()]
    [string]$RemotePath,

    [string]$SourcePath = '',
    [string]$SnapshotPath = '',
    [string]$ExpectedRemoteSha256 = '',
    [switch]$AllowCreate,
    [switch]$Execute,
    [string]$ConfirmProductionWrite = '',
    [string]$MockRoot = ''
)

$ErrorActionPreference = 'Stop'

# ---------------------------------------------------------------------------
# Fail-closed helpers
# ---------------------------------------------------------------------------
function Fail-Exit {
    param([int]$Code, [string]$Message)
    try {
        [Console]::Error.WriteLine("FYZSXNB DEPLOY FAIL ($Code): $Message")
    }
    catch {
        Write-Host "FYZSXNB DEPLOY FAIL ($Code): $Message"
    }
    exit $Code
}

function Assert-SafeRemotePath([string]$Path) {
    if ([string]::IsNullOrWhiteSpace($Path)) {
        Fail-Exit 1 'RemotePath must not be empty.'
    }
    if ($Path -ne $Path.Trim()) {
        Fail-Exit 1 'RemotePath must not have leading/trailing whitespace.'
    }
    if ($Path -match '[\.]{2}' -or $Path -match '[*?]' -or $Path -match '[\x00-\x1f]') {
        Fail-Exit 1 "Unsafe RemotePath pattern: $Path"
    }
    if ($Path -match '/$' -or $Path -match '\\$') {
        Fail-Exit 1 'RemotePath must point to a file, not end with a slash.'
    }
    return $Path -replace '\\', '/' -replace '/{2,}', '/'
}

function Assert-LocalFile([string]$Path) {
    if ([string]::IsNullOrWhiteSpace($Path)) {
        Fail-Exit 1 'SourcePath is required for this action.'
    }
    if (-not (Test-Path -LiteralPath $Path -PathType Leaf)) {
        Fail-Exit 1 "Source file not found: $Path"
    }
    if ((Get-Item -LiteralPath $Path).Length -le 0) {
        Fail-Exit 1 "Source file is empty: $Path"
    }
    return (Resolve-Path -LiteralPath $Path).Path
}

function Get-FileSha256([string]$Path) {
    $hash = [System.Security.Cryptography.SHA256]::Create()
    try {
        $fs = [System.IO.File]::OpenRead($Path)
        try {
            return ([System.BitConverter]::ToString($hash.ComputeHash($fs))).Replace('-', '').ToLowerInvariant()
        }
        finally {
            $fs.Dispose()
        }
    }
    finally {
        $hash.Dispose()
    }
}

function Get-BytesSha256([byte[]]$Bytes) {
    $hash = [System.Security.Cryptography.SHA256]::Create()
    try {
        return ([System.BitConverter]::ToString($hash.ComputeHash($Bytes))).Replace('-', '').ToLowerInvariant()
    }
    finally {
        $hash.Dispose()
    }
}

function Write-JsonFile([string]$Path, [object]$Value) {
    $parent = Split-Path -Parent $Path
    if ($parent) {
        New-Item -ItemType Directory -Force -Path $parent | Out-Null
    }
    $Value | ConvertTo-Json -Depth 8 | Set-Content -LiteralPath $Path -Encoding UTF8
}

function Get-SafeMessage([string]$Message) {
    if ([string]::IsNullOrWhiteSpace($Message)) {
        return 'unknown error'
    }
    $short = $Message -replace "`r", ' ' -replace "`n", ' '
    if ($short.Length -gt 160) {
        $short = $short.Substring(0, 160)
    }
    return $short
}

# ---------------------------------------------------------------------------
# Credentials (never printed)
# ---------------------------------------------------------------------------
$ftpHost = if ($env:FYZSXNB_FTP_HOST) { $env:FYZSXNB_FTP_HOST } else { 'ftp.fyzsxnb.com' }
$ftpUser = $env:FYZSXNB_FTP_USER
$ftpPassword = $env:FYZSXNB_FTP_PASSWORD
if (-not $MockRoot -and ([string]::IsNullOrWhiteSpace($ftpUser) -or [string]::IsNullOrWhiteSpace($ftpPassword))) {
    Fail-Exit 1 'FYZSXNB FTP credentials must be supplied through the current process environment.'
}

$script:MockRoot = $MockRoot

# ---------------------------------------------------------------------------
# Transport: real FTP or local mock (only for the test matrix)
# ---------------------------------------------------------------------------
function Get-MockPath([string]$Path) {
    return (Join-Path $script:MockRoot ($Path.Trim('/') -replace '/', '\'))
}

function Get-FtpUri([string]$Path) {
    $cleanPath = $Path.TrimStart('/')
    return "ftp://$ftpHost/$cleanPath"
}

function New-FtpRequest([string]$Method, [string]$Path) {
    $request = [System.Net.FtpWebRequest]::Create((Get-FtpUri $Path))
    $request.Method = $Method
    $request.Credentials = [System.Net.NetworkCredential]::new($ftpUser, $ftpPassword)
    $request.UseBinary = $true
    $request.KeepAlive = $false
    $request.Timeout = 30000
    $request.ReadWriteTimeout = 30000
    $request.Proxy = $null
    return $request
}

function Get-FtpBytes([string]$Path) {
    if ($script:MockRoot) {
        $mp = Get-MockPath $Path
        if (-not (Test-Path -LiteralPath $mp -PathType Leaf)) {
            throw "Mock remote file not found: $Path"
        }
        return [System.IO.File]::ReadAllBytes($mp)
    }
    $request = New-FtpRequest ([System.Net.WebRequestMethods+Ftp]::DownloadFile) $Path
    $response = $request.GetResponse()
    try {
        $stream = $response.GetResponseStream()
        try {
            $memory = [System.IO.MemoryStream]::new()
            try {
                $stream.CopyTo($memory)
                return $memory.ToArray()
            }
            finally {
                $memory.Dispose()
            }
        }
        finally {
            $stream.Dispose()
        }
    }
    finally {
        $response.Dispose()
    }
}

function Set-FtpBytes([string]$Path, [byte[]]$Bytes) {
    if ($script:MockRoot) {
        $mp = Get-MockPath $Path
        New-Item -ItemType Directory -Force -Path (Split-Path -Parent $mp) | Out-Null
        [System.IO.File]::WriteAllBytes($mp, $Bytes)
        return
    }
    $request = New-FtpRequest ([System.Net.WebRequestMethods+Ftp]::UploadFile) $Path
    $stream = $request.GetRequestStream()
    try {
        $stream.Write($Bytes, 0, $Bytes.Length)
    }
    finally {
        $stream.Dispose()
    }
    $response = $request.GetResponse()
    $response.Dispose()
}

function Remove-FtpFile([string]$Path) {
    if ($script:MockRoot) {
        Remove-Item -LiteralPath (Get-MockPath $Path) -Force -ErrorAction SilentlyContinue
        return
    }
    $request = New-FtpRequest ([System.Net.WebRequestMethods+Ftp]::DeleteFile) $Path
    $response = $request.GetResponse()
    $response.Dispose()
}

function Get-FtpEntries([string]$Path) {
    if ($script:MockRoot) {
        $mp = Get-MockPath $Path
        if (-not (Test-Path -LiteralPath $mp)) {
            return @()
        }
        return @(Get-ChildItem -LiteralPath $mp | ForEach-Object { $_.Name })
    }
    $request = New-FtpRequest ([System.Net.WebRequestMethods+Ftp]::ListDirectory) $Path
    $response = $request.GetResponse()
    try {
        $reader = [System.IO.StreamReader]::new($response.GetResponseStream())
        try {
            return @($reader.ReadToEnd() -split "`r?`n" | Where-Object { $_ -and $_.Trim() })
        }
        finally {
            $reader.Dispose()
        }
    }
    finally {
        $response.Dispose()
    }
}

function New-FtpDirectory([string]$Path) {
    if ($script:MockRoot) {
        New-Item -ItemType Directory -Force -Path (Get-MockPath $Path) | Out-Null
        return
    }
    $request = New-FtpRequest ([System.Net.WebRequestMethods+Ftp]::MakeDirectory) $Path
    $response = $request.GetResponse()
    $response.Dispose()
}

function Ensure-FtpDirectory([string]$Path) {
    $segments = $Path.Trim('/').Split('/')
    $current = ''
    foreach ($segment in $segments) {
        $current = if ($current) { "$current/$segment" } else { $segment }
        try {
            [void](Get-FtpEntries $current)
        }
        catch {
            try {
                New-FtpDirectory $current
            }
            catch {
                if ($_.Exception.Message -notmatch 'already exists|exists|550') {
                    throw
                }
            }
        }
    }
}

# ---------------------------------------------------------------------------
# Remote read-only preflight (no writes)
# ---------------------------------------------------------------------------
function Test-RemoteFile([string]$Path) {
    try {
        [void](Get-FtpBytes $Path)
        return $true
    }
    catch {
        return $false
    }
}

function Get-RemoteFile([string]$Path) {
    $bytes = Get-FtpBytes $Path
    return @{
        exists = $true
        size = $bytes.Length
        sha256 = Get-BytesSha256 $bytes
    }
}

# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------
$logDir = Join-Path (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..')).Path 'deployments\deploy-log'
$stamp = Get-Date -Format 'yyyyMMdd-HHmmss'

function Write-DeployPlan([hashtable]$Plan) {
    $safe = $Plan.Clone()
    Write-JsonFile (Join-Path $logDir "$stamp-plan.json") $safe
    $safe | ConvertTo-Json -Depth 8
}

function Write-DeployResult([hashtable]$Result) {
    $safe = $Result.Clone()
    if ($safe.ContainsKey('credential')) { $safe.Remove('credential') }
    Write-JsonFile (Join-Path $logDir "$stamp-result.json") $safe
    $safe | ConvertTo-Json -Depth 8
}

# ===========================================================================
# Actions
# ===========================================================================
$RemotePath = Assert-SafeRemotePath $RemotePath
$remoteDirectory = Split-Path -Path $RemotePath -Parent
$remoteDirectory = ($remoteDirectory -replace '\\', '/').Trim('/')
if ([string]::IsNullOrWhiteSpace($remoteDirectory)) {
    Fail-Exit 1 'RemotePath has no parent directory.'
}

switch ($Action) {
    # ------------------------------------------------------------------
    'snapshot' {
        if ([string]::IsNullOrWhiteSpace($SnapshotPath)) {
            Fail-Exit 1 'SnapshotPath is required.'
        }
        $snapshot = [ordered]@{
            generated_at = (Get-Date).ToUniversalTime().ToString('o')
            remote_host = if ($script:MockRoot) { 'MOCK' } else { $ftpHost }
            remote_path = $RemotePath
            directory = $remoteDirectory
            directory_exists = $false
            directory_entries = @()
            target_exists = $false
            target_size = $null
            target_sha256 = $null
            backup_path = $null
        }
        try {
            $snapshot.directory_entries = @(Get-FtpEntries $remoteDirectory)
            $snapshot.directory_exists = $true
        }
        catch {
            $snapshot.directory_exists = $false
        }
        if ($snapshot.directory_exists) {
            try {
                $bytes = Get-FtpBytes $RemotePath
                $snapshot.target_exists = $true
                $snapshot.target_size = $bytes.Length
                $snapshot.target_sha256 = Get-BytesSha256 $bytes
                $backupPath = Join-Path (Split-Path -Parent $SnapshotPath) 'before-fyzsxnb-p0-seo-patch.php'
                [System.IO.File]::WriteAllBytes($backupPath, $bytes)
                $snapshot.backup_path = $backupPath
            }
            catch {
                $snapshot.target_exists = $false
            }
        }
        Write-JsonFile $SnapshotPath $snapshot
        $snapshot | ConvertTo-Json -Depth 8
    }
    # ------------------------------------------------------------------
    'deploy' {
        $localPath = Assert-LocalFile $SourcePath
        $localSha = Get-FileSha256 $localPath
        $localBytes = [System.IO.File]::ReadAllBytes($localPath)
        $localSize = $localBytes.Length

        # preflight (read-only)
        $remoteInfo = if (Test-RemoteFile $RemotePath) { Get-RemoteFile $RemotePath } else { @{ exists = $false; size = $null; sha256 = $null } }

        $operation = ''
        if (-not $remoteInfo.exists) {
            $operation = 'CREATE'
            if (-not $AllowCreate) {
                $plan = @{ timestamp = (Get-Date).ToUniversalTime().ToString('o'); local_path = $localPath; remote_path = $RemotePath;
                          operation = 'CREATE_BLOCKED'; status = 'BLOCKED_NO_ALLOW_CREATE'; local_sha256 = $localSha;
                          remote_exists_before = $false; execute_requested = $Execute.IsPresent; confirmation_received = ($ConfirmProductionWrite -eq $RemotePath) }
                Write-DeployPlan $plan
                Fail-Exit 3 'Remote file absent; -AllowCreate required for create.'
            }
        }
        elseif ($remoteInfo.sha256 -eq $localSha) {
            $plan = @{ timestamp = (Get-Date).ToUniversalTime().ToString('o'); local_path = $localPath; remote_path = $RemotePath;
                      operation = 'NO_CHANGE'; status = 'PREVIEW'; local_sha256 = $localSha; remote_sha256_before = $remoteInfo.sha256;
                      execute_requested = $Execute.IsPresent; confirmation_received = ($ConfirmProductionWrite -eq $RemotePath) }
            Write-DeployPlan $plan
            [ordered]@{ status = 'NO_CHANGE'; local_sha256 = $localSha; remote_sha256 = $remoteInfo.sha256; note = 'local == remote; no upload performed' } | ConvertTo-Json -Depth 8
            exit 0
        }
        else {
            $operation = 'UPDATE'
            if ([string]::IsNullOrWhiteSpace($ExpectedRemoteSha256)) {
                Fail-Exit 3 'Update requires -ExpectedRemoteSha256 (current remote SHA256).'
            }
            if ($ExpectedRemoteSha256.ToLowerInvariant() -ne $remoteInfo.sha256.ToLowerInvariant()) {
                $plan = @{ timestamp = (Get-Date).ToUniversalTime().ToString('o'); local_path = $localPath; remote_path = $RemotePath;
                          operation = 'UPDATE_BLOCKED'; status = 'BLOCKED_REMOTE_PRECONDITION'; local_sha256 = $localSha;
                          expected_remote_sha256 = $ExpectedRemoteSha256; remote_sha256_before = $remoteInfo.sha256;
                          execute_requested = $Execute.IsPresent; confirmation_received = ($ConfirmProductionWrite -eq $RemotePath) }
                Write-DeployPlan $plan
                Fail-Exit 3 "Remote precondition mismatch. expected=$ExpectedRemoteSha256 actual=$($remoteInfo.sha256)"
            }
        }

        $plan = @{
            timestamp = (Get-Date).ToUniversalTime().ToString('o')
            local_path = $localPath
            remote_path = $RemotePath
            operation = $operation
            local_size = $localSize
            local_sha256 = $localSha
            remote_exists_before = $remoteInfo.exists
            remote_size_before = $remoteInfo.size
            remote_sha256_before = $remoteInfo.sha256
            expected_remote_sha256 = if ($operation -eq 'UPDATE') { $ExpectedRemoteSha256 } else { $null }
            execute_requested = $Execute.IsPresent
            confirmation_received = ($ConfirmProductionWrite -eq $RemotePath)
        }

        if (-not $Execute) {
            Write-Host ''
            Write-Host 'DRY RUN / PREVIEW ONLY'
            Write-Host 'NO PRODUCTION WRITE OCCURRED'
            Write-Host ''
            Write-Host 'DEPLOYMENT PLAN'
            Write-Host "Local Path: $localPath"
            Write-Host "Remote Path: $RemotePath"
            Write-Host "Local Size: $localSize"
            Write-Host "Local SHA256: $localSha"
            Write-Host "Remote Exists: $($remoteInfo.exists)"
            Write-Host "Remote Size: $($remoteInfo.size)"
            Write-Host "Remote Current SHA256: $($remoteInfo.sha256)"
            Write-Host "Operation: $operation"
            Write-Host 'Execution Mode: PREVIEW'
            $plan.status = 'PREVIEW'
            [ordered]@{ status = 'PREVIEW'; operation = $operation; local_sha256 = $localSha; remote_sha256_before = $remoteInfo.sha256 } | ConvertTo-Json -Depth 8
            Write-DeployPlan $plan
            exit 0
        }

        if ($ConfirmProductionWrite -ne $RemotePath) {
            $plan.status = 'BLOCKED_CONFIRMATION'
            Write-DeployPlan $plan
            Fail-Exit 2 'Production write requires -ConfirmProductionWrite <exact remote path>.'
        }

        Write-Host ''
        Write-Host 'YOU ARE ABOUT TO WRITE PRODUCTION'
        Write-Host "Local: $localPath"
        Write-Host "Remote: $RemotePath"
        Write-Host "Current Remote SHA: $($remoteInfo.sha256)"
        Write-Host "New Local SHA: $localSha"
        Write-Host 'Backup: will download current remote first'
        Write-Host ''

        # backup before write (existing file only)
        $backupPath = $null
        if ($remoteInfo.exists) {
            $backupDir = Join-Path (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..')).Path "deployments\backups\$stamp"
            New-Item -ItemType Directory -Force -Path $backupDir | Out-Null
            $backupPath = Join-Path $backupDir (Split-Path -Leaf $RemotePath)
            try {
                if ($script:MockRoot -and $env:FYZSXNB_MOCK_BACKUP_FAIL -eq '1') {
                    throw 'Mock backup failure (test hook)'
                }
                $remoteBytes = Get-FtpBytes $RemotePath
                [System.IO.File]::WriteAllBytes($backupPath, $remoteBytes)
            }
            catch {
                Fail-Exit 4 "Backup failed before upload; deployment aborted. $(Get-SafeMessage $_.Exception.Message)"
            }
        }

        # upload
        try {
            Ensure-FtpDirectory $remoteDirectory
            Set-FtpBytes $RemotePath $localBytes
        }
        catch {
            Fail-Exit 5 "Upload failed. $(Get-SafeMessage $_.Exception.Message)"
        }

        # post-deploy verification: re-download + SHA256 + size
        try {
            $afterBytes = Get-FtpBytes $RemotePath
            if ($script:MockRoot -and $env:FYZSXNB_MOCK_VERIFY_FAIL -eq '1') {
                $afterBytes = $afterBytes[0..([Math]::Max(0, $afterBytes.Length - 2))]
            }
            $afterSha = Get-BytesSha256 $afterBytes
        }
        catch {
            Fail-Exit 6 "Post-deploy re-download failed. $(Get-SafeMessage $_.Exception.Message)"
        }
        if ($afterSha -ne $localSha -or $afterBytes.Length -ne $localSize) {
            Fail-Exit 6 "Post-deploy verification failed. expected_sha=$localSha actual_sha=$afterSha expected_size=$localSize actual_size=$($afterBytes.Length)"
        }

        $result = @{
            timestamp = (Get-Date).ToUniversalTime().ToString('o')
            status = 'DEPLOYED'
            local_path = $localPath
            remote_path = $RemotePath
            operation = $operation
            local_size = $localSize
            local_sha256 = $localSha
            remote_exists_before = $remoteInfo.exists
            remote_sha256_before = $remoteInfo.sha256
            expected_remote_sha256 = if ($operation -eq 'UPDATE') { $ExpectedRemoteSha256 } else { $null }
            execute_requested = $true
            confirmation_received = $true
            backup_path = $backupPath
            remote_size_after = $afterBytes.Length
            remote_sha256_after = $afterSha
            verification = 'PASS'
        }
        Write-DeployResult $result
    }
    # ------------------------------------------------------------------
    'verify' {
        $info = if (Test-RemoteFile $RemotePath) { Get-RemoteFile $RemotePath } else { @{ exists = $false; size = $null; sha256 = $null } }
        if (-not $info.exists) {
            [ordered]@{ exists = $false; remote_path = $RemotePath } | ConvertTo-Json -Depth 8
            exit 1
        }
        [ordered]@{
            exists = $true
            remote_path = $RemotePath
            size = $info.size
            sha256 = $info.sha256
            verified_at = (Get-Date).ToUniversalTime().ToString('o')
        } | ConvertTo-Json -Depth 8
    }
    # ------------------------------------------------------------------
    'rollback' {
        if ([string]::IsNullOrWhiteSpace($SnapshotPath)) {
            Fail-Exit 1 'SnapshotPath is required.'
        }
        if (-not $Execute) {
            Fail-Exit 2 'Rollback is a production write: -Execute required.'
        }
        if ($ConfirmProductionWrite -ne $RemotePath) {
            Fail-Exit 2 'Rollback requires -ConfirmProductionWrite <exact remote path>.'
        }
        $snapshot = Get-Content -Raw -LiteralPath $SnapshotPath | ConvertFrom-Json
        if ($snapshot.remote_path -ne $RemotePath) {
            Fail-Exit 1 'Snapshot remote path does not match rollback target.'
        }
        if ($snapshot.target_exists -and $snapshot.backup_path -and (Test-Path -LiteralPath $snapshot.backup_path -PathType Leaf)) {
            $bytes = [System.IO.File]::ReadAllBytes((Resolve-Path -LiteralPath $snapshot.backup_path).Path)
            Ensure-FtpDirectory $remoteDirectory
            Set-FtpBytes $RemotePath $bytes
            $remoteBytes = Get-FtpBytes $RemotePath
            if ((Get-BytesSha256 $remoteBytes) -ne $snapshot.target_sha256) {
                Fail-Exit 6 'Rollback hash mismatch.'
            }
            [ordered]@{ rolled_back = $true; action = 'restored_existing_file'; sha256 = (Get-BytesSha256 $remoteBytes) } | ConvertTo-Json -Depth 8
        }
        else {
            try {
                Remove-FtpFile $RemotePath
            }
            catch {
                if ($_.Exception.Message -notmatch '550|not found|does not exist') { throw }
            }
            [ordered]@{ rolled_back = $true; action = 'removed_new_file' } | ConvertTo-Json -Depth 8
        }
    }
}