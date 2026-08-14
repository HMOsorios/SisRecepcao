<?php

namespace App\Providers;

use App\Services\Cracha\OcrContrato;
use App\Services\Cracha\OcrNaoImplementado;
use App\Services\Fila\CatalogoService;
use App\Services\Fila\ConsoleFilaService;
use App\Services\Fila\EmissaoSenhaService;
use App\Services\Keycloak\KeycloakClient;
use App\Services\Novosga\MercurePublisher;
use App\Services\Novosga\MercureStream;
use App\Services\Novosga\NovosgaClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NovosgaClient::class, fn () => NovosgaClient::fromConfig());
        $this->app->singleton(MercureStream::class, fn () => MercureStream::fromConfig());
        $this->app->singleton(MercurePublisher::class, fn () => MercurePublisher::fromConfig());
        $this->app->singleton(EmissaoSenhaService::class, fn () => EmissaoSenhaService::fromConfig());
        $this->app->singleton(ConsoleFilaService::class);
        $this->app->singleton(CatalogoService::class);
        $this->app->singleton(KeycloakClient::class, fn () => KeycloakClient::fromConfig());

        $this->app->bind(OcrContrato::class, OcrNaoImplementado::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
