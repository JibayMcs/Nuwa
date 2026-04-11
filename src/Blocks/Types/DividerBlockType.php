<?php

namespace JibayMcs\Nuwa\Blocks\Types;

use JibayMcs\Nuwa\Blocks\AbstractBlockType;

class DividerBlockType extends AbstractBlockType
{
    public function name(): string
    {
        return 'divider';
    }

    public function icon(): string
    {
        return 'heroicon-o-minus';
    }

    public function category(): string
    {
        return 'layout';
    }

    public function defaultData(): array
    {
        return [
            'style' => 'solid',
            'color' => 'border-gray-300',
            'width' => 'w-full',
            'classes' => 'my-8',
        ];
    }
}
