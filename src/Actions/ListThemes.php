<?php

declare(strict_types=1);

namespace TweakFlux\Actions;

final class ListThemes
{
    public function __construct(
        private readonly string $userThemesPath,
    ) {}

    /**
     * Discover all theme JSON files and return their metadata.
     *
     * @return list<array{name: string, file: string, description: string}>
     */
    public function __invoke(): array
    {
        $themes = [];
        $seen = [];

        // User themes take priority
        $this->collectThemes($this->userThemesPath, $themes, $seen);

        // Then package presets
        $packagePath = __DIR__.'/../../resources/themes';

        $this->collectThemes($packagePath, $themes, $seen);

        return $themes;
    }

    /**
     * @param  list<array{name: string, file: string, description: string}>  $themes
     * @param  array<string, true>  $seen
     */
    private function collectThemes(string $path, array &$themes, array &$seen): void
    {
        if (! is_dir($path)) {
            return;
        }

        $files = glob($path.'/*.json');

        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);

            if (isset($seen[$name])) {
                continue;
            }

            $contents = file_get_contents($file);

            if ($contents === false) {
                continue;
            }

            /** @var array<string, mixed> $data */
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

            $description = $data['description'] ?? '';

            $themes[] = [
                'name' => $name,
                'file' => basename($file),
                'description' => is_string($description) ? $description : '',
            ];

            $seen[$name] = true;
        }
    }
}
