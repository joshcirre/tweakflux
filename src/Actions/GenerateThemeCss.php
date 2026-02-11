<?php

declare(strict_types=1);

namespace TweakFlux\Actions;

final class GenerateThemeCss
{
    /**
     * Generate a CSS string from a merged theme array.
     *
     * @param  array<string, mixed>  $theme
     */
    public function __invoke(array $theme): string
    {
        $lines = [];

        // Font @import URLs first
        $lines = array_merge($lines, $this->generateFontImports($theme));

        /** @var array<string, mixed> $light */
        $light = $theme['light'] ?? [];
        /** @var array<string, mixed> $dark */
        $dark = $theme['dark'] ?? [];

        // :root block (light mode + global overrides)
        $rootVars = array_merge(
            $this->generateFontVars($theme),
            $this->generateAccentVars($light),
            $this->generateZincVars($light),
            $this->generateSemanticVars($light),
            $this->generateRadiusVars($theme),
            $this->generateShadowVars($theme),
            $this->generateSpacingVar($theme),
        );

        if ($rootVars !== []) {
            $lines[] = ':root {';
            foreach ($rootVars as $var => $value) {
                $lines[] = sprintf('    %s: %s;', $var, $value);
            }

            $lines[] = '}';
            $lines[] = '';
        }

        // Dark mode block — matches Flux's @layer theme { .dark { } } pattern
        $darkVars = array_merge(
            $this->generateAccentVars($dark),
            $this->generateZincVars($dark),
            $this->generateSemanticVars($dark),
        );

        if ($darkVars !== []) {
            $lines[] = '@layer theme {';
            $lines[] = '    .dark {';
            foreach ($darkVars as $var => $value) {
                $lines[] = sprintf('        %s: %s;', $var, $value);
            }

            $lines[] = '    }';
            $lines[] = '}';
            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $theme
     * @return list<string>
     */
    private function generateFontImports(array $theme): array
    {
        $imports = [];

        /** @var array<string, mixed> $fonts */
        $fonts = $theme['fonts'] ?? [];
        /** @var array<int, string> $urls */
        $urls = $fonts['urls'] ?? [];

        foreach ($urls as $url) {
            $imports[] = sprintf('@import url("%s");', $url);
        }

        if ($imports !== []) {
            $imports[] = '';
        }

        return $imports;
    }

    /**
     * @param  array<string, mixed>  $theme
     * @return array<string, string>
     */
    private function generateFontVars(array $theme): array
    {
        $vars = [];

        /** @var array<string, string|null> $fonts */
        $fonts = $theme['fonts'] ?? [];

        foreach (['sans', 'mono', 'serif'] as $family) {
            $value = $fonts[$family] ?? null;

            if (is_string($value) && $value !== '') {
                $vars['--font-'.$family] = $value;
            }
        }

        return $vars;
    }

    /**
     * @param  array<string, mixed>  $modeTheme
     * @return array<string, string>
     */
    private function generateAccentVars(array $modeTheme): array
    {
        $vars = [];

        foreach (['accent', 'accent-content', 'accent-foreground'] as $key) {
            $value = $modeTheme[$key] ?? null;

            if (is_string($value) && $value !== '') {
                $vars['--color-'.$key] = $value;
            }
        }

        return $vars;
    }

    /**
     * @param  array<string, mixed>  $modeTheme
     * @return array<string, string>
     */
    private function generateZincVars(array $modeTheme): array
    {
        $vars = [];

        /** @var array<string, string|null> $zinc */
        $zinc = $modeTheme['zinc'] ?? [];

        foreach ($zinc as $shade => $value) {
            if ($value !== null) {
                $vars['--color-zinc-'.$shade] = $value;
            }
        }

        return $vars;
    }

    /**
     * @param  array<string, mixed>  $modeTheme
     * @return array<string, string>
     */
    private function generateSemanticVars(array $modeTheme): array
    {
        $vars = [];

        /** @var array<string, array<string, string|null>|mixed> $semantic */
        $semantic = $modeTheme['semantic'] ?? [];

        foreach ($semantic as $color => $shades) {
            if (! is_array($shades)) {
                continue;
            }

            foreach ($shades as $shade => $value) {
                if (is_string($value)) {
                    $vars[sprintf('--color-%s-%s', $color, $shade)] = $value;
                }
            }
        }

        return $vars;
    }

    /**
     * @param  array<string, mixed>  $theme
     * @return array<string, string>
     */
    private function generateRadiusVars(array $theme): array
    {
        $vars = [];

        /** @var array<string, string|null> $radius */
        $radius = $theme['radius'] ?? [];

        foreach ($radius as $size => $value) {
            if ($value !== null) {
                $vars['--radius-'.$size] = $value;
            }
        }

        return $vars;
    }

    /**
     * @param  array<string, mixed>  $theme
     * @return array<string, string>
     */
    private function generateShadowVars(array $theme): array
    {
        $vars = [];

        /** @var array<string, string|null> $shadows */
        $shadows = $theme['shadows'] ?? [];

        foreach ($shadows as $size => $value) {
            if ($value === null) {
                continue;
            }

            $varName = $size === 'DEFAULT' ? '--shadow' : '--shadow-'.$size;
            $vars[$varName] = $value;
        }

        return $vars;
    }

    /**
     * @param  array<string, mixed>  $theme
     * @return array<string, string>
     */
    private function generateSpacingVar(array $theme): array
    {
        $spacing = $theme['spacing'] ?? null;

        if (! is_string($spacing) || $spacing === '') {
            return [];
        }

        return ['--spacing' => $spacing];
    }
}
