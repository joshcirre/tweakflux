<?php

declare(strict_types=1);

namespace TweakFlux\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;
use TweakFlux\Actions\GenerateThemeCss;
use TweakFlux\Actions\GetTheme;

final class TweakFluxApply extends Command
{
    protected $signature = 'tweakflux:apply {theme? : The theme name to apply}';

    protected $description = 'Generate the TweakFlux theme CSS file';

    public function handle(GetTheme $getTheme, GenerateThemeCss $generateCss): int
    {
        /** @var string $themeName */
        $themeName = $this->argument('theme') ?? config('tweakflux.active_theme', 'default');

        try {
            $theme = $getTheme($themeName);
        } catch (RuntimeException $runtimeException) {
            $this->error($runtimeException->getMessage());

            return self::FAILURE;
        }

        $css = $generateCss($theme);

        /** @var string $outputPath */
        $outputPath = config('tweakflux.output_path');

        File::ensureDirectoryExists(dirname($outputPath));
        File::put($outputPath, $css);

        $this->info(sprintf('Theme "%s" applied to %s', $themeName, $outputPath));

        return self::SUCCESS;
    }
}
