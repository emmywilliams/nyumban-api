<?php

namespace App\Filament\Widgets;

use App\Models\Property;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables;

class RecentProperties extends TableWidget
{
    // protected function getTableQuery(): Builder
    // {
    //     return Property::query()->latest()->limit(5);
    // }
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Property::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('landlord.name')
                    ->label('Landlord'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'under_verification',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
