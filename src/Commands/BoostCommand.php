<?php

declare(strict_types=1);

namespace TweakFlux\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

final class BoostCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('boost')
            ->setDescription('Copy TweakFlux Boost guidelines and skills into your project');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cwd = (string) getcwd();
        $source = __DIR__.'/../../resources/boost';
        $destination = $cwd.'/resources/boost';

        if (! is_dir($source)) {
            warning('Boost resources not found in the TweakFlux package.');

            return Command::FAILURE;
        }

        $copied = 0;
        $skipped = 0;

        $this->copyDirectory($source, $destination, $copied, $skipped);

        if ($copied === 0 && $skipped > 0) {
            info(sprintf('Boost files are up to date (%d files already current).', $skipped));
        } elseif ($copied > 0) {
            info(sprintf('Copied %d Boost file(s) to %s', $copied, $destination));

            if ($skipped > 0) {
                info(sprintf('  (%d file(s) already up to date)', $skipped));
            }
        }

        return Command::SUCCESS;
    }

    private function copyDirectory(string $source, string $destination, int &$copied, int &$skipped): void
    {
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
