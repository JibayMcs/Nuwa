<?php

namespace JibayMcs\Nuwa\Filament\Resources\Blocks\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use JibayMcs\Nuwa\Filament\Resources\Blocks\BlockResource;

class ListBlocks extends ListRecords
{
    protected static string $resource = BlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
