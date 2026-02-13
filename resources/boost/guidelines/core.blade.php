# TweakFlux

This application uses the TweakFlux package (`joshcirre/tweakflux`) for deep theming of Flux UI components via Tailwind v4 CSS custom properties.

Activate the `tweakflux-theme-generator` skill when the user asks to create, generate, edit, or modify a TweakFlux theme — whether from a description, color palette, screenshot, brand guidelines, or any visual reference.

## Available Commands

- `tweakflux apply {theme?}` — Apply a theme (interactive picker if no name given)
- `tweakflux apply {theme} --no-effects` — Apply a theme without visual effects (glows, animations)
- `tweakflux list` — List all available themes
- `tweakflux create {name}` — Scaffold a new theme JSON file
- `tweakflux boost` — Copy Boost guidelines and skills into your project

## Theme Files

Themes are JSON files in `resources/themes/{slug}.json`. All colors use `oklch(L C H)` format. Set any value to `null` to keep the Flux default.

- `css` — Structural CSS always included (layout overrides, button shapes).
- `effects` — Toggleable visual effects (glows, animations) that users can disable with `--no-effects`.
