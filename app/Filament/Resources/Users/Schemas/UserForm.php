<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('موبایل')
                    ->tel()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),
                TextInput::make('email')
                    ->label('ایمیل')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->nullable(),
                TextInput::make('national_id')
                    ->label('کد ملی')
                    ->maxLength(20)
                    ->nullable(),
                TextInput::make('password')
                    ->label('رمز عبور')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Select::make('roles')
                    ->label('نقش‌ها')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload(),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ]);
    }
}
