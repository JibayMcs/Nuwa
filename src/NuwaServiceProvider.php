<?php

namespace JibayMcs\Nuwa;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use JibayMcs\Nuwa\Blocks\BlockTypeRegistry;
use JibayMcs\Nuwa\Blocks\FrameworkManager;
use JibayMcs\Nuwa\Blocks\Types\ButtonBlockType;
use JibayMcs\Nuwa\Blocks\Types\ColumnsBlockType;
use JibayMcs\Nuwa\Blocks\Types\DividerBlockType;
use JibayMcs\Nuwa\Blocks\Types\HeroBlockType;
use JibayMcs\Nuwa\Blocks\Types\HtmlBlockType;
use JibayMcs\Nuwa\Blocks\Types\ImageBlockType;
use JibayMcs\Nuwa\Blocks\Types\SectionBlockType;
use JibayMcs\Nuwa\Blocks\Types\SpacerBlockType;
use JibayMcs\Nuwa\Blocks\Types\TextBlockType;
use JibayMcs\Nuwa\Commands\MakeBlockCommand;
use JibayMcs\Nuwa\Commands\NuwaInstallCommand;
use JibayMcs\Nuwa\Livewire\PageEditor;
use Livewire\Livewire;

class NuwaServiceProvider extends PackageServiceProvider
{
    public static string $name = 'nuwa';

    public static string $viewNamespace = 'nuwa';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations();
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(FrameworkManager::class);

        $this->app->singleton(BlockTypeRegistry::class, function () {
            $registry = new BlockTypeRegistry;

            $registry->registerMany([
                new TextBlockType,
                new ImageBlockType,
                new HeroBlockType,
                new SectionBlockType,
                new ColumnsBlockType,
                new ButtonBlockType,
                new SpacerBlockType,
                new DividerBlockType,
                new HtmlBlockType,
            ]);

            return $registry;
        });
    }

    public function packageBooted(): void
    {
        // Livewire Components
        Livewire::component('nuwa-page-editor', PageEditor::class);

        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/nuwa/{$file->getFilename()}"),
                ], 'nuwa-stubs');
            }
        }
    }

    protected function getAssetPackageName(): ?string
    {
        return 'jibaymcs/nuwa';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            AlpineComponent::make('nuwa', __DIR__ . '/../resources/dist/nuwa.js'),
            AlpineComponent::make('nuwa-editor', __DIR__ . '/../resources/dist/nuwa-editor.js'),
            Css::make('nuwa-styles', __DIR__ . '/../resources/dist/nuwa.css'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            NuwaInstallCommand::class,
            MakeBlockCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_nuwa_templates_table',
            'create_nuwa_menus_table',
            'create_nuwa_pages_table',
            'create_nuwa_blocks_table',
            'create_nuwa_menu_items_table',
        ];
    }
}
