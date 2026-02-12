<?php

declare(strict_types=1);

namespace TweakFlux\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

final class UpdateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('update')
            ->setDescription('Update TweakFlux to the latest version');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        info('Updating TweakFlux...');

        $result = spin(
            callback: function (): array {
                $output = [];
                $exitCode = 0;

                exec('composer global update joshcirre/tweakflux 2>&1', $output, $exitCode);

                return ['output' => implode("\n", $output), 'exitCode' => $exitCode];
            },
            message: 'Updating via Composer...',
        );

        if ($result['exitCode'] !== 0) {
            $output->writeln('<error>Update failed:</error>');
            $output->writeln($result['output']);

            return Command::FAILURE;
        }

        info('TweakFlux updated successfully!');

        return Command::SUCCESS;
    }
}
