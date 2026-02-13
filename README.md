# TweakFlux

Deep theming for [Flux UI](https://fluxui.dev). Override Tailwind v4 CSS custom properties to transform every Flux component — free and pro — with a single command. Zero vendor files touched.

## Installation

### Global (recommended)

```bash
composer global require joshcirre/tweakflux
```

Then run from any project:

```bash
tweakflux apply bubblegum
```

### Per-project

```bash
composer require joshcirre/tweakflux --dev
```

Then run via vendor bin:

```bash
./vendor/bin/tweakflux apply bubblegum
```

The `apply` command generates the theme CSS at `resources/css/tweakflux-theme.css` and automatically adds the import to your `resources/css/app.css`. With Vite running, you'll see the changes instantly.

If `app.css` doesn't exist, the command prints the import and font URLs for you to add manually.

## Commands

| Command | Description |
|---------|-------------|
| `tweakflux apply {theme?}` | Apply a theme (interactive picker if no name given) |
| `tweakflux apply {theme} --no-effects` | Apply a theme without visual effects (if applicable) |
| `tweakflux list` | List all available themes |
| `tweakflux create {name}` | Scaffold a new theme JSON file |
| `tweakflux boost` | Copy Boost guidelines and skills into your project |

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
| **Laravel** | Boxy cards, pure neutral grays, and the iconic red — modeled after laravel.com |
| **Neon** | Hackerman terminal aesthetic with vivid green glow effects on dark backgrounds |
| **Nord** | Arctic-inspired muted blue-gray palette with clean, minimal aesthetics |
| **Ocean** | Cool blue-tinted slate palette with sky accents |
| **Perpetuity** | Monospace teal aesthetic with tight shadows and minimal rounding |
| **Posty** | PostHog-inspired raised buttons, warm creams, and amber accents |
| **Posty Charcoal** | Muted slate Posty variant with raised buttons, cool grays, and violet-slate accents |
| **Posty Fresh** | Mint green Posty variant with raised buttons, fresh neutrals, and emerald accents |
| **Posty Ice** | Cool blue Posty variant with raised buttons, icy neutrals, and sky blue accents |
| **Retro** | Warm vintage parchment tones with salmon and sage green accents |
| **Sunset** | Warm coral and orange tones inspired by golden hour horizons |
| **Synthwave** | Neon 80s retrowave with hot pink accents on deep purple backgrounds |

## Creating Your Own Theme

```bash
tweakflux create my-theme
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

## Effects

Some themes include visual effects like glows and animations. These are stored in the `effects` field (separate from `css`) so users can toggle them off.

When applying a theme with effects, the CLI will ask if you'd like to disable them. You can also skip the prompt:

```bash
# Apply with effects (default)
tweakflux apply neon

# Apply without effects
tweakflux apply neon --no-effects
```

When creating a theme, use `effects` for decorative CSS that users might want to disable (hover glows, animated borders) and `css` for structural CSS that is essential to the theme's identity (raised buttons, custom borders).

```json
{
    "css": null,
    "effects": "/* Glow on button hover */\n[data-flux-button]:hover {\n    box-shadow: 0 0 12px oklch(0.83 0.28 142 / 0.3);\n}"
}
```

## Path Conventions

All paths are relative to your project root (cwd):

| Path | Purpose |
|------|---------|
| `./resources/themes/` | User theme JSON files |
| `./resources/css/tweakflux-theme.css` | Generated CSS output |
| `./resources/css/app.css` | CSS entry point (for import injection) |

## Using with Flux's Built-in Theming

TweakFlux replaces Flux's manual `@theme` theming approach. If you already have `@theme` or `@layer theme` blocks in your `app.css` for Flux colors/accents, TweakFlux will override them — the import is appended to the end of your CSS file so it always takes precedence.

You can safely remove any existing Flux `@theme` color blocks from `app.css` once you're using TweakFlux, since TweakFlux manages the same variables through its JSON themes.

Published Flux components (`php artisan flux:publish`) work seamlessly — they read the same CSS custom properties that TweakFlux overrides.

## How It Works

Flux UI components resolve their styles through Tailwind v4 CSS custom properties. TweakFlux generates a stylesheet that overrides these properties at `:root` (and `.dark` for dark mode), so every component picks up the changes natively — no patches, no vendor modifications.

The generated CSS uses Flux's own `@layer theme` pattern for dark mode, ensuring proper specificity and compatibility.

## What You Can Override

- **Fonts** — sans, mono, serif families + Google Fonts URLs
- **Colors** — accent, accent-content, accent-foreground, full zinc palette, semantic colors
- **Radius** — sm, md, lg, xl, 2xl
- **Shadows** — 2xs through 2xl
- **Spacing** — base spacing unit
- **Dark mode** — separate light/dark palettes per theme
- **Custom CSS** — structural CSS always included (e.g., button shapes, layout overrides via `[data-flux-button]` selectors)
- **Effects** — toggleable visual effects (glows, animations) that users can disable with `--no-effects`

## AI Theme Generation

TweakFlux ships with AI guidelines and a skill for [Laravel Boost](https://laravel.com/docs/boost) that lets your coding agent generate themes from descriptions, color palettes, screenshots, or brand guidelines.

Run `tweakflux boost` to copy the guidelines and skills into your project, then ask your AI agent things like:

- "Create a TweakFlux theme inspired by Spotify"
- "Generate a theme from this color palette: #1a1a2e, #16213e, #0f3460, #e94560"
- "Make a warm earth-tones theme with serif fonts"

The agent will generate a valid theme JSON file and apply it for you.

## Requirements

- PHP 8.2+
- Flux UI (free or pro)
- Tailwind CSS v4

## License

MIT
