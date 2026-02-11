<?php

declare(strict_types=1);

namespace TweakFlux;

use Illuminate\Support\Facades\Blade;
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
        $this->registerBladeDirectives();

        if ($this->app->runningInConsole()) {
            $this->commands([
                TweakFluxApply::class,
                TweakFluxCreate::class,
                TweakFluxList::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/tweakflux.php' => config_path('tweakflux.php'),
            ], 'tweakflux-config');
        }
    }

    private function registerBladeDirectives(): void
    {
        Blade::directive('tweakfluxStyles', function (): string {
            return '<?php
                $__tweakfluxPath = config(\'tweakflux.output_path\');
                if ($__tweakfluxPath && file_exists($__tweakfluxPath)) {
                    $__tweakfluxHref = asset(str_replace(public_path(), \'\', $__tweakfluxPath));
                    $__tweakfluxVersion = filemtime($__tweakfluxPath);
                    echo \'<link rel="stylesheet" href="\' . e($__tweakfluxHref) . \'?v=\' . $__tweakfluxVersion . \'">\';
                }
            ?>';
        });
    }
}
