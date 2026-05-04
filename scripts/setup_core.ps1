<# Windows cross-platform core setup script #>
<# This script performs a minimal installer flow: composer install, autoload check, then migrations/seed if available #>
$ErrorActionPreference = "Stop"

$coreDir = Join-Path -Path (Split-Path -Parent $MyInvocation.MyCommand.Path) -ChildPath "..\core"
Write-Host "[setup_core.ps1] Core directory: $coreDir"

if (!(Test-Path $coreDir)) {
  Write-Error "Core directory not found: $coreDir"; exit 1
}

Set-Location $coreDir

if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
  Write-Error "PHP CLI not found in PATH. Please install PHP and ensure 'php' is accessible."; exit 1
}

Write-Host "[setup_core.ps1] Checking for composer..."
if (Test-Path .\composer.phar) {
  $composerCmd = "php .\composer.phar"
} elseif (Get-Command composer -ErrorAction SilentlyContinue) {
  $composerCmd = "composer"
} else {
  Write-Host "Composer not found. Attempting to download composer.phar..."
  try {
    Invoke-WebRequest -Uri "https://getcomposer.org/composer.phar" -OutFile ".\composer.phar" -UseBasicParsing
    $composerCmd = "php .\composer.phar"
  } catch {
    Write-Error "Unable to fetch composer.phar. Install Composer or ensure network access."; exit 1
  }
}

Write-Host "[setup_core.ps1] Running composer install..."
try {
  & $composerCmd install --no-interaction
} catch {
  Write-Host "Composer install failed. Retrying with TLS disabled..."
  & composer config --global tls.disable true
  & $composerCmd install --no-interaction
}

$autoloadPath = ".\vendor\autoload.php"
if (!(Test-Path $autoloadPath)) {
  Write-Error "Autoload not found at $autoloadPath. Composer install may have failed."; exit 1
}
Write-Host "[setup_core.ps1] Autoload found: $autoloadPath"

if (Test-Path .\artisan) {
  Write-Host "[setup_core.ps1] Running migrations..."
  try { & php .\artisan migrate --force } catch { Write-Warning "Migration failed" }
  Write-Host "[setup_core.ps1] Seeding..."
  try { & php .\artisan db:seed --class=UpiConfigSeeder } catch { Write-Warning "Seeder not present or failed" }
} else {
  Write-Host "[setup_core.ps1] Artisan not found; skipping migrations/seed."
}

Write-Host "[setup_core.ps1] Core setup complete."
