<?php

declare(strict_types=1);

namespace TweakFlux\Commands;

use Illuminate\Console\Command;
use TweakFlux\Actions\ListThemes;

final class TweakFluxList extends Command
{
    protected $signature = 'tweakflux:list';

    protected $description = 'List all available TweakFlux themes';

    public function handle(ListThemes $listThemes): int
    {
        $themes = $listThemes();

        if ($themes === []) {
            $this->warn('No themes found.');

            return self::SUCCESS;
        }

        $activeTheme = config('tweakflux.active_theme', 'default');

        $rows = array_map(fn (array $theme): array => [
            $theme['name'] === $activeTheme ? '●' : '',
            $theme['name'],
            $theme['file'],
            $theme['description'],
        ], $themes);

        $this->table(['', 'Name', 'File', 'Description'], $rows);

        return self::SUCCESS;
    }
}
