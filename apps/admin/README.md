# SisRecepção — Admin

Aplicativo administrativo da SMS (Seções 8.1–8.4 do
[`docs/recepcao.md`](../../docs/recepcao.md)): **portal institucional**,
**BI** (resumo/setor/status e busca sobre o NovoSGA), **painel da diretoria
com relatório PDF**, **gestão de usuários** e **configurações com auditoria**.
SSO via **Keycloak** (OIDC + PKCE); acesso controlado por perfil
(`Developer`, `Admin`, `Diretoria`).

## Funcionalidades

- Portal com grade de sistemas da SMS
- BI: KPIs de tempo de espera/atendimento, por setor e por status, com dados
  de amostra quando o NovoSGA está indisponível (`BI_AMOSTRA`)
- Painel da diretoria com download em PDF (dompdf)
- Usuários via Admin REST API do Keycloak (`admin-cli`); desativado sem
  credenciais configuradas
- Configurações (parâmetros e deploy) com trilha de auditoria
- Health check `/health` e páginas de erro 404/500

## Estrutura

```
app/Http/Controllers/   Auth, Bi, Diretoria, Configuracoes, Usuarios, Legal
app/Http/Middleware/    AutenticadoKeycloak, AutorizadoPerfil
app/Services/           Novosga, Bi, Keycloak, Configuracoes, Deploy
routes/                 web.php, console.php
```

## Rodando localmente

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate      # cria tabelas configuracoes e log_auditorias
npm install && npm run build
php artisan serve        # http://admin.sisrecepcao.test
```

Testes (SQLite em memória):

```bash
php artisan test          # 41 testes
vendor/bin/pint --test
```

> Instalação completa do monorepo (banco, atendimento, infra) no
> [README raiz](../../README.md).
