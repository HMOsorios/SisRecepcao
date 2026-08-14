# Contribuindo com o SisRecepção

Guia de boas maneiras para colaborar com o código do repositório. Obrigado
por ajudar! (Seção 9.4 do [`docs/recepcao.md`](docs/recepcao.md): todo código
só entra na branch de produção passando por review e testes.)

## Fluxo de trabalho

1. **Crie uma branch a partir de `main`** seguindo o padrão abaixo.
2. Implemente as mudanças em commits pequenos e coesos.
3. Rode **Pint** e os **testes** localmente antes de subir.
4. Abra um **Pull Request** com a descrição clara do que mudou.
5. Aguarde o **CI** (Pint + PHPUnit) passar e o review ser aprovado.
6. Só então o merge para `main` (que dispara o deploy automático via Coolify).

## Branches

| Prefixo | Uso | Exemplo |
|---|---|---|
| `feature/` | Nova funcionalidade | `feature/modulo-bi-setor` |
| `fix/` | Correção de bug | `fix/honeypot-totem` |
| `refactor/` | Refatoração sem mudança de comportamento | `refactor/novosga-client` |
| `docs/` | Documentação | `docs/arquitetura` |
| `chore/` | Tarefas de infraestrutura/CI | `chore/ci-pint` |

Regras:

- Nomes em minúsculas, separados por hífen.
- `main` é a branch de produção e é **protegida**: nada é commitado direto.
- `staging` é a branch de homologação (deploy de staging no Coolify).

## Padrão de commits (Conventional Commits)

```
<tipo>(<escopo>): <descrição no imperativo, minúscula>
```

Tipos:

- `feat:` nova funcionalidade
- `fix:` correção de bug
- `refactor:` mudança interna sem alterar comportamento
- `docs:` documentação
- `test:` testes
- `chore:` infra, CI, dependências
- `perf:` performance
- `style:` formatação/padrão de código
- `build:` build/package

Exemplos:

```
feat(atendimento): adiciona emissao de senha provisoria em contingencia
fix(admin): corrige lista de usuarios quando sem credenciais keycloak
test(bi): cobre resumo por setor com dados de amostra
```

Mensagens de commit **sem** fechamento de contexto não devem ser usadas
(ex.: `update`, `fix`, `altera arquivo`).

## Padrões de código

- **Laravel Pint** (PSR-12 + presets Laravel) — obrigatório:
  `vendor/bin/pint --test`
- Seguir as convenções do Laravel: controllers finos, lógica em
  `app/Services`, validação em Form Requests, Blade sem JS inline (JS
  vanilla em `public/js`).
- Não adicionar comentários desnecessários; nomear variáveis e métodos em
  português, seguindo os nomes já usados no projeto.
- Cada app tem seu próprio `composer.json` — rode os comandos na pasta do app
  correspondente (`apps/atendimento` ou `apps/admin`).

## Testes

- Toda mudança deve vir acompanhada de testes (PHPUnit/Pest).
- Execute antes de abrir o PR:
  ```bash
  vendor/bin/pint --test
  php artisan test
  ```
- Testes usam SQLite em memória (`phpunit.xml`); integrações externas são
  simuladas com `Http::fake` — nunca dependa de serviços reais nos testes.

## Abrindo um Pull Request

1. Título seguindo o padrão de commits (ex.: `feat(bi): painel por setor`).
2. Descreva **o que** mudou e **por quê**, referenciando o módulo/seção da
   especificação quando aplicável (ex.: "Seção 2.1").
3. Liste testes executados e resultados.
4. Marque "ready for review" somente com CI verde.

## Revisão

- Changes pequenos (idealmente < 300 linhas) são revisados mais rápido.
- Críticas são bem-vindas — o objetivo é manter o padrão do projeto.
- O revisor pode pedir ajustes; resolva em novos commits (não force push).

## Segurança e LGPD

- Nunca commite segredos (`.env`, chaves, senhas). `gitignore` está configurado.
- Dados pessoais (visitantes, crachás) seguem a LGPD (Seção 9.6): nada de
  dados reais em fixtures, logs ou PRs.
