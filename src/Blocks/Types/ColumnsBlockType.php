<?php

namespace JibayMcs\Nuwa\Blocks\Types;

use JibayMcs\Nuwa\Blocks\AbstractBlockType;

class ColumnsBlockType extends AbstractBlockType
{
    public function name(): string
    {
        return 'columns';
    }

    public function icon(): string
    {
        return 'heroicon-o-view-columns';
    }

    public function category(): string
    {
        return 'layout';
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function defaultData(): array
    {
        return [
            'columns' => 2,
            'gap' => 'gap-8',
            'layout' => 'equal',
            'classes' => '',
            'responsive' => true,
        ];
    }
}
