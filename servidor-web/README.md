# Pacote para hospedagem (cPanel)

Esta pasta contém utilitários para preparar o projeto Laravel para upload em hospedagem compartilhada (cPanel).

## Conteúdo
- `prepare_for_cpanel.ps1`: script PowerShell que monta uma pasta `dist/` com os arquivos prontos para envio.
- `.env.cpanel.example`: modelo de arquivo `.env` para produção (ajuste seus valores e faça upload como `.env`).

## Como preparar o pacote (Windows/PowerShell)
1. Abra o PowerShell na pasta `servidor-web/`.
2. Execute um dos comandos:
   - Sem vendor (vai instalar no servidor via Composer):
     ```powershell
     .\prepare_for_cpanel.ps1
     ```
   - Com vendor (se você já rodou `composer install --no-dev` localmente):
     ```powershell
     .\prepare_for_cpanel.ps1 -IncludeVendor
     ```
3. Após concluir, a pasta `servidor-web/dist/` conterá o pacote para upload.

## Upload e configuração no cPanel
1. Envie o conteúdo de `servidor-web/dist/` para uma pasta no servidor (ex.: `~/laravel-app/`).
2. Ajuste o Document Root do domínio/subdomínio para apontar para `~/laravel-app/public/`.
   - Alternativa: mover o conteúdo de `public/` para `public_html/` e ajustar caminhos em `public_html/index.php`:
     ```php
     require __DIR__.'/../vendor/autoload.php';
     $app = require_once __DIR__.'/../bootstrap/app.php';
     ```
3. Crie o arquivo `.env` no servidor com base em `servidor-web/.env.cpanel.example` (cole suas variáveis reais).
4. Se tiver SSH/Composer no servidor, rode na raiz do projeto:
   ```bash
   composer install --no-dev --prefer-dist --optimize-autoloader
   php artisan key:generate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   ```
5. Permissões de escrita: garanta que `storage/` e `bootstrap/cache` sejam graváveis pelo PHP.

## Variáveis de ambiente essenciais
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://seu-dominio`
- `APP_KEY=...` (obrigatório, gere localmente com `php artisan key:generate --show` e cole)
- `DB_CONNECTION=mysql`
- `DB_HOST`, `DB_PORT=3306`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `CACHE_DRIVER=file`
- `SESSION_DRIVER=file`
- `FILESYSTEM_DISK=public` (ou `s3` se usar armazenamento externo)
- Outras integrações do seu projeto (e-mail, gateways, etc.).

## Observações deste projeto
- Evite colocar arquivos grandes (zip/rar) na raiz do projeto no servidor.
- Se usar filas/queues, em hospedagem compartilhada defina `QUEUE_CONNECTION=sync`.
- WebSockets (beyondcode/laravel-websockets) normalmente não operam em cPanel.
