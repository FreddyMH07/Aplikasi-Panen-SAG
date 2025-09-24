<#
 Force enable required PHP extensions (openssl, curl, mbstring, pdo_pgsql, pgsql, fileinfo).
 Adjust extension_dir to absolute path for portable install.
#>
Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $MyInvocation.MyCommand.Path | Split-Path -Parent
$phpDir = Join-Path $root '.php-bin'
$ini = Join-Path $phpDir 'php.ini'
if (!(Test-Path $ini)) { throw 'php.ini not found. Run setup-php.ps1 first.' }

$content = Get-Content $ini

# Remove previous override block if any
$content = $content | Where-Object { $_ -notmatch '#__EXT_OVERRIDE_START' -and $_ -notmatch '#__EXT_OVERRIDE_END' }

$absExt = ($phpDir + '\\ext').Replace('\\','\\\\')
$override = @(
  '#__EXT_OVERRIDE_START',
  ('extension_dir="' + $absExt + '"'),
  'extension=openssl',
  'extension=curl',
  'extension=mbstring',
  'extension=fileinfo',
  'extension=pdo_pgsql',
  'extension=pgsql',
  '#__EXT_OVERRIDE_END'
)
$content += ''
$content += $override
$content | Set-Content $ini -Encoding UTF8

Write-Host 'Updated php.ini override block.' -ForegroundColor Green
& (Join-Path $phpDir 'php.exe') -m | Select-String -Pattern 'openssl|curl|pgsql|mbstring|fileinfo'
