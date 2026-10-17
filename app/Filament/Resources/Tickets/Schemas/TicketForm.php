<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Models\User;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TicketForm
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
                TextInput::make('subject')
                    ->label('موضوع')
                    ->required()
                    ->maxLength(255),
                Select::make('department')
                    ->label('دپارتمان')
                    ->options([
                        'support' => 'پشتیبانی',
                        'sales' => 'فروش',
                        'warranty' => 'گارانتی',
                    ])
                    ->default('support'),
                Select::make('priority')
                    ->label('اولویت')
                    ->options(['low' => 'کم', 'medium' => 'متوسط', 'high' => 'زیاد'])
                    ->default('medium'),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(['open' => 'باز', 'answered' => 'پاسخ داده شده', 'closed' => 'بسته'])
                    ->default('open'),
                Repeater::make('messages')
                    ->label('گفتگو')
                    ->relationship()
                    ->schema([
                        Select::make('user_id')
                            ->label('فرستنده')
                            ->options(fn () => User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Textarea::make('message')
                            ->label('پیام')
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
