<?php

namespace JibayMcs\Nuwa\Blocks\Types;

use JibayMcs\Nuwa\Blocks\AbstractBlockType;

class HeroBlockType extends AbstractBlockType
{
    public function name(): string
    {
        return 'hero';
    }

    public function icon(): string
    {
        return 'heroicon-o-sparkles';
    }

    public function category(): string
    {
        return 'sections';
    }

    public function defaultData(): array
    {
        return [
            'title' => 'Welcome',
            'subtitle' => 'Your story starts here.',
            'cta_label' => 'Get Started',
            'cta_url' => '#',
            'bg_image' => null,
            'bg_color' => 'bg-gray-900',
            'text_color' => 'text-white',
            'classes' => '',
        ];
    }

    public function editableZones(): array
    {
        return [
            'title' => ['type' => 'text', 'selector' => 'h1'],
            'subtitle' => ['type' => 'text', 'selector' => '.nuwa-hero-subtitle'],
            'cta_label' => ['type' => 'text', 'selector' => '.nuwa-hero-cta'],
        ];
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'cta_url' => 'nullable|string',
        ];
    }
}
