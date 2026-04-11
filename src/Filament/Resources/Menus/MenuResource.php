<?php

namespace JibayMcs\Nuwa\Filament\Resources\Menus;

use JibayMcs\Nuwa\Filament\Resources\Menus\Pages\CreateMenu;
use JibayMcs\Nuwa\Filament\Resources\Menus\Pages\EditMenu;
use JibayMcs\Nuwa\Filament\Resources\Menus\Pages\ListMenus;
use JibayMcs\Nuwa\Filament\Resources\Menus\Schemas\MenuForm;
use JibayMcs\Nuwa\Filament\Resources\Menus\Tables\MenusTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use JibayMcs\Nuwa\Models\Menu;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MenuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenusTable::configure($table);
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
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
