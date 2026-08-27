$CredentialPath = "$env:APPDATA\FYZSXNB\ftp-credential.clixml"
$credential = Import-Clixml -LiteralPath $CredentialPath
$userKey = [string]$credential.UserName
$password = $credential.GetNetworkCredential().Password
$pipe = $userKey.IndexOf('|')
$ftpHost = $userKey.Substring(0, $pipe) -replace '^\s*(ftp|https?)://', '' -replace '/.*$', ''
$ftpUser = $userKey.Substring($pipe + 1)

$wpCred = Import-Clixml -LiteralPath "$env:APPDATA\FYZSXNB\wp-rest-credential.clixml"
$wpPassword = $wpCred.GetNetworkCredential().Password

$env:FYZSXNB_FTP_HOST = $ftpHost
$env:FYZSXNB_FTP_USER = $ftpUser
$env:FYZSXNB_FTP_PASSWORD = $password
$env:WP_USER = $wpCred.UserName
$env:WP_APP_PASSWORD = $wpPassword

py "C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\site-ops\deploy_home_inc.py"

exit $LASTEXITCODE

