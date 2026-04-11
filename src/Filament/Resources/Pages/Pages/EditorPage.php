<?php

namespace JibayMcs\Nuwa\Filament\Resources\Pages\Pages;

use Filament\Resources\Pages\Page;
use JibayMcs\Nuwa\Filament\Resources\Pages\PageResource;
use JibayMcs\Nuwa\Models\Page as PageModel;

class EditorPage extends Page
{
    protected static string $resource = PageResource::class;

    protected string $view = 'nuwa::filament.resources.pages.editor';

    public PageModel $record;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string
    {
        return __('nuwa::nuwa.editor.title', ['page' => $this->record->title]);
    }
}
