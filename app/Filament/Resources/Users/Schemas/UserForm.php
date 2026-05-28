<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100),

                TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null)
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->password()
                    // Only require password when creating a new user
                    ->required(fn(string $context): bool => $context === 'create')
                    // Don't overwrite existing password if left blank on edit
                    ->dehydrated(fn($state) => filled($state)),

                Select::make('role_id')
                    ->relationship('role', 'name')
                    ->preload()
                    ->required(),

                TextInput::make('national_id')
                    ->label('National ID / NIN'),

                FileUpload::make('avatar')
                    ->image()
                    ->avatar()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('avatars')
                    ->preserveFilenames(false)
                    ->columnSpanFull(),

                Select::make('status')
                    ->options(['active' => 'Active', 'suspended' => 'Suspended', 'pending' => 'Pending'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
