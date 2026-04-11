<?php

namespace JibayMcs\Nuwa\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeBlockCommand extends Command
{
    public $signature = 'nuwa:block {name : The block type name (e.g. Testimonial, PricingCard)}
                         {--category=content : The block category (content, layout, media, sections, advanced)}
                         {--container : Mark this block as a container (can hold children)}';

    public $description = 'Generate a custom Nuwa block (PHP type + Blade view + JS file).';

    public function handle(Filesystem $files): int
    {
        $name = $this->argument('name');
        $category = $this->option('category');
        $isContainer = $this->option('container');

        $studlyName = Str::studly($name);
        $kebabName = Str::kebab($name);
        $snakeName = Str::snake($name);

        $appNamespace = $this->laravel->getNamespace();

        // Paths
        $phpPath = app_path("Nuwa/Blocks/{$studlyName}BlockType.php");
        $bladePath = resource_path("views/nuwa/blocks/{$kebabName}.blade.php");
        $jsPath = resource_path("js/nuwa/blocks/{$kebabName}.js");

        // Check existing
        if ($files->exists($phpPath)) {
            $this->components->error("Block type [{$studlyName}BlockType] already exists!");

            return self::FAILURE;
        }

        // Create directories
        $files->ensureDirectoryExists(dirname($phpPath));
        $files->ensureDirectoryExists(dirname($bladePath));
        $files->ensureDirectoryExists(dirname($jsPath));

        // Generate PHP
        $phpStub = $this->getPhpStub($appNamespace, $studlyName, $kebabName, $snakeName, $category, $isContainer);
        $files->put($phpPath, $phpStub);
        $this->components->info("Created: {$phpPath}");

        // Generate Blade
        $bladeStub = $this->getBladeStub($studlyName, $kebabName);
        $files->put($bladePath, $bladeStub);
        $this->components->info("Created: {$bladePath}");

        // Generate JS
        $jsStub = $this->getJsStub($kebabName);
        $files->put($jsPath, $jsStub);
        $this->components->info("Created: {$jsPath}");

        $this->newLine();
        $this->components->info("Block [{$studlyName}] generated successfully!");
        $this->components->bulletList([
            "Register it in a ServiceProvider:",
            "  app(BlockTypeRegistry::class)->register(new \\{$appNamespace}Nuwa\\Blocks\\{$studlyName}BlockType);",
        ]);

        return self::SUCCESS;
    }

    protected function getPhpStub(string $namespace, string $studly, string $kebab, string $snake, string $category, bool $isContainer): string
    {
        $containerMethod = $isContainer ? <<<'PHP'

    public function isContainer(): bool
    {
        return true;
    }
PHP : '';

        return <<<PHP
<?php

namespace {$namespace}Nuwa\\Blocks;

use JibayMcs\\Nuwa\\Blocks\\AbstractBlockType;

class {$studly}BlockType extends AbstractBlockType
{
    public function name(): string
    {
        return '{$snake}';
    }

    public function icon(): string
    {
        return 'heroicon-o-cube';
    }

    public function category(): string
    {
        return '{$category}';
    }

    public function bladeView(): string
    {
        return 'nuwa.blocks.{$kebab}';
    }

    public function defaultData(): array
    {
        return [
            'title' => 'My {$studly}',
            'classes' => '',
        ];
    }

    public function editableZones(): array
    {
        return [
            'title' => ['type' => 'text', 'selector' => '.nuwa-{$kebab}-title'],
        ];
    }

    public function scripts(): array
    {
        return [
            'js/nuwa/blocks/{$kebab}.js',
        ];
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
        ];
    }{$containerMethod}
}

PHP;
    }

    protected function getBladeStub(string $studly, string $kebab): string
    {
        return <<<BLADE
<div class="nuwa-block-{$kebab} {{ \$data['classes'] ?? '' }}">
    <h3 class="nuwa-{$kebab}-title text-2xl font-bold">{{ \$data['title'] ?? '{$studly}' }}</h3>

    {{-- Your block HTML here --}}
</div>

BLADE;
    }

    protected function getJsStub(string $kebab): string
    {
        return <<<JS
/**
 * Nuwa block: {$kebab}
 *
 * This script is loaded when the block is rendered on the front-end.
 * Use it for animations, interactions, or any custom behavior.
 */
(function () {
    document.querySelectorAll('.nuwa-block-{$kebab}').forEach(function (el) {
        // Your block JavaScript here
    });
})();

JS;
    }
}
