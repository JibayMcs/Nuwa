<?php

namespace JibayMcs\Nuwa\Filament\Resources\Pages\Pages;

use JibayMcs\Nuwa\Filament\Resources\Pages\PageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('visual-editor')
                ->label(__('nuwa::nuwa.editor.open'))
                ->icon(Heroicon::OutlinedPaintBrush)
                ->color('primary')
                ->url(fn () => PageResource::getUrl('editor', ['record' => $this->record])),
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
