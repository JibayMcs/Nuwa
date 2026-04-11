<?php

namespace JibayMcs\Nuwa\Filament\Resources\Templates\Pages;

use JibayMcs\Nuwa\Filament\Resources\Templates\TemplateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTemplate extends ViewRecord
{
    protected static string $resource = TemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
