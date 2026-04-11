<?php

namespace JibayMcs\Nuwa\Blocks\Types;

use JibayMcs\Nuwa\Blocks\AbstractBlockType;

class SectionBlockType extends AbstractBlockType
{
    public function name(): string
    {
        return 'section';
    }

    public function icon(): string
    {
        return 'heroicon-o-rectangle-group';
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
            'tag' => 'section',
            'classes' => 'py-16 px-6',
            'container' => true,
            'container_class' => 'max-w-7xl mx-auto',
        ];
    }
}
