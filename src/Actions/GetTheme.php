<?php

declare(strict_types=1);

namespace TweakFlux\Actions;

use RuntimeException;

final class GetTheme
{
    public function __construct(
        private readonly string $userThemesPath,
    ) {}

    /**
     * Load a theme by name, deep-merged onto the default theme.
     *
     * @return array<string, mixed>
     */
    public function __invoke(string $name = 'default'): array
    {
        $defaultPath = $this->resolveThemePath('default');

        if ($defaultPath === null) {
            throw new RuntimeException('Default theme file not found.');
        }

        /** @var array<string, mixed> $default */
        $default = json_decode((string) file_get_contents($defaultPath), true, 512, JSON_THROW_ON_ERROR);

        if ($name === 'default') {
            return $default;
        }

        $themePath = $this->resolveThemePath($name);

        if ($themePath === null) {
            throw new RuntimeException('Theme not found: '.$name);
        }

        /** @var array<string, mixed> $theme */
        $theme = json_decode((string) file_get_contents($themePath), true, 512, JSON_THROW_ON_ERROR);

        return $this->deepMerge($default, $theme);
    }

    /**
     * Resolve theme file path, checking user themes first then package presets.
     */
    private function resolveThemePath(string $name): ?string
    {
        $userPath = $this->userThemesPath.'/'.$name.'.json';

        if (file_exists($userPath)) {
            return $userPath;
        }

        $packagePath = __DIR__.'/../../resources/themes/'.$name.'.json';

        if (file_exists($packagePath)) {
            return $packagePath;
        }

        return null;
    }

    /**
     * Recursively merge theme arrays. Non-null values in $override replace $base values.
     *
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $override
     * @return array<string, mixed>
     */
    private function deepMerge(array $base, array $override): array
    {
        foreach ($override as $key => $value) {
            if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                /** @var array<string, mixed> $baseValue */
                $baseValue = $base[$key];
                /** @var array<string, mixed> $overrideValue */
                $overrideValue = $value;
                $base[$key] = $this->deepMerge($baseValue, $overrideValue);
            } else {
                $base[$key] = $value;
            }
        }

        return $base;
    }
}
