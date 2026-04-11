<?php

namespace JibayMcs\Nuwa\Filament\Resources\Templates\Pages;

use JibayMcs\Nuwa\Filament\Resources\Templates\TemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTemplate extends EditRecord
{
    protected static string $resource = TemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
