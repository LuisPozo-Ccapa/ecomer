<#
.SYNOPSIS
    Prepara el paquete de despliegue optimizado para cPanel (cuota 200 MB).

.DESCRIPTION
    1. Instala dependencias PHP en modo PRODUCCION (--no-dev, ahorra ~17 MB)
    2. Compila el frontend (vite build)
    3. Genera ecomer-deploy.zip SIN: .git, node_modules, tests, scratch,
       composer-setup.php, .env, docs, sesiones/cache/log, ni las imagenes
       ya subidas (storage/app/public) -> eso ya vive en el servidor.
    4. Imprime el peso final frente a la cuota.

.EXAMPLE
    .\deploy\build-package.ps1
#>
[CmdletBinding()]
param(
    [int]$CuotaMB = 200,
    [switch]$OmitirBuild   # si el build de vite ya se hizo y no quieres repetirlo
)

$ErrorActionPreference = 'Stop'
$raiz = Split-Path $PSScriptRoot -Parent
Set-Location $raiz

# PS 5.1 convierte la salida a stderr de composer/npm en error fatal con 'Stop'.
# Se usa 'Continue' y se comprueban los codigos de salida manualmente.
$ErrorActionPreference = 'Continue'

Write-Host "`n=== 1/4 composer (produccion) ===" -ForegroundColor Cyan
$php = 'C:\laragon\bin\php\php-8.4.25-Win32-vs17-x64\php.exe'
if (-not (Test-Path $php)) { throw "No se encontro PHP 8.4 en $php" }
$env:PATH = (Split-Path $php) + ';' + $env:PATH

# Resuelve composer: el script sin extension (se ejecuta con el PHP que le pasemos)
$composer = Join-Path $env:LOCALAPPDATA 'ComposerSetup\composer'
if (-not (Test-Path $composer)) {
    $composer = (Get-Command composer -ErrorAction SilentlyContinue).Source
}
if (-not $composer) { throw "No se encontro composer" }

& $php $composer install `
    --no-dev --optimize-autoloader --no-interaction --no-progress 2>&1 | Out-Host
if ($LASTEXITCODE -ne 0) { throw "composer install fallo" }

if (-not $OmitirBuild) {
    Write-Host "`n=== 2/4 frontend (vite build) ===" -ForegroundColor Cyan
    cmd /c "npm run build 2>&1" | Out-Host
    if ($LASTEXITCODE -ne 0) { throw "npm run build fallo" }
} else {
    Write-Host "`n=== 2/4 frontend: OMITIDO ===" -ForegroundColor Yellow
}

Write-Host "`n=== 3/4 empaquetando ===" -ForegroundColor Cyan
$zip = Join-Path $raiz 'ecomer-deploy.zip'
if (Test-Path $zip) { Remove-Item $zip -Force }

# Copia a un staging temporal y borra lo que no debe salir
$stage = Join-Path $env:TEMP ('ecomer-stage-' + (Get-Date -Format 'yyyyMMddHHmmss'))
robocopy $raiz $stage /MIR /NFL /NDL /NJH /NJS /NP `
    /XD node_modules .git scratch tests vendor\phpunit vendor\fakerphp vendor\mockery `
    /XF .env .env.backup .env.production composer-setup.php package-lock.json `
        DOCUMENTACION_ARQUITECTURA.md '*.zip' | Out-Null

# storage: conserva estructura y .gitkeep, NO las imagenes (ya estan en el servidor)
Get-ChildItem (Join-Path $stage 'storage\app\public') -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.Name -ne '.gitkeep' } | Remove-Item -Force
Get-ChildItem (Join-Path $stage 'storage\framework') -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.Name -notin @('.gitkeep') } | Remove-Item -Force
Remove-Item (Join-Path $stage 'storage\logs\*.log') -Force -ErrorAction SilentlyContinue
Remove-Item (Join-Path $stage 'storage\app\livewire-tmp\*') -Force -Recurse -ErrorAction SilentlyContinue

Compress-Archive -Path (Join-Path $stage '*') -DestinationPath $zip -CompressionLevel Optimal
Remove-Item $stage -Recurse -Force

Write-Host "`n=== 4/4 resultado ===" -ForegroundColor Cyan
$mb = [math]::Round((Get-Item $zip).Length / 1MB, 2)
$libre = $CuotaMB - $mb
$pct   = [math]::Round(($mb / $CuotaMB) * 100, 1)
Write-Host ("  paquete      : {0,8:N2} MB" -f $mb)
Write-Host ("  de cuota     : {0,8:N2} MB / {1} MB  ({2}%)" -f $mb, $CuotaMB, $pct)
Write-Host ("  libre        : {0,8:N2} MB" -f $libre)
if ($libre -le 0) { Write-Host "  > EXCEDE la cuota" -ForegroundColor Red }
elseif ($libre -lt 30) { Write-Host "  > MARGEN BAJO (<30 MB)" -ForegroundColor Yellow }
else { Write-Host "  > margen suficiente" -ForegroundColor Green }
Write-Host "`n  Sube $zip por el Administrador de Archivos de cPanel y descomprimelo"
Write-Host "  en public_html (NO subas .env; ya existe en el servidor).`n" -ForegroundColor DarkGray
