<?php

declare(strict_types=1);

namespace TweakFlux\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

final class BoostCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('boost')
            ->setDescription('Install TweakFlux guidelines and skills for Laravel Boost');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cwd = (string) getcwd();
        $boostSource = __DIR__.'/../../resources/boost';

        if (! is_dir($boostSource)) {
            warning('Boost resources not found in the TweakFlux package.');

            return Command::FAILURE;
        }

        $copied = 0;
        $skipped = 0;

        // Guidelines → .ai/guidelines/tweakflux/
        $this->copyDirectory(
            $boostSource.'/guidelines',
            $cwd.'/.ai/guidelines/tweakflux',
            $copied,
            $skipped,
        );

        // Skills → .ai/skills/ (preserving subdirectory names)
        $this->copyDirectory(
            $boostSource.'/skills',
            $cwd.'/.ai/skills',
            $copied,
            $skipped,
        );

        if ($copied === 0 && $skipped > 0) {
            info(sprintf('Boost files are up to date (%d files already current).', $skipped));
        } elseif ($copied > 0) {
            info(sprintf('Installed %d Boost file(s) to .ai/', $copied));

            if ($skipped > 0) {
                info(sprintf('  (%d file(s) already up to date)', $skipped));
            }
        }

        $this->runBoostUpdate($cwd);

        return Command::SUCCESS;
    }

    private function runBoostUpdate(string $cwd): void
    {
        $artisan = $cwd.'/artisan';

        if (! file_exists($artisan)) {
            return;
        }

        $process = new Process(['php', 'artisan', 'boost:update'], $cwd);
        $process->setTimeout(30);

        try {
            $process->run();

            if ($process->isSuccessful()) {
                info('Ran boost:update to register guidelines and skills.');
            }
        } catch (\Throwable) {
            // Boost may not be installed — that's fine
        }
    }

    private function copyDirectory(string $source, string $destination, int &$copied, int &$skipped): void
    {
        if (! is_dir($source)) {
            return;
        }

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $items = (array) scandir($source);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $srcPath = $source.'/'.$item;
            $destPath = $destination.'/'.$item;

            if (is_dir($srcPath)) {
                $this->copyDirectory($srcPath, $destPath, $copied, $skipped);

                continue;
            }

            // Only overwrite if package version is newer
            if (file_exists($destPath) && filemtime($destPath) >= filemtime($srcPath)) {
                $skipped++;

                continue;
            }

            copy($srcPath, $destPath);
            $copied++;
        }
    }
}
