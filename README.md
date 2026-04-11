# Nuwa — Visual CMS for FilamentPHP v5

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jibaymcs/nuwa.svg?style=flat-square)](https://packagist.org/packages/jibaymcs/nuwa)
[![Total Downloads](https://img.shields.io/packagist/dt/jibaymcs/nuwa.svg?style=flat-square)](https://packagist.org/packages/jibaymcs/nuwa)
[![License](https://img.shields.io/packagist/l/jibaymcs/nuwa.svg?style=flat-square)](LICENSE.md)

**Nuwa** is a full-featured, open-source CMS plugin for FilamentPHP v5. Build pages visually with a custom drag & drop block editor, manage menus, templates, media and SEO — all from your Filament admin panel.

Render your pages with **Blade** for traditional Laravel sites, or serve them via a **JSON API** for headless frontends (Next.js, Nuxt, Astro...).

> Named after [Nuwa](https://en.wikipedia.org/wiki/N%C3%BCwa), the goddess who shaped the world — this plugin lets you shape your website.

---

## Features

- **Visual block editor** — Custom-built with TypeScript + Alpine.js. No GrapesJS dependency.
- **Drag & drop** — Reorder and nest blocks with intuitive drag & drop
- **Undo / Redo** — Full history with Command pattern (Ctrl+Z / Ctrl+Shift+Z)
- **Block library** — Text, Image, Hero, Grid, Columns, Button, Gallery, Video, FAQ, Pricing, Testimonials, Form, Code, Map, and more
- **Templates** — Save and reuse page layouts. Built-in starter templates included.
- **Menus** — Hierarchical navigation menus with drag & drop tree editor
- **SEO** — Meta tags, Open Graph, canonical URLs, sitemap.xml generation
- **Media manager** — Upload, browse and manage images with auto-optimization
- **Blade rendering** — Server-side rendering with customizable layouts and Blade components
- **Headless API** — JSON REST API for all pages, menus and media
- **Responsive preview** — Preview pages in desktop, tablet and mobile viewports
- **Page management** — Draft, published, scheduled, archived statuses with revisions
- **Dark mode** — Inherits Filament's dark mode automatically
- **Extensible** — Create custom blocks with `php artisan nuwa:block`. Hook system for events.
- **SOLID TypeScript** — Clean, maintainable editor codebase with strict separation of concerns

## Requirements

- PHP 8.2+
- Laravel 11+
- FilamentPHP v5

## Installation

```bash
composer require jibaymcs/nuwa
```

Run the install command:

```bash
php artisan nuwa:install
```

This will publish the config, run migrations, and set up default templates.

### Theme setup

Add the plugin views to your Filament theme CSS:

```css
@source '../../../../vendor/jibaymcs/nuwa/resources/**/*.blade.php';
```

### Register the plugin

```php
use JibayMcs\Nuwa\NuwaPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            NuwaPlugin::make(),
        ]);
}
```

## Quick start

### 1. Create a page

Navigate to **CMS > Pages** in your Filament panel and click "Create page". The visual editor opens with an empty canvas.

### 2. Add blocks

Click the **+** button in the toolbar or drag a block from the sidebar onto the canvas. Configure each block's content and settings in the right panel.

### 3. Publish

Set the page status to "Published" and save. Your page is live.

### 4. Display on your frontend

**Option A — Blade rendering:**

Nuwa registers a catch-all route (configurable) that renders pages at `/{slug}`:

```php
// config/nuwa.php
'routing' => [
    'prefix' => '',          // URL prefix (e.g. 'pages' for /pages/{slug})
    'middleware' => ['web'],
],
```

Or render manually in your own controller:

```php
use JibayMcs\Nuwa\Rendering\PageRenderer;

Route::get('/landing/{slug}', function (string $slug, PageRenderer $renderer) {
    return $renderer->render($slug);
});
```

Blade components are also available:

```blade
<x-nuwa-page :slug="'homepage'" />
<x-nuwa-menu name="main" />
```

**Option B — Headless JSON API:**

```
GET /api/nuwa/pages
GET /api/nuwa/pages/{slug}
GET /api/nuwa/menus/{name}
```

Example response:

```json
{
  "title": "Homepage",
  "slug": "homepage",
  "status": "published",
  "seo": {
    "title": "Welcome to Acme",
    "description": "...",
    "og_image": "..."
  },
  "blocks": [
    {
      "type": "hero",
      "data": {
        "title": "Welcome",
        "subtitle": "Build something great",
        "background": "/storage/hero-bg.jpg"
      },
      "children": []
    }
  ]
}
```

## Creating custom blocks

Generate a new block:

```bash
php artisan nuwa:block TestimonialBlock
```

This creates both the PHP block type and the TypeScript block class:

```php
// src/Blocks/TestimonialBlockType.php
use JibayMcs\Nuwa\Blocks\AbstractBlockType;

class TestimonialBlockType extends AbstractBlockType
{
    public static function name(): string
    {
        return 'testimonial';
    }

    public static function label(): string
    {
        return 'Testimonial';
    }

    public static function icon(): string
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function schema(): array
    {
        return [
            TextInput::make('author'),
            TextInput::make('role'),
            Textarea::make('quote'),
            FileUpload::make('avatar'),
            Select::make('style')->options([
                'card' => 'Card',
                'minimal' => 'Minimal',
                'quote' => 'Large quote',
            ]),
        ];
    }
}
```

Register custom blocks in a service provider:

```php
use JibayMcs\Nuwa\Blocks\BlockTypeRegistry;

public function boot(): void
{
    BlockTypeRegistry::register(TestimonialBlockType::class);
}
```

## Configuration

```bash
php artisan vendor:publish --tag="nuwa-config"
```

```php
// config/nuwa.php
return [
    // Blocks enabled by default
    'blocks' => [
        'text', 'image', 'hero', 'button', 'spacer', 'divider',
        'grid', 'columns', 'gallery', 'video', 'faq', 'pricing',
        'testimonial', 'form', 'code', 'map', 'social', 'html',
    ],

    // Front routing
    'routing' => [
        'enabled' => true,
        'prefix' => '',
        'middleware' => ['web'],
    ],

    // API
    'api' => [
        'enabled' => true,
        'prefix' => 'api/nuwa',
        'middleware' => ['api'],
        'rate_limit' => 60,
    ],

    // Media
    'media' => [
        'disk' => 'public',
        'max_upload_size' => 10240, // KB
        'auto_optimize' => true,
    ],

    // SEO
    'seo' => [
        'sitemap' => true,
        'schema_org' => true,
    ],
];
```

## Customizing layouts

Publish the views:

```bash
php artisan vendor:publish --tag="nuwa-views"
```

Override the default layout at `resources/views/vendor/nuwa/layouts/default.blade.php`.

## Roadmap

See [docs/ROADMAP.md](docs/ROADMAP.md) for the full development roadmap with 18 phases covering the complete feature set.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [JibayMcs](https://github.com/JibayMcs)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
