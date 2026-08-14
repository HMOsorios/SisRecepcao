# TODO

Bloco de anotações rápido — tarefas pendentes, refatorações e ideias antes de
virarem tasks oficiais. Promova para o GitHub Issues quando detalhadas.

## Pendências

- [ ] Inicializar repositório Git (`git init`) e criar o repositório
      `HMOsorios/sisrecepcao` no GitHub
- [ ] Validar o import do realm `sisrecepcao` em um Keycloak real
      (`infra/keycloak/realm-sisrecepcao.json`) e criar usuários de teste
- [ ] Configurar `.env` de produção (APP_KEY, DB, segredos NovoSGA/Keycloak)
      e definir o fluxo de segredos no Coolify
- [ ] Subir o hub Mercure e validar o SSE fim-a-fim com o NovoSGA
- [ ] Homologar `NOVOSGA_SIMULATION=false` contra a API real
- [ ] Adicionar **PHPStan/Larastan** ao CI (ROADMAP)
- [ ] Definir RPO/RTO e implementar backup 3-2-1 (`spatie/laravel-backup`)
- [ ] Auditoria de acessibilidade eMAG/WCAG no totem e painel TV
- [ ] Definir política de VPN/allowlist para o `sisrecepcao-admin`
- [ ] Confirmar grafia do domínio de produção (`recpcao.atb.app.br`?)

## Refatorações

- [ ] Extrair validações dos controllers para Form Requests (padrão Laravel)
- [ ] Revisar se `MercurePublisher` ainda é necessário (publicação é do NovoSGA)
- [ ] Centralizar constantes de perfis/roles em um enum ou config única

## Ideias

- [ ] Relatórios do BI exportáveis (CSV/XLSX)
- [ ] Painéis de TV por andar/serviço
- [ ] Modo escuro no painel administrativo
- [ ] Melhorar fallback de catálogo com cache de `localStorage` (Seção 9.1)
