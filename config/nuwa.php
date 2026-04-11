<?php

// config for JibayMcs/Nuwa

return [

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The URI prefix for front-end page rendering.
    | Example: 'pages' => /pages/{slug}
    | Set to '' for root-level pages (/{slug}).
    |
    */
    'route_prefix' => 'pages',

    /*
    |--------------------------------------------------------------------------
    | Front-end Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware applied to front-end page routes.
    |
    */
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */
    'api' => [
        'enabled' => true,
        'prefix' => 'api/nuwa',
        'middleware' => ['api'],
        'rate_limit' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Configuration
    |--------------------------------------------------------------------------
    */
    'media' => [
        'disk' => 'public',
        'max_upload_size' => 10240, // KB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'mp4', 'webm', 'pdf'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Block Storage
    |--------------------------------------------------------------------------
    |
    | Disk used to store block content (HTML, CSS, JS files).
    | Blocks are stored in: {disk}/nuwa/blocks/{id}/
    | Use 'local' for private or 'public' for direct web access.
    |
    */
    'blocks' => [
        'disk' => 'local',
    ],

    /*
    |--------------------------------------------------------------------------
    | CSS Framework
    |--------------------------------------------------------------------------
    |
    | The CSS framework used for block rendering and preview.
    | Options: 'tailwind4', 'bootstrap', 'none'
    |
    */
    'css_framework' => 'tailwind4',

    /*
    |--------------------------------------------------------------------------
    | Active Blocks
    |--------------------------------------------------------------------------
    |
    | List of block types available in the editor.
    | Remove a block from this list to disable it.
    |
    */
    'blocks' => [
        'text',
        'image',
        'hero',
        'section',
        'columns',
        'button',
        'spacer',
        'divider',
        'html',
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */
    'seo' => [
        'sitemap' => true,
        'schema_org' => true,
    ],

];
