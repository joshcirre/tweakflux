<?php

declare(strict_types=1);

namespace TweakFlux\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;
use TweakFlux\Actions\GenerateThemeCss;
use TweakFlux\Actions\GetTheme;
use TweakFlux\Actions\ListThemes;

use function Laravel\Prompts\info;
use function Laravel\Prompts\select;

final class TweakFluxApply extends Command
{
    protected $signature = 'tweakflux:apply {theme? : The theme name to apply}';

    protected $description = 'Generate the TweakFlux theme CSS file';

    private const IMPORT_STATEMENT = '@import "./tweakflux-theme.css";';

    private const FONTS_START = '/* tweakflux:fonts:start */';

    private const FONTS_END = '/* tweakflux:fonts:end */';

    public function handle(GetTheme $getTheme, GenerateThemeCss $generateCss, ListThemes $listThemes): int
    {
        /** @var string|null $themeName */
        $themeName = $this->argument('theme');

        if ($themeName === null) {
            $themes = $listThemes();

            if ($themes === []) {
                info('No themes found.');

                return self::FAILURE;
            }

            $options = [];
            foreach ($themes as $theme) {
                $options[$theme['name']] = $theme['name'].' — '.$theme['description'];
            }

            /** @var string $themeName */
            $themeName = select(
                label: 'Which theme would you like to apply?',
                options: $options,
                default: config('tweakflux.active_theme', 'default'),
            );
        }

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

        info(sprintf('Theme "%s" applied to %s', $themeName, $outputPath));

        $this->injectImport();
        $this->injectFontImports($theme);

        return self::SUCCESS;
    }

    private function injectImport(): void
    {
        /** @var string $entryPoint */
        $entryPoint = config('tweakflux.css_entry_point');

        if (! File::exists($entryPoint)) {
            return;
        }

        $contents = File::get($entryPoint);

        if (str_contains($contents, self::IMPORT_STATEMENT)) {
            return;
        }

        File::put($entryPoint, $contents."\n".self::IMPORT_STATEMENT."\n");

        info('Added TweakFlux import to '.basename($entryPoint));
    }

    /**
     * Inject font @import URLs at the top of app.css (CSS spec requires @import before other rules).
     *
     * @param  array<string, mixed>  $theme
     */
    private function injectFontImports(array $theme): void
    {
        /** @var string $entryPoint */
        $entryPoint = config('tweakflux.css_entry_point');

        if (! File::exists($entryPoint)) {
            return;
        }

        $contents = File::get($entryPoint);

        // Strip existing TweakFlux font block
        $pattern = '/'.preg_quote(self::FONTS_START, '/').'.*?'.preg_quote(self::FONTS_END, '/').'\n?/s';
        $contents = preg_replace($pattern, '', $contents) ?? $contents;

        // Build new font import block
        /** @var array<string, mixed> $fonts */
        $fonts = $theme['fonts'] ?? [];
        /** @var array<int, string> $urls */
        $urls = $fonts['urls'] ?? [];

        if ($urls === []) {
            File::put($entryPoint, $contents);

            return;
        }

        $fontBlock = self::FONTS_START."\n";
        foreach ($urls as $url) {
            $fontBlock .= sprintf('@import url("%s");', $url)."\n";
        }
        $fontBlock .= self::FONTS_END."\n";

        // Prepend font imports at the very top
        File::put($entryPoint, $fontBlock.$contents);
    }
}
