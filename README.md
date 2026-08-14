# SisRecepção — Controle de Recepção e Atendimento da SMS

Sistema de gestão de recepção e atendimento da **Secretaria Municipal de Saúde
(SMS)**: totem de autoatendimento, console de atendimento, painel de TV,
crachá e painel administrativo (BI/diretoria), tendo o **NovoSGA 2.3** como
motor de filas (Seções 1–9 do [`docs/recepcao.md`](docs/recepcao.md)).

> **Licença:** uso privado e comercial liberado sem custos à SMS, por tempo
> indeterminado. Veja [`LICENSE`](LICENSE).

## Tecnologias

- **PHP 8.3+** e **Laravel 12** (dois aplicativos independentes no monorepo)
- **PWA** (totem kiosk): `manifest.json` + service worker em
  `apps/atendimento/public` — funcionamento off-line com cache de assets
- **MySQL 8.0+ / MariaDB 10.6+** — banco único compartilhado `sisrecepcao_db`
- **NovoSGA 2.3** — REST API + **Mercure** (SSE em tempo real)
- **Keycloak** — SSO (OIDC authorization-code + PKCE)
- **Coolify + Docker** — publicação e deploy automático
- **JavaScript vanilla** (Blade) — interface leve, sem SPA framework

## Aplicativos

| App | Pasta | Função |
|---|---|---|
| **SisRecepção — Atendimento** | `apps/atendimento` | Totem (Módulo 2.1), console do atendente (2.2), painel de TV (2.1), crachá/OCR (2.3), senha provisória (9.1) |
| **SisRecepção — Admin** | `apps/admin` | Portal, BI, painel da diretoria (PDF), usuários (8.3), configurações e deploy (8.4) |

## Rodando localmente

Requisitos: PHP 8.3+ (`mbstring`, `dom`, `sqlite3`, `fileinfo`, `zip`),
Composer 2, MySQL 8.0+, Node 20+ (assets) e, opcionalmente, Docker para a
infraestrutura (`infra/`).

```bash
# 1) Banco único compartilhado
#    crie o schema no MySQL:
CREATE DATABASE sisrecepcao_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 2) Atendimento
cd apps/atendimento
cp .env.example .env      # edite conforme seu ambiente
composer install
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan serve          # http://atendimento.sisrecepcao.test

# 3) Admin
cd ../admin
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan serve          # http://admin.sisrecepcao.test
```

> Dica de hosts locais (arquivo `hosts`): `atendimento.sisrecepcao.test` e
> `admin.sisrecepcao.test` → `127.0.0.1`. Componentes externos (Keycloak,
> Mercure, NovoSGA) em [`infra/`](infra).

### Testes

```bash
# em cada app
php artisan test        # atendimento: 41 testes · admin: 41 testes
vendor/bin/pint --test  # padrão de código (Laravel Pint)
```

## Credenciais de teste

Não existem senhas padrão embutidas no código — a autenticação é feita pelo
Keycloak (SSO). Para testar localmente:

| Recurso | Como usar |
|---|---|
| **Login** | Crie usuários no realm `sisrecepcao` do Keycloak e atribua as roles `Developer`, `Admin`, `Diretoria`, `Servidor`, `Atendente` ([`infra/keycloak`](infra/keycloak)) |
| **NovoSGA** | Com `NOVOSGA_SIMULATION=true` (padrão do `.env.example`), nenhuma credencial externa é necessária: o sistema emite senha provisória local (Seção 9.1) e o BI usa dados de amostra (`BI_AMOSTRA=500`) |
| **Integração real** | Preencha `NOVOSGA_CLIENT_ID`/`NOVOSGA_CLIENT_SECRET` (conta de serviço) e `KEYCLOAK_*` — ver `infra/coolify/*.env.example` |
| **BI/diretoria** | Requer login com perfil `Developer`, `Admin` ou `Diretoria` no app admin |

## Documentação

- [`docs/recepcao.md`](docs/recepcao.md) — especificação funcional e técnica (v2.0)
- [`docs/novosga-2.3 - Gov/`](docs/novosga-2.3%20-%20Gov/) — referência do NovoSGA
- [`ARCHITECTURE.md`](ARCHITECTURE.md) — desenho técnico · [`CONTRIBUTING.md`](CONTRIBUTING.md) — como colaborar
- [`CHANGELOG.md`](CHANGELOG.md) — histórico de versões · [`ROADMAP.md`](ROADMAP.md) — planejamento
- [`TODO.md`](TODO.md) — tarefas pendentes
