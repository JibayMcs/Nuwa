<?php

namespace JibayMcs\Nuwa\Blocks\Types;

use JibayMcs\Nuwa\Blocks\AbstractBlockType;

class ImageBlockType extends AbstractBlockType
{
    public function name(): string
    {
        return 'image';
    }

    public function icon(): string
    {
        return 'heroicon-o-photo';
    }

    public function category(): string
    {
        return 'media';
    }

    public function defaultData(): array
    {
        return [
            'src' => '',
            'alt' => '',
            'width' => null,
            'height' => null,
            'lazy' => true,
            'classes' => 'rounded-lg',
            'caption' => '',
        ];
    }

    public function editableZones(): array
    {
        return [
            'caption' => ['type' => 'text', 'selector' => 'figcaption'],
        ];
    }

    public function rules(): array
    {
        return [
            'src' => 'required|string',
            'alt' => 'nullable|string|max:255',
        ];
    }
}
