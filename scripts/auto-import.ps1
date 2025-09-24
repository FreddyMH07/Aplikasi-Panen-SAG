<#
 .SYNOPSIS
   Install composer (local), install dependencies, jalankan bulk import & diagnostik.

 .USAGE
   pwsh -ExecutionPolicy Bypass -File scripts/auto-import.ps1

 .NOTES
   Memakai php portable di .php-bin. Pastikan scripts/setup-php.ps1 sudah dijalankan.
#>
Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path | Split-Path -Parent
Set-Location $projectRoot

$php = Join-Path $projectRoot '.php-bin/php.exe'
if (!(Test-Path $php)) { throw 'php portable belum ditemukan. Jalankan scripts/setup-php.ps1 dulu.' }

if (!(Test-Path 'composer.phar')) {
  Write-Host 'Mengunduh composer.phar ...' -ForegroundColor Cyan
  Invoke-WebRequest https://getcomposer.org/download/latest-stable/composer.phar -OutFile composer.phar
}

if (!(Test-Path 'vendor')) {
  Write-Host 'Menjalankan composer install ...' -ForegroundColor Cyan
  & $php composer.phar install --no-interaction --prefer-dist
} else {
  Write-Host 'Folder vendor sudah ada, skip composer install.' -ForegroundColor Yellow
}

# Deteksi file CSV
$master = Get-ChildItem -File -Filter '*Master*Data*Panen*.csv' | Select-Object -First 1
$harian = Get-ChildItem -File -Filter '*Panen Harian*.csv' | Select-Object -First 1

if (-not $master -and -not $harian) { Write-Warning 'Tidak menemukan file CSV master atau harian di root.' }

$importArgs = @()
if ($master) { $importArgs += "--master=`"$($master.Name)`"" }
if ($harian) { $importArgs += "--harian=`"$($harian.Name)`"" }

if ($importArgs.Count -gt 0) {
  Write-Host "Menjalankan bulk import: $($importArgs -join ' ')" -ForegroundColor Green
  & $php artisan panen:bulk-import @importArgs
} else {
  Write-Host 'Skip bulk import (tidak ada file).' -ForegroundColor Yellow
}

Write-Host 'Diagnostik:' -ForegroundColor Cyan
& $php artisan panen:diag --limit=2

Write-Host 'Selesai.' -ForegroundColor Green
