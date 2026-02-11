# TweakFlux

Deep theming for [Flux UI](https://fluxui.dev). Override Tailwind v4 CSS custom properties to transform every Flux component — free and pro — with a single command. Zero vendor files touched.

## Installation

```bash
composer require joshcirre/tweakflux
php artisan tweakflux:apply bubblegum
```

That's it. The `apply` command generates the theme CSS and automatically adds the import to your `resources/css/app.css`. With Vite running, you'll see the changes instantly.

Optionally publish the config to customize paths:

```bash
php artisan vendor:publish --tag=tweakflux-config
```

### Troubleshooting

If the styles aren't loading, you can manually add `@tweakfluxStyles` to your layout's `<head>` as a fallback:

```blade
<head>
    ...
    @tweakfluxStyles
</head>
```

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
| **Catppuccin** | Soothing pastel purple and lavender tones from the popular dev theme |
| **Claude** | Warm terra cotta and cream tones inspired by Anthropic's Claude |
| **Coffee** | Warm brown and gold tones with a cozy coffeehouse feel |
| **Dracula** | The iconic dark palette with vibrant pastels on deep purple-gray backgrounds |
| **Forest** | Warm earthy greens with stone-tinted neutrals and muted shadows |
| **Nord** | Arctic-inspired muted blue-gray palette with clean, minimal aesthetics |
| **Ocean** | Cool blue-tinted slate palette with sky accents |
| **Retro** | Warm vintage parchment tones with salmon and sage green accents |
| **Sunset** | Warm coral and orange tones inspired by golden hour horizons |
| **Synthwave** | Neon 80s retrowave with hot pink accents on deep purple backgrounds |

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

## Using with Flux's Built-in Theming

TweakFlux replaces Flux's manual `@theme` theming approach. If you already have `@theme` or `@layer theme` blocks in your `app.css` for Flux colors/accents, TweakFlux will override them — the import is appended to the end of your CSS file so it always takes precedence.

You can safely remove any existing Flux `@theme` color blocks from `app.css` once you're using TweakFlux, since TweakFlux manages the same variables through its JSON themes.

Published Flux components (`php artisan flux:publish`) work seamlessly — they read the same CSS custom properties that TweakFlux overrides.

## How It Works

Flux UI components resolve their styles through Tailwind v4 CSS custom properties. TweakFlux generates a stylesheet that overrides these properties at `:root` (and `.dark` for dark mode), so every component picks up the changes natively — no patches, no vendor modifications.

The generated CSS uses Flux's own `@layer theme` pattern for dark mode, ensuring proper specificity and compatibility.

## Configuration

Published to `config/tweakflux.php`:

```php
return [
    'active_theme' => env('TWEAKFLUX_THEME', 'default'),
    'themes_path' => resource_path('themes'),
    'output_path' => resource_path('css/tweakflux-theme.css'),
    'css_entry_point' => resource_path('css/app.css'),
];
```

## What You Can Override

- **Fonts** — sans, mono, serif families + Google Fonts URLs
- **Colors** — accent, accent-content, accent-foreground, full zinc palette, semantic colors
- **Radius** — sm, md, lg, xl, 2xl
- **Shadows** — 2xs through 2xl
- **Spacing** — base spacing unit
- **Dark mode** — separate light/dark palettes per theme

## AI Theme Generation

TweakFlux ships with an AI skill for [Laravel Boost](https://laravel.com/docs/boost) that lets your coding agent generate themes from descriptions, color palettes, screenshots, or brand guidelines.

After running `php artisan boost:install`, Boost will automatically discover and offer to install the TweakFlux skill. Then you can ask your AI agent things like:

- "Create a TweakFlux theme inspired by Spotify"
- "Generate a theme from this color palette: #1a1a2e, #16213e, #0f3460, #e94560"
- "Make a warm earth-tones theme with serif fonts"

The agent will generate a valid theme JSON file and apply it for you.

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Flux UI (free or pro)
- Tailwind CSS v4

## License

MIT
