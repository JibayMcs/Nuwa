<?php

namespace JibayMcs\Nuwa;

use JibayMcs\Nuwa\Filament\Resources\Blocks\BlockResource;
use JibayMcs\Nuwa\Filament\Resources\Menus\MenuResource;
use JibayMcs\Nuwa\Filament\Resources\Pages\PageResource;
use JibayMcs\Nuwa\Filament\Resources\Templates\TemplateResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class NuwaPlugin implements Plugin
{
    public function getId(): string
    {
        return 'nuwa';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PageResource::class,
            BlockResource::class,
            TemplateResource::class,
            MenuResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
