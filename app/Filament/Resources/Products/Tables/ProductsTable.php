<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Support\MoneyComponents;
use App\Models\Product;
use App\Models\StockMovement;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('عکس')
                    ->circular(),
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->description(fn ($record) => $record->product_code),
                TextColumn::make('brand.name')
                    ->label('برند')
                    ->badge()
                    ->sortable(),
                TextColumn::make('categories.title')
                    ->label('دسته‌ها')
                    ->badge(),
                MoneyComponents::tomanColumn('price_rial', 'قیمت'),
                TextColumn::make('quantity')
                    ->label('موجودی')
                    ->badge()
                    ->color(fn ($state) => $state > 10 ? 'success' : ($state > 0 ? 'warning' : 'danger'))
                    ->sortable(),
                TextColumn::make('warranty.title')
                    ->label('گارانتی')
                    ->toggleable(),
                ToggleColumn::make('in_stock')
                    ->label('موجود'),
                ToggleColumn::make('status')
                    ->label('فعال'),
                TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('brand_id')
                    ->label('برند')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('categories')
                    ->label('دسته')
                    ->relationship('categories', 'title')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('in_stock')
                    ->label('موجودی'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('adjustStock')
                    ->label('انبار')
                    ->schema([
                        Select::make('type')
                            ->label('نوع')
                            ->options(['in' => 'ورود', 'out' => 'خروج'])
                            ->default('in')
                            ->required(),
                        TextInput::make('quantity')
                            ->label('تعداد')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        TextInput::make('reason')
                            ->label('دلیل'),
                    ])
                    ->action(function (Product $record, array $data): void {
                        $quantity = (int) $data['quantity'];

                        StockMovement::create([
                            'product_id' => $record->id,
                            'type' => $data['type'],
                            'quantity' => $quantity,
                            'reason' => $data['reason'] ?? null,
                            'user_id' => auth()->id(),
                        ]);

                        $record->quantity = max(0, $record->quantity + ($data['type'] === 'in' ? $quantity : -$quantity));
                        $record->in_stock = $record->quantity > 0;
                        $record->save();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
