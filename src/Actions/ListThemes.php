<?php

declare(strict_types=1);

namespace TweakFlux\Actions;

use Illuminate\Support\Facades\File;

final class ListThemes
{
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
        /** @var string $themesPath */
        $themesPath = config('tweakflux.themes_path');

        $this->collectThemes($themesPath, $themes, $seen);

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
        if (! File::isDirectory($path)) {
            return;
        }

        /** @var list<string> $files */
        $files = File::glob($path.'/*.json');

        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);

            if (isset($seen[$name])) {
                continue;
            }

            /** @var array<string, mixed> $data */
            $data = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);

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
