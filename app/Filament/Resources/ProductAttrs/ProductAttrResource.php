<?php

namespace App\Filament\Resources\ProductAttrs;

use App\Filament\Resources\ProductAttrs\Pages\CreateProductAttr;
use App\Filament\Resources\ProductAttrs\Pages\EditProductAttr;
use App\Filament\Resources\ProductAttrs\Pages\ListProductAttrs;
use App\Filament\Resources\ProductAttrs\Schemas\ProductAttrForm;
use App\Filament\Resources\ProductAttrs\Tables\ProductAttrsTable;
use App\Models\ProductAttr;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ProductAttrResource extends Resource
{
    protected static ?string $model = ProductAttr::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static string|UnitEnum|null $navigationGroup = 'کاتالوگ';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'ویژگی';
    }

    public static function getPluralModelLabel(): string
    {
        return 'ویژگی‌ها و مشخصات';
    }

    public static function form(Schema $schema): Schema
    {
        return ProductAttrForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductAttrsTable::configure($table);
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
            'index' => ListProductAttrs::route('/'),
            'create' => CreateProductAttr::route('/create'),
            'edit' => EditProductAttr::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
