<?php

declare(strict_types=1);

namespace TweakFlux\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\text;

final class CreateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('create')
            ->setDescription('Scaffold a new TweakFlux theme JSON file')
            ->addArgument('name', InputArgument::REQUIRED, 'The theme slug (e.g. my-theme)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $slug = $this->slugify((string) $input->getArgument('name'));
        $themesPath = getcwd().'/resources/themes';
        $filePath = $themesPath.'/'.$slug.'.json';

        if (file_exists($filePath)) {
            error('Theme file already exists: '.$filePath);

            return Command::FAILURE;
        }

        $displayName = text(
            label: 'Theme display name',
            default: ucwords(str_replace('-', ' ', $slug)),
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

        if (! is_dir($themesPath)) {
            mkdir($themesPath, 0755, true);
        }

        file_put_contents($filePath, json_encode($skeleton, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

        info('Created theme: '.$filePath);

        return Command::SUCCESS;
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = (string) preg_replace('/[^a-z0-9-]/', '-', $value);

        return (string) preg_replace('/-+/', '-', trim($value, '-'));
    }
}
