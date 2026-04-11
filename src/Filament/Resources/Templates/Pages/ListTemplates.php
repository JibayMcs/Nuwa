<?php

namespace JibayMcs\Nuwa\Filament\Resources\Templates\Pages;

use JibayMcs\Nuwa\Filament\Resources\Templates\TemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTemplates extends ListRecords
{
    protected static string $resource = TemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
