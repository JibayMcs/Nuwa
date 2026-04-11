<?php

namespace JibayMcs\Nuwa\Filament\Resources\Templates;

use JibayMcs\Nuwa\Models\Template;
use JibayMcs\Nuwa\Filament\Resources\Templates\Pages\CreateTemplate;
use JibayMcs\Nuwa\Filament\Resources\Templates\Pages\EditTemplate;
use JibayMcs\Nuwa\Filament\Resources\Templates\Pages\ListTemplates;
use JibayMcs\Nuwa\Filament\Resources\Templates\Pages\ViewTemplate;
use JibayMcs\Nuwa\Filament\Resources\Templates\Schemas\TemplateForm;
use JibayMcs\Nuwa\Filament\Resources\Templates\Schemas\TemplateInfolist;
use JibayMcs\Nuwa\Filament\Resources\Templates\Tables\TemplatesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TemplateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TemplateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TemplatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTemplates::route('/'),
            'create' => CreateTemplate::route('/create'),
            'view' => ViewTemplate::route('/{record}'),
            'edit' => EditTemplate::route('/{record}/edit'),
        ];
    }
}
