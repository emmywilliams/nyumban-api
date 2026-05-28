<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->fontFamily('mono')
                    ->searchable(),

                TextColumn::make('description')
                    ->limit(50) // Don't let long text break the table
                    ->wrap()
                    ->placeholder('No description provided'),

                TextColumn::make('created_at')
                    ->label('Created On')
                    ->date()

            ])
            ->filters([
                //
            ])

            // Table Actions (Edit, Delete)
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->recordActionsColumnLabel('Actions')

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
