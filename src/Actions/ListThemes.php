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
        /** @var string $themesPath */
        $themesPath = config('tweakflux.themes_path');

        if (! File::isDirectory($themesPath)) {
            return [];
        }

        $themes = [];

        /** @var list<string> $files */
        $files = File::glob($themesPath.'/*.json');

        foreach ($files as $file) {
            /** @var array<string, mixed> $data */
            $data = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);

            $description = $data['description'] ?? '';

            $themes[] = [
                'name' => pathinfo($file, PATHINFO_FILENAME),
                'file' => basename($file),
                'description' => is_string($description) ? $description : '',
            ];
        }

        return $themes;
    }
}
