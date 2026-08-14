# Infra — Keycloak

Realm `sisrecepcao` (Seções 5.4, 8.3 e 8.6 do `docs/recepcao.md`) — IdP central
de autenticação para os dois apps (SSO via OIDC authorization-code + PKCE).

## Import

Keycloak com import na primeira subida:

```bash
# cole realm-sisrecepcao.json em /opt/keycloak/data/import/ do container
docker exec -i keycloak bin/kc.sh import --file /opt/keycloak/data/import/realm-sisrecepcao.json
```

Após o import:

1. Crie os usuários (Dev/Admin/Diretoria/Servidor/Atendente) em
   *Realm settings → Users*.
2. Atribua as roles do realm na aba *Role mapping* de cada usuário.
3. Crie (ou use) um usuário com a role `realm-admin` para o app admin
   gerenciar usuários — é com as credenciais dele que o app usa o grant
   `password` no client `admin-cli`.

> Usuários não entram no import: senhas usam hash BCrypt e não fazem parte
> do formato de import — crie-os na interface após importar o realm.

## Clients

| client | tipo | fluxo | uso |
|---|---|---|---|
| `sisrecepcao-atendimento` | public | authorization_code + PKCE (S256) | login de servidores/atendentes em `apps/atendimento` |
| `sisrecepcao-admin` | public | authorization_code + PKCE (S256) | login de Dev/Admin/Diretoria em `apps/admin` |
| `admin-cli` | public | password grant (direct access) | `KeycloakClient::usuarios()` — gestão de usuários via Admin REST API |

## Roles de realm

`Developer`, `Admin`, `Diretoria`, `Servidor`, `Atendente`.

As apps leem os perfis do token via `realm_access.roles` (claim padrão do
Keycloak). O app admin também mescla `resource_access.sisrecepcao-admin.roles`
— caso queira roles exclusivas por client, basta criar client roles no client
`sisrecepcao-admin`.

## Mapeamento nos apps

`apps/atendimento/.env`:

```dotenv
KEYCLOAK_BASE_URL=https://sso.sms.local
KEYCLOAK_REALM=sisrecepcao
KEYCLOAK_CLIENT_ID=sisrecepcao-atendimento
KEYCLOAK_REDIRECT_URI=https://atendimento.sisrecepcao.test/auth/callback
```

`apps/admin/.env`:

```dotenv
KEYCLOAK_BASE_URL=https://sso.sms.local
KEYCLOAK_REALM=sisrecepcao
KEYCLOAK_CLIENT_ID=sisrecepcao-admin
KEYCLOAK_REDIRECT_URI=https://admin.sisrecepcao.test/auth/callback
KEYCLOAK_ADMIN_USER=usuario.realm-admin
KEYCLOAK_ADMIN_PASSWORD=
```

> O `KEYCLOAK_ADMIN_USER`/`KEYCLOAK_ADMIN_PASSWORD` são usados somente no
> grant `password` do `admin-cli` para listar usuários (Seção 8.3). Se vazios,
> o painel de usuários é desativado.
