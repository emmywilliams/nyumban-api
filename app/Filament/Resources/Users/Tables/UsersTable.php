<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl('/images/default-avatar.png'),

                // Name with Email as Description
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->email),

                TextColumn::make('phone')
                    ->searchable()
                    ->copyable(),

                // Role - Showing it as a simple badge
                TextColumn::make('role.name')
                    ->badge()
                    ->color('gray'),

                // Status with custom colors
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('national_id')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('role_id')
                    ->relationship('role', 'name')
                    ->label('Filter by Role'),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'pending' => 'Pending',
                    ]),
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
