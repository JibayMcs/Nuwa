<?php

namespace JibayMcs\Nuwa\Filament\Resources\Pages\Pages;

use JibayMcs\Nuwa\Filament\Resources\Pages\PageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPage extends ViewRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
