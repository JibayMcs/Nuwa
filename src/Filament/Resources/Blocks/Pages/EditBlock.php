<?php

namespace JibayMcs\Nuwa\Filament\Resources\Blocks\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use JibayMcs\Nuwa\Filament\Resources\Blocks\BlockResource;

class EditBlock extends EditRecord
{
    protected static string $resource = BlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Charger le contenu depuis le storage dans le formulaire
        $data['content'] = $this->record->loadContent();

        return $data;
    }

    protected function afterSave(): void
    {
        $content = $this->data['content'] ?? [];
        $this->record->saveContent($content);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Le contenu sera sauvegarde dans le storage, pas en BDD
        unset($data['content']);

        return $data;
    }
}
