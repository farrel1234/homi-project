param(
    [string]$BindHost = "0.0.0.0",
    [int]$Port = 8000,
    [switch]$WithQueue = $false,
    [switch]$DryRun = $false
)

$ErrorActionPreference = "Stop"

$projectRoot = $PSScriptRoot
$backendPath = Join-Path $projectRoot "backup-backend-homi\backend-homi"

if (!(Test-Path $backendPath)) {
    throw "Backend path tidak ditemukan: $backendPath"
}

function Get-PhpPath {
    $preferred = "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe"
    if (Test-Path $preferred) {
        return $preferred
    }

    $all = Get-ChildItem "C:\laragon\bin\php" -Directory -ErrorAction SilentlyContinue |
        Sort-Object Name -Descending |
        ForEach-Object {
            Join-Path $_.FullName "php.exe"
        } |
        Where-Object { Test-Path $_ }

    if ($all.Count -gt 0) {
        return $all[0]
    }

    return $null
}

$phpPath = Get-PhpPath
if (!$phpPath) {
    throw "php.exe tidak ditemukan di C:\laragon\bin\php"
}

$npmCmd = Get-Command npm.cmd -ErrorAction SilentlyContinue
if (!$npmCmd) {
    throw "npm.cmd tidak ditemukan di PATH."
}

$mysqlRunning = (Get-Process mysqld -ErrorAction SilentlyContinue | Measure-Object).Count -gt 0
if (!$mysqlRunning) {
    Write-Warning "mysqld belum jalan. Jalankan MySQL dulu agar login API tidak gagal."
}

$artisanCmd = "Set-Location -LiteralPath '$backendPath'; & '$phpPath' artisan serve --host=$BindHost --port=$Port"
$viteCmd = "Set-Location -LiteralPath '$backendPath'; npm.cmd run dev"
$queueCmd = "Set-Location -LiteralPath '$backendPath'; & '$phpPath' artisan queue:listen --tries=1"

Write-Host "Project root : $projectRoot"
Write-Host "Backend path : $backendPath"
Write-Host "PHP         : $phpPath"
Write-Host "MySQL alive : $mysqlRunning"
Write-Host "API URL     : http://localhost:$Port"

if ($DryRun) {
    Write-Host ""
    Write-Host "[DRY RUN] Command API  : $artisanCmd"
    Write-Host "[DRY RUN] Command Vite : $viteCmd"
    if ($WithQueue) {
        Write-Host "[DRY RUN] Command Queue: $queueCmd"
    }
    exit 0
}

Start-Process powershell -ArgumentList @(
    "-NoExit",
    "-Command",
    $artisanCmd
)

Start-Process powershell -ArgumentList @(
    "-NoExit",
    "-Command",
    $viteCmd
)

if ($WithQueue) {
    Start-Process powershell -ArgumentList @(
        "-NoExit",
        "-Command",
        $queueCmd
    )
}

Write-Host ""
Write-Host "Dev server dijalankan di terminal terpisah."
Write-Host "Tutup masing-masing terminal untuk stop."
