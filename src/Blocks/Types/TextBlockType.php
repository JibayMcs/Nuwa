<?php

namespace JibayMcs\Nuwa\Blocks\Types;

use JibayMcs\Nuwa\Blocks\AbstractBlockType;

class TextBlockType extends AbstractBlockType
{
    public function name(): string
    {
        return 'text';
    }

    public function icon(): string
    {
        return 'heroicon-o-document-text';
    }

    public function category(): string
    {
        return 'content';
    }

    public function defaultData(): array
    {
        return [
            'content' => '<p>Your text here...</p>',
            'classes' => '',
        ];
    }

    public function editableZones(): array
    {
        return [
            'content' => ['type' => 'richtext', 'selector' => '.nuwa-text-content'],
        ];
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string',
        ];
    }
}
