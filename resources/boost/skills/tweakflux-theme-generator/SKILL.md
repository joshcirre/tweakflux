---
name: tweakflux-theme-generator
description: Generate TweakFlux themes for Flux UI from descriptions, color palettes, screenshots, or brand guidelines.
---

# TweakFlux Theme Generator

You are an expert at generating TweakFlux themes for Flux UI. When the user asks you to create a theme — whether from a description, color palette, screenshot, brand guidelines, or any visual reference — generate a valid TweakFlux theme JSON file.

## Theme JSON Schema

Every theme is a JSON file saved to `resources/themes/{slug}.json`. Here is the complete schema with all available fields:

```json
{
    "name": "Display Name",
    "description": "Short description of the theme",
    "fonts": {
        "sans": "Font Family, sans-serif",
        "mono": "Mono Font, monospace",
        "serif": "Serif Font, serif",
        "urls": [
            "https://fonts.googleapis.com/css2?family=Font+Family:wght@400..700&display=swap"
        ]
    },
    "light": {
        "accent": "oklch(L C H)",
        "accent-content": "oklch(L C H)",
        "accent-foreground": "oklch(L C H)",
        "zinc": {
            "50": "oklch(L C H)",
            "100": "oklch(L C H)",
            "200": "oklch(L C H)",
            "300": "oklch(L C H)",
            "400": "oklch(L C H)",
            "500": "oklch(L C H)",
            "600": "oklch(L C H)",
            "700": "oklch(L C H)",
            "800": "oklch(L C H)",
            "900": "oklch(L C H)",
            "950": "oklch(L C H)"
        },
        "semantic": {}
    },
    "dark": {
        "accent": "oklch(L C H)",
        "accent-content": "oklch(L C H)",
        "accent-foreground": "oklch(L C H)",
        "zinc": {
            "50": "oklch(L C H)",
            "100": "oklch(L C H)",
            "200": "oklch(L C H)",
            "300": "oklch(L C H)",
            "400": "oklch(L C H)",
            "500": "oklch(L C H)",
            "600": "oklch(L C H)",
            "700": "oklch(L C H)",
            "800": "oklch(L C H)",
            "900": "oklch(L C H)",
            "950": "oklch(L C H)"
        },
        "semantic": {}
    },
    "radius": {
        "sm": "0.25rem",
        "md": "0.375rem",
        "lg": "0.5rem",
        "xl": "0.75rem",
        "2xl": "1rem"
    },
    "shadows": {
        "2xs": "shadow value",
        "xs": "shadow value",
        "sm": "shadow value",
        "DEFAULT": "shadow value",
        "md": "shadow value",
        "lg": "shadow value",
        "xl": "shadow value",
        "2xl": "shadow value"
    },
    "spacing": null
}
```

## Rules for Generating Themes

### Colors (oklch)

- All colors MUST use `oklch(L C H)` format (Tailwind v4 native).
- **L** (lightness): 0 to 1. Higher = lighter.
- **C** (chroma): 0 to ~0.4. Higher = more saturated. Use 0 for pure grays.
- **H** (hue): 0 to 360 degrees.
- The zinc palette is used for ALL neutral/gray surfaces in Flux (backgrounds, borders, text). Tint the zinc shades with the theme's hue for a cohesive feel.
- Zinc shades MUST be a smooth ramp: 50 is lightest (~0.985 L), 950 is darkest (~0.14 L).
- `accent` is the primary brand/action color (buttons, links, focus rings).
- `accent-content` is used for text/icons ON accent backgrounds.
- `accent-foreground` is the contrasting background behind accent content.

### Dark Mode

- Dark mode values go in the `dark` key. They follow the same structure as `light`.
- In dark mode, accent colors are typically lighter/brighter for visibility.
- The zinc palette stays the same between light and dark — Flux handles the inversion.

### Fonts

- Always include Google Fonts URLs in `fonts.urls` if using non-system fonts.
- Include appropriate fallbacks (e.g., `sans-serif`, `monospace`).
- Set to `null` to keep the default (Inter).

### Radius

- Controls border-radius across all components.
- Rounder themes: increase values (lg: "1rem", 2xl: "2rem").
- Sharper themes: decrease values or use "0".
- Set to `null` to keep defaults.

### Shadows

- Shadows should be tinted with the theme's accent or zinc hue for cohesion.
- Use oklch with alpha for shadow colors: `oklch(L C H / alpha)`.
- Larger shadows = more elevation. Scale alpha from ~0.05 (2xs) to ~0.22 (2xl).
- For flat/brutalist themes, use hard offset shadows: `4px 4px 0 oklch(0 0 0)`.

### Null Values

- Set any field to `null` to keep the Flux default. Only override what you need.
- An empty `"semantic": {}` means no semantic color overrides.

## After Generating

1. Save the JSON file to `resources/themes/{slug}.json`.
2. Run `php artisan tweakflux:apply {slug}` to generate the CSS.
3. If Vite is running, the changes appear instantly.

## Example: Complete Theme

Here's the "Bubblegum" theme as a reference for quality and structure:

```json
{
    "name": "Bubblegum",
    "description": "Playful pink accents with warm rose-tinted neutrals and rounded corners",
    "fonts": {
        "sans": "Quicksand, sans-serif",
        "mono": null,
        "serif": null,
        "urls": ["https://fonts.googleapis.com/css2?family=Quicksand:wght@400..700&display=swap"]
    },
    "light": {
        "accent": "oklch(0.65 0.24 350)",
        "accent-content": "oklch(0.65 0.24 350)",
        "accent-foreground": "oklch(0.98 0.01 350)",
        "zinc": {
            "50": "oklch(0.985 0.005 350)",
            "100": "oklch(0.965 0.008 350)",
            "200": "oklch(0.925 0.012 350)",
            "300": "oklch(0.87 0.016 350)",
            "400": "oklch(0.70 0.02 350)",
            "500": "oklch(0.55 0.02 350)",
            "600": "oklch(0.45 0.018 350)",
            "700": "oklch(0.37 0.016 350)",
            "800": "oklch(0.27 0.012 350)",
            "900": "oklch(0.20 0.01 350)",
            "950": "oklch(0.14 0.008 350)"
        },
        "semantic": {}
    },
    "dark": {
        "accent": "oklch(0.80 0.15 80)",
        "accent-content": "oklch(0.80 0.15 80)",
        "accent-foreground": "oklch(0.18 0.02 80)",
        "zinc": {
            "50": "oklch(0.985 0.005 350)",
            "100": "oklch(0.965 0.008 350)",
            "200": "oklch(0.925 0.012 350)",
            "300": "oklch(0.87 0.016 350)",
            "400": "oklch(0.70 0.02 350)",
            "500": "oklch(0.55 0.02 350)",
            "600": "oklch(0.45 0.018 350)",
            "700": "oklch(0.37 0.016 350)",
            "800": "oklch(0.27 0.012 350)",
            "900": "oklch(0.20 0.01 350)",
            "950": "oklch(0.14 0.008 350)"
        },
        "semantic": {}
    },
    "radius": {
        "sm": "0.375rem",
        "md": "0.625rem",
        "lg": "1rem",
        "xl": "1.5rem",
        "2xl": "2rem"
    },
    "shadows": {
        "2xs": "0 1px 1px oklch(0.65 0.15 350 / 0.06)",
        "xs": "0 1px 3px oklch(0.65 0.15 350 / 0.1), 0 1px 2px oklch(0.65 0.15 350 / 0.06)",
        "sm": "0 2px 6px oklch(0.65 0.15 350 / 0.12), 0 1px 3px oklch(0.65 0.15 350 / 0.08)",
        "DEFAULT": "0 4px 10px oklch(0.65 0.15 350 / 0.12), 0 2px 4px oklch(0.65 0.15 350 / 0.08)",
        "md": "0 6px 16px oklch(0.65 0.15 350 / 0.14), 0 2px 6px oklch(0.65 0.15 350 / 0.08)",
        "lg": "0 12px 28px oklch(0.65 0.15 350 / 0.16), 0 4px 10px oklch(0.65 0.15 350 / 0.08)",
        "xl": "0 20px 40px oklch(0.65 0.15 350 / 0.18)",
        "2xl": "0 28px 56px oklch(0.65 0.15 350 / 0.22)"
    },
    "spacing": null
}
```
