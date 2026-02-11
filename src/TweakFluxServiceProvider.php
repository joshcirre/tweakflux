<?php

declare(strict_types=1);

namespace TweakFlux;

use Illuminate\Support\ServiceProvider;
use TweakFlux\Commands\TweakFluxApply;
use TweakFlux\Commands\TweakFluxCreate;
use TweakFlux\Commands\TweakFluxList;

final class TweakFluxServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tweakflux.php', 'tweakflux');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TweakFluxApply::class,
                TweakFluxCreate::class,
                TweakFluxList::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/tweakflux.php' => config_path('tweakflux.php'),
            ], 'tweakflux-config');

            $this->publishes([
                __DIR__.'/../resources/themes' => resource_path('themes'),
            ], 'tweakflux-themes');
        }
    }
}
