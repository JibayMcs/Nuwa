<?php

namespace JibayMcs\Nuwa\Filament\Resources\Blocks\Pages;

use Filament\Resources\Pages\CreateRecord;
use JibayMcs\Nuwa\Filament\Resources\Blocks\BlockResource;

class CreateBlock extends CreateRecord
{
    protected static string $resource = BlockResource::class;

    protected function afterCreate(): void
    {
        $content = $this->data['content'] ?? [];
        $this->record->saveContent($content);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Le contenu HTML/CSS/JS sera sauvegarde dans le storage apres creation
        unset($data['content']);

        return $data;
    }
}
