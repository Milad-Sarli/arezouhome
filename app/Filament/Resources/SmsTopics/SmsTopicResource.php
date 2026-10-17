<?php

namespace App\Filament\Resources\SmsTopics;

use App\Filament\Resources\SmsTopics\Pages\CreateSmsTopic;
use App\Filament\Resources\SmsTopics\Pages\EditSmsTopic;
use App\Filament\Resources\SmsTopics\Pages\ListSmsTopics;
use App\Filament\Resources\SmsTopics\Schemas\SmsTopicForm;
use App\Filament\Resources\SmsTopics\Tables\SmsTopicsTable;
use App\Models\SmsTopic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SmsTopicResource extends Resource
{
    protected static ?string $model = SmsTopic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'پیامک';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getModelLabel(): string
    {
        return 'موضوع پیامک';
    }

    public static function getPluralModelLabel(): string
    {
        return 'موضوعات پیامک';
    }

    public static function form(Schema $schema): Schema
    {
        return SmsTopicForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmsTopicsTable::configure($table);
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
            'index' => ListSmsTopics::route('/'),
            'create' => CreateSmsTopic::route('/create'),
            'edit' => EditSmsTopic::route('/{record}/edit'),
        ];
    }
}
