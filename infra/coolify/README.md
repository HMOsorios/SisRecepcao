# Infra — Coolify (deploy)

Publicação dos dois apps Laravel via Coolify (Seção 8.9 e 9.4 do
`docs/recepcao.md`). Servidor próprio, deploy automático a partir do GitHub
(branch protegida) com health check pós-deploy.

## Instalação do Coolify

Instalação oficial (precisa de Docker):

```bash
curl -fsSL https://cdn.coollabs.io/coolify/install.sh | sudo bash
```

Ou, com root:

```bash
curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash
```

O Coolify sobe Traefik (proxy/reverse com TLS) e seus serviços auxiliares
(Postgres, Redis). Depois de instalado, acesse o painel, gere os certificados
Let's Encrypt e adicione um **Servidor** de produção (ou use o local).

## Aplicações

Crie **dois recursos** apontando para o repositório `HMOsorios/sisrecepcao`
(branch `main`), com build **Nixpacks** (PHP 8.3, suportado nativamente):

| Recurso | Diretório de build | Domínio | Health check |
|---|---|---|---|
| `sisrecepcao-atendimento` | `apps/atendimento` | `https://atendimento.sisrecepcao.test` | `/health` |
| `sisrecepcao-admin` | `apps/admin` | `https://admin.sisrecepcao.test` | `/health` |

> No Coolify, o diretório de build é o caminho dentro do repositório
> (`apps/atendimento` / `apps/admin`). Copie o `.env.example` correspondente
> para o painel de **Environment Variables** de cada recurso (veja
> `atendimento.env.example` e `admin.env.example` nesta pasta).

### Configurações recomendadas (por recurso)

- **Porta** (exposição): `80` — start command do Laravel:
  `php artisan serve --host=0.0.0.0 --port=80`
  (ou suba um worker tipo FrankenPHP/Octane na mesma porta se quiser).
- **Command** (deploy): após build, executar
  `php artisan migrate --force && php artisan config:cache && php artisan route:cache`.
- **Worker**: `php artisan queue:work --tries=1 --timeout=0` para o fila
  (outbox de contingência no atendimento, Seção 9.1).
- **Scheduler**: `php artisan schedule:work` (backup 3-2-1, Seção 9.2).
- **Health check** (`/health`): defina `healthcheckEnabled=true` — o Coolify
  só corta o tráfego para o container após responder OK; se falhar, faz
  rollback (Seção 9.4).
- **Auto deploy**: apenas no branch protegido (`main`). O `sisrecepcao-admin`
  usa o webhook da Seção 8.4 (`COOLIFY_WEBHOOK_URL`) para disparar deploy a
  partir do próprio painel.

## Banco de dados

Um único MySQL (8.0+ / MariaDB 10.6+) com o banco `sisrecepcao_db` é
compartilhado pelos dois apps (Seção 5.3). No Coolify, adicione uma
**Database MySQL** e use o mesmo `DB_DATABASE/DB_USERNAME/DB_PASSWORD` nos dois
recursos.

## Rede

- Os dois apps expostos por HTTPS via Traefik do Coolify.
- O `sisrecepcao-admin` deve ficar atrás de VPN ou allowlist de IP (Seção 9.3).
- VLAN dedicada para totem/TV/crachá, com saída só para
  `https://atendimento.sisrecepcao.test` (Seção 9.3).

## Deploy automático sem gate de CI

O fluxo exigido pela Seção 9.4:

1. Push/merge em `main` passa pelo pipeline `.github/workflows/ci.yml`
   (Pint + PHPUnit nos dois apps) — branch protegida com status check.
2. Coolify observa somente `main`; merge aprovado dispara deploy.
3. Health check `/health` valida o container antes de cortar tráfego;
   falha ⇒ rollback automático.
