<?php

namespace App\Filament\Resources\SmsMessages;

use App\Filament\Resources\SmsMessages\Pages\CreateSmsMessage;
use App\Filament\Resources\SmsMessages\Pages\EditSmsMessage;
use App\Filament\Resources\SmsMessages\Pages\ListSmsMessages;
use App\Filament\Resources\SmsMessages\Schemas\SmsMessageForm;
use App\Filament\Resources\SmsMessages\Tables\SmsMessagesTable;
use App\Models\SmsMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SmsMessageResource extends Resource
{
    protected static ?string $model = SmsMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleOvalLeftEllipsis;

    protected static string|UnitEnum|null $navigationGroup = 'پیامک';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return 'متن پیامک';
    }

    public static function getPluralModelLabel(): string
    {
        return 'متن‌های پیامک';
    }

    public static function form(Schema $schema): Schema
    {
        return SmsMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmsMessagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSmsMessages::route('/'),
            'create' => CreateSmsMessage::route('/create'),
            'edit' => EditSmsMessage::route('/{record}/edit'),
        ];
    }
}
