<?php

namespace App\Services\Keycloak;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/**
 * Cliente OIDC (authorization-code + PKCE) para o Keycloak — IdP central
 * do SisRecepção (Seção 5.4/8.6).
 *
 * Usa discovery (/.well-known/openid-configuration) para obter os endpoints.
 * O estado de login fica em sessão; nenhuma senha é armazenada no app.
 */
class KeycloakClient
{
    protected string $authorizationEndpoint = '';

    protected string $tokenEndpoint = '';

    protected string $userinfoEndpoint = '';

    protected string $logoutEndpoint = '';

    protected bool $discovered = false;

    public function __construct(
        protected readonly string $baseUrl,
        protected readonly string $realm,
        protected readonly string $clientId,
        protected readonly string $clientSecret,
        protected readonly string $redirectUri,
        protected readonly string $scopes,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            baseUrl: rtrim((string) config('keycloak.base_url'), '/'),
            realm: (string) config('keycloak.realm'),
            clientId: (string) config('keycloak.client_id'),
            clientSecret: (string) config('keycloak.client_secret'),
            redirectUri: (string) config('keycloak.redirect_uri'),
            scopes: (string) config('keycloak.scopes', 'openid profile email'),
        );
    }

    public function authorizationUrl(): string
    {
        $this->discover();

        $state = Str::random(40);
        $verifier = $this->codeVerifier();
        $challenge = $this->codeChallenge($verifier);

        Session::put(config('keycloak.session_state_key'), $state);
        Session::put('keycloak_code_verifier', $verifier);

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => $this->scopes,
            'state' => $state,
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
        ];

        return $this->authorizationEndpoint.'?'.http_build_query($params);
    }

    public function handleCallback(string $code): array
    {
        $this->discover();

        $verifier = Session::pull('keycloak_code_verifier', '');

        $response = Http::asForm()
            ->post($this->tokenEndpoint, [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
                'code' => $code,
                'code_verifier' => $verifier,
            ])
            ->throw();

        $tokens = $response->json();

        $userinfo = Http::withToken($tokens['access_token'])
            ->acceptJson()
            ->get($this->userinfoEndpoint)
            ->throw()
            ->json();

        Session::put(config('keycloak.session_token_key'), $tokens['access_token']);
        Session::put(config('keycloak.session_refresh_key'), $tokens['refresh_token'] ?? '');
        Session::put(config('keycloak.session_user_key'), $userinfo);

        return $userinfo;
    }

    public function usuario(): ?array
    {
        $usuario = Session::get(config('keycloak.session_user_key'));

        return is_array($usuario) ? $usuario : null;
    }

    public function usuarioId(): ?string
    {
        $usuario = $this->usuario();

        return $usuario ? (string) $usuario[config('keycloak.subject_claim', 'sub')] : null;
    }

    /**
     * Perfis (roles) vindos do Keycloak (realm_access.roles + client roles).
     */
    public function perfis(): array
    {
        $usuario = $this->usuario() ?? [];

        $roles = $usuario['realm_access']['roles'] ?? [];

        if (isset($usuario['resource_access'][$this->clientId]['roles'])) {
            $roles = array_merge($roles, $usuario['resource_access'][$this->clientId]['roles']);
        }

        return array_values(array_unique($roles));
    }

    public function temPerfil(string ...$perfis): bool
    {
        return count(array_intersect($this->perfis(), $perfis)) > 0;
    }

    public function logoutUrl(string $redirect): string
    {
        $this->discover();

        return $this->logoutEndpoint.'?'.http_build_query([
            'client_id' => $this->clientId,
            'post_logout_redirect_uri' => $redirect,
        ]);
    }

    /**
     * Lista de usuários do realm via Admin REST API (Seção 8.3).
     * Requer credenciais de administrador configuradas.
     *
     * @return array<int,array<string,mixed>>|null
     */
    public function usuarios(int $max = 100): ?array
    {
        $adminUser = (string) config('keycloak.admin_user');
        $adminPassword = (string) config('keycloak.admin_password');

        if ($adminUser === '' || $adminPassword === '') {
            return null;
        }

        $this->discover();

        $token = Http::asForm()
            ->post("{$this->baseUrl}/realms/{$this->realm}/protocol/openid-connect/token", [
                'grant_type' => 'password',
                'client_id' => 'admin-cli',
                'username' => $adminUser,
                'password' => $adminPassword,
            ])
            ->throw()
            ->json('access_token');

        return Http::withToken($token)
            ->acceptJson()
            ->get("{$this->baseUrl}/admin/realms/{$this->realm}/users", ['max' => $max])
            ->throw()
            ->json() ?? [];
    }

    protected function discover(): void
    {
        if ($this->discovered) {
            return;
        }

        $config = Cache::remember('keycloak_discovery', 3600, function () {
            return Http::acceptJson()
                ->timeout(5)
                ->get("{$this->baseUrl}/realms/{$this->realm}/.well-known/openid-configuration")
                ->throw()
                ->json();
        });

        $this->authorizationEndpoint = (string) ($config['authorization_endpoint'] ?? $this->authorizationEndpoint);
        $this->tokenEndpoint = (string) ($config['token_endpoint'] ?? $this->tokenEndpoint);
        $this->userinfoEndpoint = (string) ($config['userinfo_endpoint'] ?? $this->userinfoEndpoint);
        $this->logoutEndpoint = (string) ($config['end_session_endpoint'] ?? $this->logoutEndpoint);

        $this->discovered = true;
    }

    protected function codeVerifier(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(48)), '+/', '-_'), '=');
    }

    protected function codeChallenge(string $verifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    }
}
