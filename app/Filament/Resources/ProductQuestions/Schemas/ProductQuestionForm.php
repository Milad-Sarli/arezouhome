<?php

namespace App\Filament\Resources\ProductQuestions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('کالا')
                    ->relationship('product', 'name')
                    ->required(),
                Select::make('user_id')
                    ->label('کاربر')
                    ->relationship('user', 'name')
                    ->required(),
                Textarea::make('question')
                    ->label('پرسش')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('answer')
                    ->label('پاسخ')
                    ->columnSpanFull(),
                Toggle::make('is_answered')
                    ->label('پاسخ داده شده')
                    ->required(),
                Toggle::make('status')
                    ->label('نمایش در سایت')
                    ->required(),
            ]);
    }
}
