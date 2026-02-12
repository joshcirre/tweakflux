<?php

declare(strict_types=1);

namespace TweakFlux\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TweakFlux\Actions\ListThemes;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

final class ListCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('themes')
            ->setDescription('List all available TweakFlux themes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $themesPath = getcwd().'/resources/themes';
        $listThemes = new ListThemes($themesPath);

        $themes = $listThemes();

        if ($themes === []) {
            info('No themes found.');

            return Command::SUCCESS;
        }

        $rows = array_map(fn (array $theme): array => [
            $theme['name'],
            $theme['description'],
        ], $themes);

        table(
            headers: ['Theme', 'Description'],
            rows: $rows,
        );

        return Command::SUCCESS;
    }
}
