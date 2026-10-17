<?php

namespace App\Filament\Resources\SmsMessages\Schemas;

use App\Models\SmsTopic;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SmsMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('topic_id')
                    ->label('موضوع')
                    ->options(fn () => SmsTopic::pluck('title', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(['draft' => 'پیش‌نویس', 'sent' => 'ارسال شده'])
                    ->default('draft'),
                Textarea::make('body')
                    ->label('متن پیامک')
                    ->required()
                    ->columnSpanFull(),
                Hidden::make('created_by')
                    ->default(fn () => auth()->id()),
            ]);
    }
}
