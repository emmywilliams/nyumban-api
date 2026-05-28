<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('e.g. landlord')
                    ->maxLength(50),

                Hidden::make('guard_name')
                    ->default('web'),

                Textarea::make('description')
                    ->placeholder('What can this user do?')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
