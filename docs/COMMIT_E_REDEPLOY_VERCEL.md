# Guia de Commit (GitHub) e Redeploy (Vercel) — admin-guiadasbancas

Este guia documenta, de forma rápida e reproduzível, como você faz um commit no GitHub e aciona um redeploy na Vercel para o projeto `admin-guiadasbancas`.

> Observação: As informações abaixo foram preenchidas com base na configuração atual do seu projeto. Se algo mudar (nome do projeto, branch padrão, build command, etc.), atualize este arquivo.

## Dados do projeto
- Repositório GitHub: https://github.com/sidneysantossp/admin-guiadasbancas
- Projeto na Vercel: `admin-guiadasbancas`
- Project ID (Vercel): `prj_Sq4idQqoqFv6ZLNXs4RfBbJUHL1g`
- Framework/Runtime: `vercel-php@0.7.4` (Laravel em runtime PHP na Vercel)
- Entry point (Serverless): `api/index.php`
- Build command: `chmod +x build.sh && ./build.sh`
- Onde acessar no dashboard: Vercel Dashboard → Projects → `admin-guiadasbancas`

## Pré‑requisitos
- Git instalado e configurado (nome e e‑mail):
  - `git config --global user.name "Seu Nome"`
  - `git config --global user.email "seu-email@exemplo.com"`
- Estar no diretório raiz do projeto (ex.: `c:\xampp\htdocs\admin-bancas-do-bairro`)
- Remote `origin` apontando para o repositório acima:
  - Verificar: `git remote -v`
  - Ajustar (se necessário): `git remote set-url origin https://github.com/sidneysantossp/admin-guiadasbancas.git`

## Passo a passo
### 1) Commit e push no GitHub
1. Verifique a branch atual:
   - PowerShell: `git branch --show-current`
2. Adicione, comite e faça push:
   - `git add -A`
   - `git commit -m "docs: atualiza guia de commit e redeploy (GitHub + Vercel)"`
   - Descubra a branch: `git branch --show-current`
   - Faça push: `git push -u origin <sua-branch>` (geralmente `main`)

Dica: Se receber rejeição de push (divergência com remoto), rode:
- `git pull --rebase origin <sua-branch>` e depois `git push -u origin <sua-branch>`

### 2) Redeploy na Vercel
- A Vercel dispara automaticamente um novo deployment quando há um novo commit no branch monitorado (geralmente `main`).
- Alternativamente, no Dashboard da Vercel, abra o projeto `admin-guiadasbancas` → Deployments → botão "Redeploy" no deployment desejado.

## Pós‑deploy (checagens rápidas)
- Abra o deployment mais recente no dashboard e verifique:
  - Build Logs (para erros de dependência/composer, tamanho da função, etc.)
  - Function Logs (para erros em tempo de execução)
- Valide as rotas principais do app no domínio configurado.

## Variáveis de ambiente (Vercel)
Garanta que as env vars necessárias estejam definidas em Vercel → Settings → Environment Variables (por exemplo):
- `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`
- Variáveis de banco (se aplicável em produção serverless) como `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

Observação: Em ambiente serverless, avalie conexões persistentes e drivers suportados. Ajuste `config/database.php` e timeouts, se necessário.

## Erros comuns e soluções (troubleshooting)
- "This deployment cannot be redeployed. Please try again from a fresh commit."
  - Faça um pequeno commit novo (por ex., atualize este README) e dê push. Em geral, isso destrava o redeploy.
- Arquivos (HTML/PHP) sendo baixados em vez de renderizados
  - Na Vercel, isso costuma indicar configurações de projeto/dashboards persistentes incorretas, e não problema de Content-Type no código.
  - Solução: limpar/ajustar configurações do projeto no dashboard ou criar um novo projeto na Vercel (recomendado se o problema persistir).
- Função serverless > 250MB (limite Vercel)
  - Use `.vercelignore` para excluir dependências de desenvolvimento e pacotes pesados que não são necessários em produção (ex.: `phpunit`, `mockery`, `fakerphp`, pacotes de debugging, `laravel/sail`, `laravel/pint`, `laravel/tinker`, toolings `sebastian/*`, etc.).
  - Em `vercel.json`, mantenha `excludeFiles` conciso para não estourar o limite de 256 caracteres e focar só no essencial (ex.: `storage/**`, `tests/**`, `.git/**`, `bootstrap/cache/**`, `node_modules/**`, `installation/**`, `Modules/**`, `admin-bancas-do-bairro/**`, `servidor-web/**`, `database/**`, `tools/**`, `*.log`, `*.cache`, `*.md`, `*.txt`).
- "Build Command" antigo continua executando mesmo após removido do `vercel.json`
  - Esse comando pode ficar cacheado nas configurações do projeto na Vercel (Build & Output Settings). Limpe/ajuste no dashboard ou crie um novo projeto.

## Comandos rápidos (Windows PowerShell)
```
# Dentro do diretório do projeto
$branch = git branch --show-current
if (-not $branch) { $branch = "main" }

git add -A
git commit -m "docs: atualiza guia de commit e redeploy (GitHub + Vercel)"
git push -u origin $branch
```

## Dicas adicionais
- Use mensagens de commit descritivas (ex.: `feat:`, `fix:`, `docs:`, `chore:`) para manter o histórico limpo.
- Se precisar forçar um redeploy sem mudanças no código, altere um arquivo de documentação (como este) e faça um novo commit.

---
Atualize este guia sempre que você mudar o fluxo de deploy, configurações na Vercel ou estrutura do repositório.
