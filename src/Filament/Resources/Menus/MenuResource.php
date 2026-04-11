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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'Nuwa';
    }

    public static function getNavigationLabel(): string
    {
        return __('nuwa::nuwa.navigation.menus');
    }

    public static function getModelLabel(): string
    {
        return __('nuwa::nuwa.models.menu');
    }

    public static function getPluralModelLabel(): string
    {
        return __('nuwa::nuwa.models.menus');
    }

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
