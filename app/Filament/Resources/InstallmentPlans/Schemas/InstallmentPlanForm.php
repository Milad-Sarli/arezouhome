<?php

namespace App\Filament\Resources\InstallmentPlans\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class InstallmentPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                TextInput::make('down_payment_percent')
                    ->label('پیش‌پرداخت')
                    ->numeric()
                    ->default(0)
                    ->suffix('٪'),
                TextInput::make('months')
                    ->label('تعداد اقساط')
                    ->numeric()
                    ->default(12)
                    ->suffix('ماه'),
                TextInput::make('fee_percent')
                    ->label('کارمزد')
                    ->numeric()
                    ->default(0)
                    ->suffix('٪'),
                Toggle::make('status')
                    ->label('فعال')
                    ->default(true),
                Textarea::make('description')
                    ->label('شرایط')
                    ->columnSpanFull(),
            ]);
    }
}
