<?php

declare(strict_types=1);

namespace TweakFlux\Commands;

use Illuminate\Console\Command;
use TweakFlux\Actions\ListThemes;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

final class TweakFluxList extends Command
{
    protected $signature = 'tweakflux:list';

    protected $description = 'List all available TweakFlux themes';

    public function handle(ListThemes $listThemes): int
    {
        $themes = $listThemes();

        if ($themes === []) {
            info('No themes found.');

            return self::SUCCESS;
        }

        /** @var string $activeTheme */
        $activeTheme = config('tweakflux.active_theme', 'default');

        $rows = array_map(fn (array $theme): array => [
            $theme['name'] === $activeTheme ? ' ●' : '',
            $theme['name'],
            $theme['description'],
        ], $themes);

        table(
            headers: ['', 'Theme', 'Description'],
            rows: $rows,
        );

        return self::SUCCESS;
    }
}
