<?php

namespace App\Filament\Resources\Provinces;

use App\Filament\Resources\Provinces\Pages\CreateProvince;
use App\Filament\Resources\Provinces\Pages\EditProvince;
use App\Filament\Resources\Provinces\Pages\ListProvinces;
use App\Filament\Resources\Provinces\Schemas\ProvinceForm;
use App\Filament\Resources\Provinces\Tables\ProvincesTable;
use App\Models\Province;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ProvinceResource extends Resource
{
    protected static ?string $model = Province::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static string|UnitEnum|null $navigationGroup = 'مناطق';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'استان';
    }

    public static function getPluralModelLabel(): string
    {
        return 'استان‌ها';
    }

    public static function form(Schema $schema): Schema
    {
        return ProvinceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvincesTable::configure($table);
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
            'index' => ListProvinces::route('/'),
            'create' => CreateProvince::route('/create'),
            'edit' => EditProvince::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('cities');
    }
}
