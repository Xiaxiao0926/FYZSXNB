# run_ftp_deploy_secure.ps1 — secure entry wrapper for ftp_p0_deployer.ps1
# (HARDENED, fail-closed — see ftp_p0_deployer.ps1 header for the contract).
#
# No implicit target: RemotePath is mandatory and has NO default value.
# All hardening controls are passed through transparently; this wrapper must
# never re-introduce a default target.

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
    [string]$MockRoot = '',

    [string]$CredentialPath = "$env:APPDATA\FYZSXNB\ftp-credential.clixml"
)

$ErrorActionPreference = 'Stop'

if ([string]::IsNullOrWhiteSpace($RemotePath)) {
    throw 'RemotePath is required and must not be empty (no implicit target).'
}

if (-not $MockRoot -and -not (Test-Path -LiteralPath $CredentialPath -PathType Leaf)) {
    throw 'Encrypted FTP credential is missing. Run site-ops/ftp_credential_gui.ps1 first.'
}

$credential = $null
$password = $null
if (-not $MockRoot) {
    $credential = Import-Clixml -LiteralPath $CredentialPath
    $userKey = [string]$credential.UserName   # format: "<host>|<username>"
    $password = $credential.GetNetworkCredential().Password
    if ([string]::IsNullOrWhiteSpace($userKey) -or -not $userKey.Contains('|') -or [string]::IsNullOrWhiteSpace($password)) {
        throw 'Encrypted FTP credential is invalid.'
    }
    $pipe = $userKey.IndexOf('|')
    $ftpHost = $userKey.Substring(0, $pipe)
    $ftpUser = $userKey.Substring($pipe + 1)
    $ftpHost = ($ftpHost -replace '^\s*ftp://', '').Trim().TrimEnd('/')

    if ([string]::IsNullOrWhiteSpace($ftpHost)) {
        throw 'Encrypted FTP credential contains an invalid host.'
    }
}

try {
    if (-not $MockRoot) {
        $env:FYZSXNB_FTP_HOST = $ftpHost
        $env:FYZSXNB_FTP_USER = $ftpUser
        $env:FYZSXNB_FTP_PASSWORD = $password
    }

    & (Join-Path $PSScriptRoot 'ftp_p0_deployer.ps1') -Action $Action `
        -SourcePath $SourcePath -SnapshotPath $SnapshotPath -RemotePath $RemotePath `
        -ExpectedRemoteSha256 $ExpectedRemoteSha256 `
        -AllowCreate:$AllowCreate -Execute:$Execute -ConfirmProductionWrite $ConfirmProductionWrite `
        -MockRoot $MockRoot
    exit $LASTEXITCODE
}
finally {
    Remove-Item Env:FYZSXNB_FTP_HOST -ErrorAction SilentlyContinue
    Remove-Item Env:FYZSXNB_FTP_USER -ErrorAction SilentlyContinue
    Remove-Item Env:FYZSXNB_FTP_PASSWORD -ErrorAction SilentlyContinue
    $password = $null
    $credential = $null
}