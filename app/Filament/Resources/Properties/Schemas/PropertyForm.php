<?php

namespace App\Filament\Resources\Properties\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use App\Models\User;
use App\Models\District;
use App\Models\County;
use App\Models\SubCounty;
use App\Models\Parish;
use App\Models\Village;

class PropertyForm
{
    public static function configure($schema)
    {
        return $schema
            ->schema([
                Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(150),

                        Textarea::make('description')
                            ->required()
                            ->maxLength(1000),

                        Select::make('categories')
                            ->label('Property Categories')
                            ->multiple()
                            ->relationship(
                                name: 'categories',
                                titleAttribute: 'name',
                                // 2. Add the filter so you only see Property Types, not Room Amenities
                                modifyQueryUsing: fn($query) => $query->where('type', 'property_type')
                            )
                            // ->relationship('categories', 'name')
                            // ->options(
                            //     fn() => \App\Models\Category::where('type', 'property_type')->pluck('name', 'id')
                            // )
                            ->searchable()
                            ->preload(),

                        Select::make('landlord_id')
                            ->label('Landlord')
                            ->options(
                                User::where('role_id', 2) // 👈 landlords only
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload(),

                        Select::make('district_id')
                            ->label('District')
                            ->options(District::pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(fn($set) => $set('county_id', null)),

                        Select::make('county_id')
                            ->label('County')
                            ->options(
                                fn($get) =>
                                County::where('district_id', $get('district_id'))->pluck('name', 'id')
                            )
                            ->searchable()
                            ->reactive()
                            ->disabled(fn($get) => !$get('district_id'))
                            ->afterStateUpdated(fn($set) => $set('sub_county_id', null)),

                        Select::make('sub_county_id')
                            ->label('Sub County')
                            ->options(
                                fn($get) =>
                                SubCounty::where('county_id', $get('county_id'))->pluck('name', 'id')
                            )
                            ->searchable()
                            ->reactive()
                            ->disabled(fn($get) => !$get('county_id'))
                            ->afterStateUpdated(fn($set) => $set('parish_id', null)),

                        Select::make('parish_id')
                            ->label('Parish')
                            ->options(
                                fn($get) =>
                                Parish::where('sub_county_id', $get('sub_county_id'))->pluck('name', 'id')
                            )
                            ->searchable()
                            ->reactive()
                            ->disabled(fn($get) => !$get('sub_county_id'))
                            ->afterStateUpdated(fn($set) => $set('village_id', null)),

                        Select::make('village_id')
                            ->label('Village')
                            ->options(
                                fn($get) =>
                                Village::where('parish_id', $get('parish_id'))->pluck('name', 'id')
                            )
                            ->searchable()
                            ->disabled(fn($get) => !$get('parish_id')),

                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'under_verification' => 'Under Verification',
                            ])
                            ->default('inactive')
                            ->required(),
                    ]),

                Section::make('Address & Features')
                    ->columns(2)
                    ->schema([
                        Textarea::make('address')
                            ->required()
                            ->columnSpanFull(),

                        Toggle::make('is_gated')
                            ->label('Gated Community?'),

                        Toggle::make('is_multi_unit')
                            ->label('Multiple Units/Apartments?'),

                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->helperText('Autofilled from the mobile app'),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->helperText('Autofilled from the mobile app'),

                        FileUpload::make('images')
                            ->label('Upload Property Images')
                            ->image()
                            ->disk('public')
                            ->directory('properties')
                            ->multiple()
                            ->imageEditor()
                            ->reorderable()
                            ->appendFiles()
                            ->columnSpanFull()
                            ->helperText('You can upload multiple images. The first one will be the cover photo.'),

                    ]),


            ]);
    }
}
