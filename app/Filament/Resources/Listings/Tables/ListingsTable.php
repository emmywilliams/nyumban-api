<?php

namespace App\Filament\Resources\Listings\Tables;


use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;

class ListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->description(fn($record) => "Unit: " . $record->unit?->name),

                TextColumn::make('monthly_price')
                    ->money('UGX')
                    ->sortable(),

                ToggleColumn::make('is_featured')
                    ->label('Featured'),

                TextColumn::make('visibility_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'visible' => 'success',
                        'hidden' => 'gray',
                    }),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('visibility_status'),
                \Filament\Tables\Filters\TernaryFilter::make('is_featured'),
            ]);
    }
}
