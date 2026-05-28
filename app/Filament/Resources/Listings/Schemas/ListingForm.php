<?php

namespace App\Filament\Resources\Listings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\Section;

class ListingForm
{
    public static function configure($schema)
    {
        return $schema
            ->schema([
                // PHYSICAL UNIT DETAILS (Table: units)
                Section::make('Unit Details')
                    ->description('Physical characteristics of the space.')
                    ->columns(3)
                    ->schema([
                        Select::make('unit_id')
                            ->relationship('unit', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('title') // Listing Title
                            ->label('Public Title')
                            ->placeholder('e.g., Luxury Studio with Balcony')
                            ->required()
                            ->columnSpan(2),
                    ]),

                // PRICING (Table: listings)
                Section::make('Pricing & Terms')
                    ->columns(3)
                    ->schema([
                        TextInput::make('daily_price')
                            ->numeric()
                            ->prefix('UGX')
                            ->label('Daily Rate'),

                        TextInput::make('weekly_price')
                            ->numeric()
                            ->prefix('UGX')
                            ->label('Weekly Rate'),

                        TextInput::make('monthly_price')
                            ->numeric()
                            ->prefix('UGX')
                            ->label('Monthly Rate'),

                        TextInput::make('minimum_stay_days')
                            ->numeric()
                            ->default(1),
                    ]),

                // CONFIGURATION
                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_featured')
                            ->label('Feature on Homepage?'),

                        Select::make('visibility_status')
                            ->options([
                                'visible' => 'Visible',
                                'hidden' => 'Hidden',
                            ])
                            ->default('visible'),

                        // For the JSON "rules" field in your schema
                        KeyValue::make('rules')
                            ->label('House Rules')
                            ->addActionLabel('Add Rule')
                            ->keyLabel('Rule Name')
                            ->valueLabel('Details'),
                    ]),
            ]);
    }
}
