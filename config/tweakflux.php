<?php

declare(strict_types=1);

return [
    'active_theme' => env('TWEAKFLUX_THEME', 'default'),
    'themes_path' => resource_path('themes'),
    'output_path' => resource_path('css/tweakflux-theme.css'),
    'css_entry_point' => resource_path('css/app.css'),
];
