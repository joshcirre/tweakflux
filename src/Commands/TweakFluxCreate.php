<?php

declare(strict_types=1);

namespace TweakFlux\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\text;

final class TweakFluxCreate extends Command
{
    protected $signature = 'tweakflux:create {name : The theme slug (e.g. my-theme)}';

    protected $description = 'Scaffold a new TweakFlux theme JSON file';

    public function handle(): int
    {
        $slug = Str::slug((string) $this->argument('name'));

        /** @var string $themesPath */
        $themesPath = config('tweakflux.themes_path');

        $filePath = $themesPath.'/'.$slug.'.json';

        if (File::exists($filePath)) {
            $this->error('Theme file already exists: '.$filePath);

            return self::FAILURE;
        }

        $displayName = text(
            label: 'Theme display name',
            default: Str::title(str_replace('-', ' ', $slug)),
            required: true,
        );

        $description = text(
            label: 'Theme description',
            default: '',
        );

        $skeleton = [
            'name' => $displayName,
            'description' => $description,
            'fonts' => [
                'sans' => null,
                'mono' => null,
                'serif' => null,
                'urls' => [],
            ],
            'light' => [
                'accent' => null,
                'accent-content' => null,
                'accent-foreground' => null,
                'zinc' => array_fill_keys(['50', '100', '200', '300', '400', '500', '600', '700', '800', '900', '950'], null),
                'semantic' => (object) [],
            ],
            'dark' => [
                'accent' => null,
                'accent-content' => null,
                'accent-foreground' => null,
                'zinc' => array_fill_keys(['50', '100', '200', '300', '400', '500', '600', '700', '800', '900', '950'], null),
                'semantic' => (object) [],
            ],
            'radius' => array_fill_keys(['sm', 'md', 'lg', 'xl', '2xl'], null),
            'shadows' => array_fill_keys(['2xs', 'xs', 'sm', 'DEFAULT', 'md', 'lg', 'xl', '2xl'], null),
            'spacing' => null,
        ];

        File::ensureDirectoryExists($themesPath);
        File::put($filePath, json_encode($skeleton, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

        $this->info('Created theme: '.$filePath);

        return self::SUCCESS;
    }
}
