<?php

namespace JibayMcs\Nuwa\Filament\Resources\Blocks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use JibayMcs\Nuwa\Blocks\BlockTypeRegistry;

class BlocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label(__('nuwa::nuwa.fields.block_type'))
                    ->badge()
                    ->formatStateUsing(function (string $state) {
                        $registry = app(BlockTypeRegistry::class);

                        return $registry->has($state) ? $registry->get($state)->label() : $state;
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('page.title')
                    ->label(__('nuwa::nuwa.fields.page'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('order')
                    ->label(__('nuwa::nuwa.fields.order'))
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('nuwa::nuwa.fields.updated_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('nuwa::nuwa.fields.block_type'))
                    ->options(fn () => collect(app(BlockTypeRegistry::class)->all())
                        ->mapWithKeys(fn ($type) => [$type->name() => $type->label()])
                        ->toArray()
                    ),
                SelectFilter::make('page_id')
                    ->relationship('page', 'title')
                    ->label(__('nuwa::nuwa.fields.page')),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
