# TweakFlux

Deep theming for [Flux UI](https://fluxui.dev). Override Tailwind v4 CSS custom properties to transform every Flux component — free and pro — with a single command. Zero vendor files touched.

## Installation

```bash
composer require joshcirre/tweakflux
```

Publish the config and preset themes:

```bash
php artisan vendor:publish --tag=tweakflux-config
php artisan vendor:publish --tag=tweakflux-themes
```

## Quick Start

Add `@tweakfluxStyles` to your layout's `<head>`:

```blade
<head>
    ...
    @tweakfluxStyles
</head>
```

Apply a preset theme:

```bash
php artisan tweakflux:apply bubblegum
```

That's it. Refresh your browser.

## Available Commands

| Command | Description |
|---------|-------------|
| `tweakflux:apply {theme}` | Generate the theme CSS file |
| `tweakflux:list` | List all available themes |
| `tweakflux:create {name}` | Scaffold a new theme JSON file |

## Preset Themes

| Theme | Description |
|-------|-------------|
| **Default** | Flux defaults — zinc neutrals, standard radius, Inter font |
| **Bubblegum** | Playful pink accents with warm rose-tinted neutrals and rounded corners |
| **Brutalist** | Sharp corners, hard shadows, high contrast monospace aesthetic |
| **Forest** | Warm earthy greens with stone-tinted neutrals and muted shadows |
| **Ocean** | Cool blue-tinted slate palette with sky accents |

## Creating Your Own Theme

```bash
php artisan tweakflux:create my-theme
```

This scaffolds a JSON file at `resources/themes/my-theme.json`. Set any value to `null` to keep the Flux default. Only override what you need.

```json
{
    "fonts": {
        "sans": "Quicksand, sans-serif",
        "urls": ["https://fonts.googleapis.com/css2?family=Quicksand:wght@400..700&display=swap"]
    },
    "light": {
        "accent": "oklch(0.65 0.24 350)"
    },
    "radius": {
        "lg": "1rem"
    }
}
```

## How It Works

Flux UI components resolve their styles through Tailwind v4 CSS custom properties. TweakFlux generates a stylesheet that overrides these properties at `:root` (and `.dark` for dark mode), so every component picks up the changes natively — no patches, no vendor modifications.

The generated CSS uses Flux's own `@layer theme` pattern for dark mode, ensuring proper specificity and compatibility.

## Configuration

Published to `config/tweakflux.php`:

```php
return [
    'active_theme' => env('TWEAKFLUX_THEME', 'default'),
    'themes_path' => resource_path('themes'),
    'output_path' => public_path('css/tweakflux-theme.css'),
];
```

## What You Can Override

- **Fonts** — sans, mono, serif families + Google Fonts URLs
- **Colors** — accent, accent-content, accent-foreground, full zinc palette, semantic colors
- **Radius** — sm, md, lg, xl, 2xl
- **Shadows** — 2xs through 2xl
- **Spacing** — base spacing unit
- **Dark mode** — separate light/dark palettes per theme

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Flux UI (free or pro)
- Tailwind CSS v4

## License

MIT
