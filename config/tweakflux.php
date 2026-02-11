<?php

declare(strict_types=1);

return [
    'active_theme' => env('TWEAKFLUX_THEME', 'default'),
    'themes_path' => resource_path('themes'),
    'output_path' => public_path('css/tweakflux-theme.css'),
];
