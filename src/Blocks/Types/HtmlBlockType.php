<?php

namespace JibayMcs\Nuwa\Blocks\Types;

use JibayMcs\Nuwa\Blocks\AbstractBlockType;

class HtmlBlockType extends AbstractBlockType
{
    public function name(): string
    {
        return 'html';
    }

    public function icon(): string
    {
        return 'heroicon-o-code-bracket';
    }

    public function category(): string
    {
        return 'advanced';
    }

    public function defaultData(): array
    {
        return [
            'html' => '<div>\n    <!-- Your custom HTML here -->\n</div>',
            'css' => '',
            'js' => '',
        ];
    }

    public function editableZones(): array
    {
        return [
            'html' => ['type' => 'code', 'selector' => '.nuwa-html-content'],
        ];
    }

    public function inlineScript(): ?string
    {
        return '(function(el) { const js = el.dataset.nuwaJs; if (js) { try { new Function(js).call(el); } catch(e) { console.warn("[Nuwa] Block JS error:", e); } } })(__NUWA_BLOCK_EL__);';
    }
}
