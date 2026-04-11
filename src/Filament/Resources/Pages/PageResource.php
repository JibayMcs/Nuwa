<?php

namespace JibayMcs\Nuwa\Filament\Resources\Pages;

use JibayMcs\Nuwa\Filament\Resources\Pages\Pages\CreatePage;
use JibayMcs\Nuwa\Filament\Resources\Pages\Pages\EditPage;
use JibayMcs\Nuwa\Filament\Resources\Pages\Pages\EditorPage;
use JibayMcs\Nuwa\Filament\Resources\Pages\Pages\ListPages;
use JibayMcs\Nuwa\Filament\Resources\Pages\Pages\ViewPage;
use JibayMcs\Nuwa\Filament\Resources\Pages\Schemas\PageForm;
use JibayMcs\Nuwa\Filament\Resources\Pages\Schemas\PageInfolist;
use JibayMcs\Nuwa\Filament\Resources\Pages\Tables\PagesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use JibayMcs\Nuwa\Models\Page;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
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
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'view' => ViewPage::route('/{record}'),
            'edit' => EditPage::route('/{record}/edit'),
            'editor' => EditorPage::route('/{record}/editor'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
