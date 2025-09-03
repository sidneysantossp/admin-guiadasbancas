param(
    [switch]$IncludeVendor
)

# Preparação de pacote para upload em hospedagem cPanel
# Uso:
#   1) Abra o PowerShell nesta pasta (servidor-web)
#   2) Execute:  .\prepare_for_cpanel.ps1             # copia o projeto sem vendor/
#      ou        .\prepare_for_cpanel.ps1 -IncludeVendor  # inclui vendor/ (se já existir localmente)
# Resultado: cria a pasta .\dist com os arquivos prontos para upload

$ErrorActionPreference = 'Stop'

$projectRoot = Resolve-Path (Join-Path $PSScriptRoot '..')
$distDir     = Join-Path $PSScriptRoot 'dist'

Write-Host "Projeto:" $projectRoot
Write-Host "Saída:  " $distDir

if (Test-Path $distDir) {
    Write-Host "Limpando dist..."
    Remove-Item -Recurse -Force $distDir
}
New-Item -ItemType Directory -Path $distDir | Out-Null

# Padrões para excluir do pacote
$excludes = @(
    '.git', '.gitignore', '.gitattributes', '.github', '.idea', '.vscode',
    'node_modules', 'tests', 'storage\\framework\\testing', 'storage\\framework\\cache\\data',
    'storage\\logs\\*.log', 'storage\\debugbar',
    'installation', 'Modules\\chek',
    '*.zip', '*.rar', '*.7z', '*.tar', '*.tar.gz',
    '.env*', 'php.ini', '.DS_Store',
    'Admin panel new install V8.3.zip'
)

function Should-Exclude($path) {
    foreach ($pattern in $excludes) {
        if ([System.Management.Automation.WildcardPattern]::new($pattern, 'IgnoreCase').IsMatch($path)) {
            return $true
        }
    }
    return $false
}

Write-Host "Copiando arquivos..."
Get-ChildItem -Recurse -Force -Path $projectRoot | ForEach-Object {
    if ($_.PSIsContainer) { return }
    $rel = Resolve-Path $_.FullName -Relative -ErrorAction SilentlyContinue
    if (-not $rel) { return }
    # Normalizar separadores para comparação
    $relNorm = $rel -replace '/', '\\'
    if (Should-Exclude $relNorm) { return }

    $target = Join-Path $distDir $rel
    $targetDir = Split-Path $target -Parent
    if (-not (Test-Path $targetDir)) { New-Item -ItemType Directory -Path $targetDir -Force | Out-Null }
    Copy-Item $_.FullName $target -Force
}

# Opcional: incluir vendor/ existente
if ($IncludeVendor) {
    $vendorSrc = Join-Path $projectRoot 'vendor'
    if (Test-Path $vendorSrc) {
        Write-Host "Incluindo vendor/..."
        Copy-Item -Recurse -Force $vendorSrc (Join-Path $distDir 'vendor')
    } else {
        Write-Warning "vendor/ não encontrado na máquina local. Rode 'composer install --no-dev' antes, ou faça o install no servidor."
    }
}

# Dicas finais
Write-Host "\nPacote preparado em: $distDir"
Write-Host "\nPróximos passos:" 
Write-Host "  1) Faça upload do conteúdo de dist/ para o servidor (exceto .env)."
Write-Host "  2) No servidor, crie/edite o arquivo .env com suas variáveis (APP_KEY, DB_*, APP_URL, etc.)."
Write-Host "  3) Ajuste o Document Root para a pasta public/ (ou mova o conteúdo de public/ para public_html e ajuste caminhos no index.php)."
Write-Host "  4) Se tiver SSH/Composer: 'composer install --no-dev --prefer-dist --optimize-autoloader' na raiz; depois 'php artisan storage:link' e 'php artisan config:cache' e 'php artisan route:cache'."
Write-Host "  5) Garanta permissões de escrita em storage/ e bootstrap/cache."
