<#
 .SYNOPSIS
   Automated PHP 8.3 download & setup (portable) for this project.

 .USAGE
   pwsh -ExecutionPolicy Bypass -File scripts/setup-php.ps1

 .NOTES
   Adds local php to project under .php-bin and writes .env.local-path hint.
   Does NOT modify global PATH permanently unless -AddToUserPath switch used.
#>
[CmdletBinding()]
param(
  [string]$PhpVersion = '8.3.25',
  [ValidateSet('nts','ts')][string]$Threading = 'nts',
  [switch]$AddToUserPath
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path | Split-Path -Parent
$binDir = Join-Path $projectRoot '.php-bin'
if (!(Test-Path $binDir)) { New-Item -ItemType Directory -Path $binDir | Out-Null }

$arch = 'x64'
$zipName = "php-$PhpVersion-Win32-vs16-$arch.zip"
if ($Threading -eq 'nts') { $zipName = "php-$PhpVersion-nts-Win32-vs16-$arch.zip" }
$downloadUrl = "https://windows.php.net/downloads/releases/$zipName"
$zipPath = Join-Path $binDir $zipName

Write-Host "Downloading $downloadUrl ..." -ForegroundColor Cyan
try {
  Invoke-WebRequest -Uri $downloadUrl -OutFile $zipPath -ErrorAction Stop
} catch {
  Write-Warning "Download gagal ($downloadUrl). Coba ambil index untuk versi tersedia."
  $index = Invoke-WebRequest -Uri 'https://windows.php.net/downloads/releases/' -UseBasicParsing
  $match = ($index.Content | Select-String -Pattern 'php-8\.3\.[0-9]+-nts-Win32-vs16-x64\.zip' -AllMatches).Matches | Select-Object -First 1
  if ($match) {
    $alt = $match.Value
    Write-Host "Menemukan alternatif: $alt" -ForegroundColor Yellow
    $downloadUrl = "https://windows.php.net/downloads/releases/$alt"
    Invoke-WebRequest -Uri $downloadUrl -OutFile $zipPath -ErrorAction Stop
  } else {
    throw 'Tidak menemukan file PHP 8.3 NTS di index.'
  }
}

Write-Host 'Extracting...' -ForegroundColor Cyan
Expand-Archive -Path $zipPath -DestinationPath $binDir -Force

# Pada paket rilis terbaru struktur file langsung di root zip (tidak nested folder)
$phpDir = $binDir
if (!(Test-Path (Join-Path $phpDir 'php.exe'))) { throw 'php.exe tidak ditemukan setelah ekstraksi.' }
Copy-Item (Join-Path $phpDir 'php.ini-production') (Join-Path $phpDir 'php.ini') -Force

# Enable required extensions
$iniPath = Join-Path $phpDir 'php.ini'
$ini = Get-Content $iniPath
$enable = @(
  'extension_dir = "ext"',
  'extension=openssl',
  'extension=curl',
  'extension=pdo_pgsql',
  'extension=pgsql',
  'extension=fileinfo',
  'extension=mbstring'
)
foreach ($e in $enable) {
  if (-not ($ini -match [regex]::Escape($e))) {
    $ini += $e
  }
}
$ini | Set-Content $iniPath -Encoding UTF8

# Write helper env file
$envHint = @("PHP_BINARY=$phpDir\\php.exe")
$envFile = Join-Path $projectRoot '.php-local'
$envHint | Set-Content $envFile -Encoding UTF8

Write-Host "Local PHP ready: $($phpDir)" -ForegroundColor Green
& "$phpDir\php.exe" -v

if ($AddToUserPath) {
  Write-Host 'Adding to user PATH...' -ForegroundColor Yellow
  $current = [Environment]::GetEnvironmentVariable('Path','User')
  if ($current -notlike "*$phpDir*") {
    [Environment]::SetEnvironmentVariable('Path', ($current + ';' + $phpDir).Trim(';'), 'User')
    Write-Host 'Reopen terminal to use php globally.' -ForegroundColor Yellow
  }
}

Write-Host 'To use artisan now (without reopening terminal):' -ForegroundColor Cyan
Write-Host "`n  & \"$phpDir\\php.exe\" artisan panen:diag" -ForegroundColor White
