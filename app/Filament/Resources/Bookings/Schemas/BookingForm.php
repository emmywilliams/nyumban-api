<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;

class BookingForm
{
    public static function configure($schema)
    {
        return $schema
            ->schema([
                Section::make('Booking Details')
                    ->columns(2)
                    ->schema([
                        Select::make('listing_id')
                            ->relationship('listing', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        DatePicker::make('start_date')
                            ->required(),

                        DatePicker::make('end_date')
                            ->required()
                            ->after('start_date'),
                    ]),

                Section::make('Financials & Status')
                    ->columns(3)
                    ->schema([
                        TextInput::make('total_amount')
                            ->numeric()
                            ->required(),

                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),

                        Select::make('payment_status')
                            ->options([
                                'unpaid' => 'Unpaid',
                                'paid' => 'Paid',
                                'refunded' => 'Refunded',
                            ])
                            ->required(),
                    ]),
            ]);
    }
}
