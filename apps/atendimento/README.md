# SisRecepção — Atendimento

Aplicativo de atendimento e recepção da SMS (Módulos 2.1–2.3 do
[`docs/recepcao.md`](../../docs/recepcao.md)): **totem de autoatendimento
(PWA)**, **console do atendente**, **painel de TV**, **módulo de crachá/OCR**
e **senha provisória em contingência**. O **NovoSGA 2.3** é o motor de filas
(REST API + Mercure).

## Funcionalidades

- Totem kiosk PWA (`public/manifest.json` + `public/sw.js`): emissão de senha,
  prioridade, QR Code, áudio e rate limiting
- Console do atendente: chamada, encaminhamento e atendimento
- Painel de TV: atualização via Mercure (SSE) com fallback de polling de 8s
- Crachá: OCR e mascaramento LGPD
- Contingência (Seção 9.1): senha provisória local em `senha_outbox` e
  reenvio assíncrono ao NovoSGA
- SSO via Keycloak (OIDC + PKCE)

## Estrutura

```
app/Http/Controllers/   Totem, Atendente, PainelTv, Cracha, Auth
app/Services/           Fila, Novosga, Keycloak, Totem, Cracha, Notificacoes
app/Models/             SenhaOutbox, Cracha, Visitante, Notificacao, Mascaramento
public/                 manifest.json, sw.js, js/, css/
routes/                 web.php, console.php
```

## Rodando localmente

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan serve        # http://atendimento.sisrecepcao.test
```

Testes (SQLite em memória):

```bash
php artisan test          # 41 testes
vendor/bin/pint --test
```

> Instalação completa do monorepo (banco, admin, infra) no
> [README raiz](../../README.md).
