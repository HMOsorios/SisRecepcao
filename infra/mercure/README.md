# Infra — Mercure Hub

Hub Mercure compartilhado com o NovoSGA, responsável por notificações em tempo
real dos módulos 2.1 (painel de TV) e 2.2 (console do atendente) — Seções
2.6 e 5.1 do `docs/recepcao.md`.

## Topologia

- **Publisher:** o NovoSGA publica eventos (`MercureService`) usando a
  `MERCURE_JWT_SECRET` como chave de assinatura do JWT.
- **Subscribers:** o painel de TV (`painel-tv`) e o console do setor
  (`atendente`) assinam via `EventSource` — `MercureStream::eventSourceUrl()`.

Tópicos relevantes (ver `src/Service/MercureService.php` do NovoSGA):

| Evento NovoSGA | Tópico(s) |
|---|---|
| `notificaFilaUnidade` | `/unidades/{id}/fila`, `/fila` |
| `notificaPainel` | `/paineis`, `/unidades/{id}/painel` |
| `notificaAtendimento` | `/atendimentos/{id}`, `/unidades/{id}/fila` |

O SisRecepção escuta apenas `/unidades/{id}/painel` e `/unidades/{id}/fila`
(com fallback de polling de 8s quando o SSE falha).

## Uso

```bash
cp .env.example .env
# edite .env com a mesma MERCURE_JWT_SECRET do NovoSGA
docker compose up -d
```

## Configuração nos apps

`apps/atendimento/.env`:

```dotenv
MERCURE_URL=https://mercure.sisrecepcao.test/.well-known/mercure
MERCURE_PUBLISHER_KEY=
MERCURE_SUBSCRIBER_KEY=
```

- Assinatura anônima (padrão): deixe `MERCURE_SUBSCRIBER_KEY` vazia — o hub
  aceita qualquer subscriber. Ideal para painel de TV público.
- Para exigir JWT de assinatura: defina `MERCURE_SUBSCRIBER_JWT_KEY` no hub e
  `MERCURE_SUBSCRIBER_KEY` (JWT pré-gerado) nos apps.
