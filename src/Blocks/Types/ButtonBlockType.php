<?php

namespace JibayMcs\Nuwa\Blocks\Types;

use JibayMcs\Nuwa\Blocks\AbstractBlockType;

class ButtonBlockType extends AbstractBlockType
{
    public function name(): string
    {
        return 'button';
    }

    public function icon(): string
    {
        return 'heroicon-o-cursor-arrow-ripple';
    }

    public function category(): string
    {
        return 'content';
    }

    public function defaultData(): array
    {
        return [
            'label' => 'Click me',
            'url' => '#',
            'target' => '_self',
            'variant' => 'primary',
            'size' => 'md',
            'classes' => '',
        ];
    }

    public function editableZones(): array
    {
        return [
            'label' => ['type' => 'text', 'selector' => 'a, button'],
        ];
    }

    public function rules(): array
    {
        return [
            'label' => 'required|string|max:100',
            'url' => 'nullable|string',
        ];
    }
}
