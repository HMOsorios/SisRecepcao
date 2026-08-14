<?php

namespace App\Providers;

use App\Services\Bi\BiService;
use App\Services\Deploy\DeployService;
use App\Services\Keycloak\KeycloakClient;
use App\Services\Novosga\NovosgaClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NovosgaClient::class, fn () => NovosgaClient::fromConfig());
        $this->app->singleton(BiService::class, fn () => BiService::fromConfig());
        $this->app->singleton(KeycloakClient::class, fn () => KeycloakClient::fromConfig());
        $this->app->singleton(DeployService::class);
    }

    public function boot(): void
    {
        //
    }
}
