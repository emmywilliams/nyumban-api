<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),

                // Link to the Tenant via the Booking relationship
                TextColumn::make('booking.tenant.name')
                    ->label('Tenant')
                    ->searchable(),

                TextColumn::make('amount')
                    ->money('UGX') // Automatically formats as currency
                    ->sortable()
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->label('Total Revenue')),

                TextColumn::make('provider')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('transaction_ref')
                    ->label('Ref')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                    }),

            ])
            ->filters([

                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'pending' => 'Pending',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('provider')
                    ->options([
                        'mtn_momo' => 'MTN MoMo',
                        'airtel_money' => 'Airtel Money',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteBulkAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
