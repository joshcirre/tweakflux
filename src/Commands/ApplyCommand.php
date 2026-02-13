<?php

declare(strict_types=1);

namespace TweakFlux\Commands;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TweakFlux\Actions\GenerateThemeCss;
use TweakFlux\Actions\GetTheme;
use TweakFlux\Actions\ListThemes;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\warning;

final class ApplyCommand extends Command
{
    private const IMPORT_STATEMENT = '@import "./tweakflux-theme.css";';

    private const FONTS_START = '/* tweakflux:fonts:start */';

    private const FONTS_END = '/* tweakflux:fonts:end */';

    protected function configure(): void
    {
        $this
            ->setName('apply')
            ->setDescription('Generate the TweakFlux theme CSS file (use --no-effects to disable visual effects)')
            ->addArgument('theme', InputArgument::OPTIONAL, 'The theme name to apply')
            ->addOption('no-effects', null, InputOption::VALUE_NONE, 'Exclude theme effects (glows, animations)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cwd = (string) getcwd();
        $themesPath = $cwd.'/resources/themes';
        $outputPath = $cwd.'/resources/css/tweakflux-theme.css';
        $entryPoint = $cwd.'/resources/css/app.css';

        /** @var string|null $themeName */
        $themeName = $input->getArgument('theme');

        // Handle remote URL — fetch theme JSON and save locally
        if (is_string($themeName) && str_starts_with($themeName, 'http')) {
            return $this->applyFromUrl($themeName, $themesPath, $outputPath, $entryPoint, $input);
        }

        if ($themeName === null) {
            $listThemes = new ListThemes($themesPath);
            $themes = $listThemes();

            if ($themes === []) {
                info('No themes found.');

                return Command::FAILURE;
            }

            $options = [];
            foreach ($themes as $theme) {
                $options[$theme['name']] = $theme['name'].' — '.$theme['description'];
            }

            /** @var string $themeName */
            $themeName = select(
                label: 'Which theme would you like to apply?',
                options: $options,
                default: 'default',
            );
        }

        $getTheme = new GetTheme($themesPath);
        $generateCss = new GenerateThemeCss();

        try {
            $theme = $getTheme($themeName);
        } catch (RuntimeException $e) {
            error($e->getMessage());

            return Command::FAILURE;
        }

        $includeEffects = ! $input->getOption('no-effects');

        $effectsCss = $theme['effects'] ?? null;
        $themeHasEffects = is_string($effectsCss) && $effectsCss !== '';

        if ($includeEffects && $themeHasEffects && ! $input->getOption('no-interaction')) {
            $disableEffects = confirm(
                label: 'This theme includes visual effects (glows, animations). Disable them?',
                default: false,
            );

            if ($disableEffects) {
                $includeEffects = false;
            }
        }

        $css = $generateCss($theme, $includeEffects);

        $outputDir = dirname($outputPath);

        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        file_put_contents($outputPath, $css);

        info(sprintf('Theme "%s" applied to %s', $themeName, $outputPath));

        $this->injectImport($entryPoint);
        $this->injectFontImports($entryPoint, $theme);

        return Command::SUCCESS;
    }

    private function injectImport(string $entryPoint): void
    {
        if (! file_exists($entryPoint)) {
            warning('Could not find '.basename($entryPoint).'. Add this import manually:');
            info('  '.self::IMPORT_STATEMENT);

            return;
        }

        $contents = (string) file_get_contents($entryPoint);

        if (str_contains($contents, self::IMPORT_STATEMENT)) {
            return;
        }

        file_put_contents($entryPoint, $contents."\n".self::IMPORT_STATEMENT."\n");

        info('Added TweakFlux import to '.basename($entryPoint));
    }

    private function applyFromUrl(string $url, string $themesPath, string $outputPath, string $entryPoint, InputInterface $input): int
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 15,
                'header' => "Accept: application/json\r\n",
            ],
        ]);

        $json = @file_get_contents($url, false, $context);

        if ($json === false) {
            error('Failed to fetch theme from: '.$url);

            return Command::FAILURE;
        }

        /** @var array<string, mixed>|null $themeData */
        $themeData = json_decode($json, true);

        if (! is_array($themeData)) {
            error('Invalid JSON returned from: '.$url);

            return Command::FAILURE;
        }

        $name = is_string($themeData['name'] ?? null) ? $themeData['name'] : 'remote-theme';
        $slug = mb_strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        if (! is_dir($themesPath)) {
            mkdir($themesPath, 0755, true);
        }

        $themeFile = $themesPath.'/'.$slug.'.json';
        file_put_contents($themeFile, json_encode($themeData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

        info(sprintf('Downloaded theme "%s" to %s', $name, $themeFile));

        // Now apply the downloaded theme using the standard flow
        $getTheme = new GetTheme($themesPath);
        $generateCss = new GenerateThemeCss();

        $theme = $getTheme($slug);
        $includeEffects = ! $input->getOption('no-effects');

        $effectsCss = $theme['effects'] ?? null;
        $themeHasEffects = is_string($effectsCss) && $effectsCss !== '';

        if ($includeEffects && $themeHasEffects && ! $input->getOption('no-interaction')) {
            $disableEffects = confirm(
                label: 'This theme includes visual effects (glows, animations). Disable them?',
                default: false,
            );

            if ($disableEffects) {
                $includeEffects = false;
            }
        }

        $css = $generateCss($theme, $includeEffects);

        $outputDir = dirname($outputPath);

        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        file_put_contents($outputPath, $css);

        info(sprintf('Theme "%s" applied to %s', $name, $outputPath));

        $this->injectImport($entryPoint);
        $this->injectFontImports($entryPoint, $theme);

        return Command::SUCCESS;
    }

    /**
     * Inject font @import URLs at the top of app.css (CSS spec requires @import before other rules).
     *
     * @param  array<string, mixed>  $theme
     */
    private function injectFontImports(string $entryPoint, array $theme): void
    {
        if (! file_exists($entryPoint)) {
            /** @var array<string, mixed> $fonts */
            $fonts = $theme['fonts'] ?? [];
            /** @var array<int, string> $urls */
            $urls = $fonts['urls'] ?? [];

            if ($urls !== []) {
                warning('Add these font imports to the top of your CSS entry point:');
                foreach ($urls as $url) {
                    info(sprintf('  @import url("%s");', $url));
                }
            }

            return;
        }

        $contents = (string) file_get_contents($entryPoint);

        // Strip existing TweakFlux font block
        $pattern = '/'.preg_quote(self::FONTS_START, '/').'.*?'.preg_quote(self::FONTS_END, '/').'\n?/s';
        $contents = preg_replace($pattern, '', $contents) ?? $contents;

        // Build new font import block
        /** @var array<string, mixed> $fonts */
        $fonts = $theme['fonts'] ?? [];
        /** @var array<int, string> $urls */
        $urls = $fonts['urls'] ?? [];

        if ($urls === []) {
            file_put_contents($entryPoint, $contents);

            return;
        }

        $fontBlock = self::FONTS_START."\n";
        foreach ($urls as $url) {
            $fontBlock .= sprintf('@import url("%s");', $url)."\n";
        }
        $fontBlock .= self::FONTS_END."\n";

        // Prepend font imports at the very top
        file_put_contents($entryPoint, $fontBlock.$contents);
    }
}
