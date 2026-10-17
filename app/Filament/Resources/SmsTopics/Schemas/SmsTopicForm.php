<?php

namespace App\Filament\Resources\SmsTopics\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SmsTopicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->columnSpanFull(),
            ]);
    }
}
