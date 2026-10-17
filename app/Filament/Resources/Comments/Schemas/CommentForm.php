<?php

namespace App\Filament\Resources\Comments\Schemas;

use App\Models\Product;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('مشتری')
                    ->options(fn () => User::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('product_id')
                    ->label('کالا')
                    ->options(fn () => Product::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('rating')
                    ->label('امتیاز (۱ تا ۵)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->nullable(),
                Toggle::make('status')
                    ->label('تأیید شده'),
                Textarea::make('body')
                    ->label('متن دیدگاه')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
