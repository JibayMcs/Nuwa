<?php

namespace JibayMcs\Nuwa\Filament\Fields;

use Closure;
use Filament\Forms\Components\Field;
use JibayMcs\Nuwa\Blocks\BlockTypeRegistry;
use JibayMcs\Nuwa\Blocks\FrameworkManager;

class BlockEditorField extends Field
{
    protected string $view = 'nuwa::filament.fields.block-editor';

    protected string|Closure|null $blockType = null;

    protected string|Closure|null $framework = null;

    protected int|Closure $previewHeight = 400;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([
            'html' => '',
            'css' => '',
            'js' => '',
        ]);
    }

    public function blockType(string|Closure|null $type): static
    {
        $this->blockType = $type;

        return $this;
    }

    public function getBlockType(): ?string
    {
        return $this->evaluate($this->blockType);
    }

    public function framework(string|Closure|null $framework): static
    {
        $this->framework = $framework;

        return $this;
    }

    public function getFramework(): string
    {
        return $this->evaluate($this->framework) ?? app(FrameworkManager::class)->active();
    }

    public function previewHeight(int|Closure $height): static
    {
        $this->previewHeight = $height;

        return $this;
    }

    public function getPreviewHeight(): int
    {
        return $this->evaluate($this->previewHeight);
    }

    public function getFrameworkHead(): string
    {
        $manager = app(FrameworkManager::class);
        $original = $manager->active();
        $manager->setActive($this->getFramework());
        $head = $manager->renderHead();
        $manager->setActive($original);

        return $head;
    }

    public function getBlockTypeDefinition(): ?array
    {
        $typeName = $this->getBlockType();

        if (! $typeName) {
            return null;
        }

        $registry = app(BlockTypeRegistry::class);

        if (! $registry->has($typeName)) {
            return null;
        }

        return $registry->get($typeName)->toArray();
    }

    public function getDefaultHtmlForType(): string
    {
        $typeName = $this->getBlockType();

        if (! $typeName) {
            return '';
        }

        $registry = app(BlockTypeRegistry::class);

        if (! $registry->has($typeName)) {
            return '';
        }

        $type = $registry->get($typeName);

        return $type->render($type->defaultData());
    }
}
