<?php

namespace App\Filament\Resources\Properties\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class PropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->district?->name),

                TextColumn::make('categories.name')
                    ->label('Property Type')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                TextColumn::make('landlord.name')
                    ->label('Owner')
                    ->searchable(),

                IconColumn::make('is_multi_unit')
                    ->label('Multi-Unit')
                    ->boolean(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'under_verification' => 'warning',
                    }),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status'),
            ])
            ->defaultSort('created_at', 'desc')

            // Table Actions (Edit, Delete)
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->recordActionsColumnLabel('Actions');
    }
}
