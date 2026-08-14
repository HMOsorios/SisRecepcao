# Changelog

Todas as mudanças relevantes do SisRecepção são registradas aqui. O formato
segue [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/) e o
versionamento semântico [SemVer](https://semver.org/lang/pt-BR/).

## [Não publicado]

### Adicionado
- Nada ainda.

---

## [2.0.0] — 2026-08-14

### Adicionado
- **Módulo administrativo completo** (`apps/admin`): portal institucional,
  painel da diretoria com relatório PDF (dompdf), BI com resumo/setor/status e
  busca, gestão de usuários via Keycloak (Seção 8.3) e configurações com
  trilha de auditoria (Seção 8.4).
- **SSO Keycloak** nos dois apps (OIDC authorization-code + PKCE) com
  middlewares `auth.keycloak` e `perfil` (Seções 5.4/8.6).
- **Painel administrativo de deploy** (`DeployService`): pull do GitHub,
  migrações e webhook Coolify a partir do próprio painel (Seção 8.4).
- **Contingência outbox** no atendimento: senha provisória local + reenvio
  assíncrono ao NovoSGA (Seção 9.1).
- **Infraestrutura versionada**: `infra/keycloak`, `infra/mercure`,
  `infra/coolify` e pipeline de CI (Pint + PHPUnit) em `.github/workflows`
  (Seções 8.9/9.4).

### Corrigido
- Exceções do NovoSGA passam o código de status a `getCode()`.
- Modelo `Configuracao` usando a tabela `configuracoes` (migrations próprias).
- Views Blade sem `null` em coleções (`@forelse` exige arrays).

## [1.1.0] — 2026-08

### Adicionado
- Módulo de **crachás com OCR** e mascaramento LGPD (Módulo 2.3).
- **Notificações** ao gabinete (Seção 2.4) com fila.
- Painel de TV com integração **Mercure/SSE** e fallback de polling (Seção 2.6).
- Portal/BI inicial no app administrativo.

## [1.0.0] — 2026-07

### Adicionado
- **Totem de autoatendimento PWA** (kiosk): emissão de senha, prioridade e
  QR Code (Módulo 2.1).
- **Console do atendente** com chamada e encaminhamento (Módulo 2.2).
- Integração inicial com o **NovoSGA** (REST API, filas e catálogos).
- Estrutura do monorepo com os dois aplicativos Laravel 12.
