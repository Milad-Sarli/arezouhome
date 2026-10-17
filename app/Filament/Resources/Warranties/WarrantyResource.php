<?php

namespace App\Filament\Resources\Warranties;

use App\Filament\Resources\Warranties\Pages\CreateWarranty;
use App\Filament\Resources\Warranties\Pages\EditWarranty;
use App\Filament\Resources\Warranties\Pages\ListWarranties;
use App\Filament\Resources\Warranties\Schemas\WarrantyForm;
use App\Filament\Resources\Warranties\Tables\WarrantiesTable;
use App\Models\Warranty;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class WarrantyResource extends Resource
{
    protected static ?string $model = Warranty::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'کاتالوگ';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getModelLabel(): string
    {
        return 'گارانتی';
    }

    public static function getPluralModelLabel(): string
    {
        return 'گارانتی‌ها';
    }

    public static function form(Schema $schema): Schema
    {
        return WarrantyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarrantiesTable::configure($table);
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
            'index' => ListWarranties::route('/'),
            'create' => CreateWarranty::route('/create'),
            'edit' => EditWarranty::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('products');
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
