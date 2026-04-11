<?php

namespace JibayMcs\Nuwa\Blocks\Types;

use JibayMcs\Nuwa\Blocks\AbstractBlockType;

class SpacerBlockType extends AbstractBlockType
{
    public function name(): string
    {
        return 'spacer';
    }

    public function icon(): string
    {
        return 'heroicon-o-arrows-up-down';
    }

    public function category(): string
    {
        return 'layout';
    }

    public function defaultData(): array
    {
        return [
            'height' => 40,
        ];
    }

    public function rules(): array
    {
        return [
            'height' => 'required|integer|min:1|max:500',
        ];
    }
}
