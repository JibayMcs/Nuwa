<?php

namespace JibayMcs\Nuwa\Filament\Resources\Blocks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use JibayMcs\Nuwa\Blocks\BlockTypeRegistry;
use JibayMcs\Nuwa\Filament\Fields\BlockEditorField;
use JibayMcs\Nuwa\Models\Page;

class BlockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('nuwa::nuwa.sections.block_settings'))
                    ->schema([
                        Select::make('type')
                            ->label(__('nuwa::nuwa.fields.block_type'))
                            ->options(fn () => collect(app(BlockTypeRegistry::class)->all())
                                ->mapWithKeys(fn ($type) => [$type->name() => $type->label()])
                                ->toArray()
                            )
                            ->required()
                            ->searchable()
                            ->live(),
                        Select::make('page_id')
                            ->label(__('nuwa::nuwa.fields.page'))
                            ->relationship('page', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('order')
                            ->label(__('nuwa::nuwa.fields.order'))
                            ->numeric()
                            ->default(0),
                    ])->columns(3),

                Section::make(__('nuwa::nuwa.sections.block_content'))
                    ->columnSpanFull()
                    ->schema([
                        BlockEditorField::make('content')
                            ->label('')
                            ->previewHeight(500),
                    ]),
            ]);
    }
}
