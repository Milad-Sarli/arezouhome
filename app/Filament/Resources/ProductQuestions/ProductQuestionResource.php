<?php

namespace App\Filament\Resources\ProductQuestions;

use App\Filament\Resources\ProductQuestions\Pages\CreateProductQuestion;
use App\Filament\Resources\ProductQuestions\Pages\EditProductQuestion;
use App\Filament\Resources\ProductQuestions\Pages\ListProductQuestions;
use App\Filament\Resources\ProductQuestions\Schemas\ProductQuestionForm;
use App\Filament\Resources\ProductQuestions\Tables\ProductQuestionsTable;
use App\Models\ProductQuestion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductQuestionResource extends Resource
{
    protected static ?string $model = ProductQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'پرسش کالا';

    protected static ?string $pluralModelLabel = 'پرسش‌های کالا';

    public static function form(Schema $schema): Schema
    {
        return ProductQuestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductQuestionsTable::configure($table);
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
            'index' => ListProductQuestions::route('/'),
            'create' => CreateProductQuestion::route('/create'),
            'edit' => EditProductQuestion::route('/{record}/edit'),
        ];
    }
}
