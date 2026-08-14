# Roadmap

Visão do que já foi feito, do que está em andamento e dos próximos passos do
SisRecepção, alinhada às Seções do [`docs/recepcao.md`](docs/recepcao.md).

Legenda: ✅ feito · 🔄 em andamento · 🚧 planejado

## ✅ Feito (versão 2.0.0)

- [x] Monorepo com dois apps Laravel 12 (`apps/atendimento` e `apps/admin`)
- [x] Totem de autoatendimento **PWA** (kiosk): emissão de senha, prioridade,
      QR Code e áudio (Módulo 2.1)
- [x] Console do atendente com chamada e encaminhamento (Módulo 2.2)
- [x] Módulo de **crachás com OCR** e mascaramento LGPD (Módulo 2.3)
- [x] Notificações ao gabinete (Seção 2.4)
- [x] Painel de TV com **Mercure/SSE** e fallback de polling (Seção 2.6)
- [x] Integração REST com o **NovoSGA** (token OAuth2, filas e catálogos)
- [x] Contingência **outbox**: senha provisória local + reenvio (Seção 9.1)
- [x] **SSO Keycloak** nos dois apps (OIDC + PKCE)
- [x] **Admin**: portal, BI (resumo/setor/status/busca), painel da diretoria
      (PDF), usuários e configurações com auditoria (Seções 8.2–8.4)
- [x] Página de **saúde/health check** `/health` e páginas de erro 404/500
- [x] **Infra** versionada: Keycloak, Mercure, Coolify (Seção 8.9)
- [x] **CI** com Pint + PHPUnit e branch `main` protegida (Seção 9.4)

## 🔄 Em andamento

- [ ] Estabilização e homologação em ambiente de staging (Coolify)
- [ ] Criação e importação do realm `sisrecepcao` no Keycloak real
- [ ] Homologação da integração com o NovoSGA em produção (REST + Mercure)
- [ ] Revisão de acessibilidade **eMAG 3.1 / WCAG 2.1 AA** no totem (Seção 9.5)

## 🚧 Planejado

- [ ] **Backup 3-2-1** com `spatie/laravel-backup` e dump diário + binlog
      (Seção 9.2)
- [ ] Definição formal de **RPO/RTO** e teste trimestral de restauração
- [ ] **PHPStan/Larastan** no pipeline de CI (nível 5+)
- [ ] **Segmentação de rede**: VLAN dedicada ao totem/TV/crachá e
      admin atrás de VPN/allowlist (Seção 9.3)
- [ ] **Monitoramento**: alertas automáticos de TI ao entrar em contingência
      (Seção 9.1) e health checks com rollback (Seção 9.4)
- [ ] Análise estática de dependências e varredura de segurança
- [ ] Módulos sugeridos pela SMS após a primeira versão em produção
      (fila por serviço, painéis por andar, relatórios exportáveis, etc.)
