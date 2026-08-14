# Arquitetura Técnica

Visão geral de como o SisRecepção é estruturado, para que o time mantenha o
mesmo padrão. Referência à especificação em [`docs/recepcao.md`](docs/recepcao.md).

## Visão geral

```
                    ┌─────────────────────────────────────────────────┐
                    │                    Keycloak                     │
                    │              (SSO · realm sisrecepcao)          │
                    └───────────────┬─────────────────────────────────┘
                                    │ OIDC auth-code + PKCE
        ┌───────────────────────────┼───────────────────────────────┐
        │                           │                               │
┌───────▼────────┐         ┌────────▼────────┐             ┌────────▼────────┐
│  atendimento   │         │     admin       │             │     NovoSGA     │
│  (Laravel 12)  │         │  (Laravel 12)   │             │ (motor de filas)│
│  totem/console/│         │  portal/BI/     │             │ REST + Mercure  │
│  painel/crachá │         │  diretoria/usr  │             └───────┬────────┘
└───────┬────────┘         └───────┬────────┘                     │
        │ Mercure/SSE               │ HTTP (somente leitura)       │ publica eventos
        └───────────────────────────┼──────────────────────────────┘
                                    ▼
                         MySQL  sisrecepcao_db
                    (banco único compartilhado, utf8mb4)
```

- **Dois apps Laravel 12 independentes** dentro de um monorepo — cada um com
  seu `composer.json`, rotas, testes e `.env`.
- **NovoSGA é a única fonte de verdade da numeração** (Seção 5.4); os apps
  nunca geram número definitivo — só senha provisória em contingência (Seção 9.1).
- **Mercure** é o canal de tempo real: painel TV e console assinam SSE direto
  do hub do NovoSGA; polling de 8s como fallback.
- **Keycloak** centraliza autenticação (Seções 5.4/8.6); perfis decidem acesso.

## Estrutura do monorepo

```
sisrecepcao/
├─ apps/
│  ├─ atendimento/          # Totem, console, painel TV, crachá
│  │  ├─ app/
│  │  │  ├─ Http/Controllers/{Totem,Atendente,PainelTv,Cracha,Auth}/
│  │  │  ├─ Models/         # Cracha, SenhaOutbox, Visitante, Notificacao, Mascaramento
│  │  │  └─ Services/
│  │  │     ├─ Fila/        # catálogos, emissão de senha, contingência
│  │  │     ├─ Novosga/     # client REST, MercureStream, MercurePublisher
│  │  │     ├─ Keycloak/    # OIDC auth-code + PKCE
│  │  │     ├─ Totem/       # QR Code, rate limiting
│  │  │     ├─ Cracha/      # OCR + mascaramento
│  │  │     └─ Notificacoes/
│  │  ├─ public/            # manifest.json + sw.js (PWA do totem), js/, css/
│  │  └─ routes/            # web.php, console.php
│  └─ admin/                # Portal, BI, diretoria, usuários, configurações
│     └─ app/
│        ├─ Http/Controllers/{Auth,Bi,Diretoria,Configuracoes,Usuarios,Legal}/
│        ├─ Http/Middleware/  # AutenticadoKeycloak, AutorizadoPerfil
│        └─ Services/
│           ├─ Novosga/       # client REST (somente leitura)
│           ├─ Bi/            # resumo/setor/status + amostra
│           ├─ Keycloak/      # OIDC + admin-cli (usuários)
│           ├─ Configuracoes/ # valores + auditoria
│           └─ Deploy/        # git pull + migrate + webhook Coolify
├─ docs/                    # especificação (recepcao.md) + referência NovoSGA
├─ infra/                   # keycloak/ mercure/ coolify/ (Docker)
└─ .github/workflows/ci.yml # Pint + PHPUnit
```

## Decisões de arquitetura

### 1. Banco de dados único (MySQL 8.0+ / MariaDB 10.6+)
Um só schema `sisrecepcao_db` para os dois apps (Seção 5.3). Cada app migra
apenas suas tabelas; o admin usa tabelas de auditoria/parâmetros
(`configuracoes`, `log_auditorias`) e o atendimento as de domínio
(`senha_outbox`, `crachas`, `visitantes`, `notificacoes`).

### 2. Camada de serviço isolada
`app/Services/*` concentra toda integração externa. **Nenhuma view/controller
fala diretamente com o NovoSGA/Keycloak** — facilita testes com `Http::fake` e
trocas de fornecedor.

### 3. Padrão cliente `request()`/`rawRequest()`
No `NovosgaClient`, o fluxo de token é resolvido antes da chamada sem
recursão; o token é cacheado (`novosga_token`). Exceções tipadas
(`NovosgaApiException`, `NovosgaIndisponivelException`) diferenciam erro da
API de indisponibilidade da rede.

### 4. Contingência outbox (Seção 9.1)
Se o NovoSGA está fora, o totem emite senha **provisória local** (`OFF-…`),
persistida em `senha_outbox`; um job da fila reenvia preservando a ordem real
via `POST /api/distribui`. Três níveis: lento → retry backoff;
indisponível → outbox; rede fora → atendimento manual.

### 5. Tempo real via Mercure
`MercureStream::eventSourceUrl()` monta a URL do EventSource com os tópicos do
NovoSGA (`/unidades/{id}/painel`, `/unidades/{id}/fila`, `/atendimentos/{id}`).
O `painel.js` cai para polling de 8s após 3 falhas de SSE e reconecta
automaticamente.

### 6. Autenticação e autorização
- **OIDC authorization-code + PKCE** com `code_verifier` na sessão e validação
  do `state`.
- Middlewares `auth.keycloak` (presença do usuário na sessão) e `perfil`
  (lista de roles plana via `realm_access` + `resource_access`).
- Rotas sensíveis por perfil: `bi.*` (autenticado), `diretoria.*`
  (Developer/Admin/Diretoria), `usuarios.*` e `configuracoes.*` (Developer).

### 7. Segurança e LGPD
- Mascaramento de dados pessoais (documento, telefone) em telas e PDFs.
- Honeypot + rate limiting no totem; headers de segurança; auditoria de
  configurações; logout limpa tokens e `code_verifier` da sessão.

### 8. Front-end leve (Blade + JS vanilla)
Sem framework SPA. O **totem é PWA** (`manifest.json` + `sw.js`) com cache de
assets para funcionar off-line. Acessibilidade por design (teclado, contraste,
alvos ≥ 44px, Web Speech no totem).

### 9. Deploy e CI
Coolify observa a branch `main`; pipeline GitHub Actions exige Pint + PHPUnit
nos dois apps (Seção 9.4). Health check `/health` valida o container antes de
cortar tráfego; rollback automático em falha.
