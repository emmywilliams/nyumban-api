<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('booking_id')
                    ->relationship('booking', 'uuid')
                    ->disabled()
                    ->label('Related Booking'),

                TextInput::make('amount')
                    ->numeric()
                    ->prefix('UGX')
                    ->disabled(),

                TextInput::make('transaction_ref')
                    ->label('Transaction Reference')
                    ->required()
                    ->copyable(),

                Select::make('provider')
                    ->options([
                        'mtn_momo' => 'Mtn momo',
                        'airtel_money' => 'Airtel money',
                        'bank' => 'Bank',
                        'paypal' => 'Paypal',
                    ])
                    ->required(),

                Select::make('status')
                    ->options(['pending' => 'Pending', 'success' => 'Success', 'failed' => 'Failed'])
                    ->required()
                    ->native(false),
            ]);
    }
}
