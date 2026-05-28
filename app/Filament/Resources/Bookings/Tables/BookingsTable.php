<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('listing.thumbnail')
                    ->label('Property')
                    ->circular(),

                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => "From: " . $record->start_date->format('d M') . " To: " . $record->end_date->format('d M')),

                TextColumn::make('total_amount')
                    ->money('UGX')
                    ->sortable(),

                // Overall Booking Status
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'in_progress' => 'info',
                        'pending' => 'warning',
                        'completed' => 'gray',
                        'cancelled', 'rejected' => 'danger',
                        default => 'gray',
                    }),

                // Financial Status
                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'danger',
                        'refunded' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status'),
                \Filament\Tables\Filters\SelectFilter::make('payment_status'),
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
