<?php

namespace JibayMcs\Nuwa\Filament\Resources\Blocks;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use JibayMcs\Nuwa\Filament\Resources\Blocks\Pages\CreateBlock;
use JibayMcs\Nuwa\Filament\Resources\Blocks\Pages\EditBlock;
use JibayMcs\Nuwa\Filament\Resources\Blocks\Pages\ListBlocks;
use JibayMcs\Nuwa\Filament\Resources\Blocks\Schemas\BlockForm;
use JibayMcs\Nuwa\Filament\Resources\Blocks\Tables\BlocksTable;
use JibayMcs\Nuwa\Models\Block;

class BlockResource extends Resource
{
    protected static ?string $model = Block::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $recordTitleAttribute = 'type';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Nuwa';
    }

    public static function getNavigationLabel(): string
    {
        return __('nuwa::nuwa.navigation.blocks');
    }

    public static function getModelLabel(): string
    {
        return __('nuwa::nuwa.models.block');
    }

    public static function getPluralModelLabel(): string
    {
        return __('nuwa::nuwa.models.blocks');
    }

    public static function form(Schema $schema): Schema
    {
        return BlockForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlocksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlocks::route('/'),
            'create' => CreateBlock::route('/create'),
            'edit' => EditBlock::route('/{record}/edit'),
        ];
    }
}
