<?php

namespace App\Filament\Resources\Amazings;

use App\Filament\Resources\Amazings\Pages\CreateAmazing;
use App\Filament\Resources\Amazings\Pages\EditAmazing;
use App\Filament\Resources\Amazings\Pages\ListAmazings;
use App\Filament\Resources\Amazings\Schemas\AmazingForm;
use App\Filament\Resources\Amazings\Tables\AmazingsTable;
use App\Models\Amazing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AmazingResource extends Resource
{
    protected static ?string $model = Amazing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static string|UnitEnum|null $navigationGroup = 'فروش';

    protected static ?int $navigationSort = 6;

    public static function getModelLabel(): string
    {
        return 'فروش شگفت‌انگیز';
    }

    public static function getPluralModelLabel(): string
    {
        return 'فروش‌های شگفت‌انگیز';
    }

    public static function form(Schema $schema): Schema
    {
        return AmazingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AmazingsTable::configure($table);
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
            'index' => ListAmazings::route('/'),
            'create' => CreateAmazing::route('/create'),
            'edit' => EditAmazing::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['product']);
    }
}
