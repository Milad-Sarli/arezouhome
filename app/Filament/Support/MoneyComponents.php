<?php

namespace App\Filament\Support;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

/**
 * Money policy: database stores Rial (integer), admin types and sees Toman.
 */
class MoneyComponents
{
    public static function tomanInput(string $name, string $label, bool $required = false): TextInput
    {
        $field = TextInput::make($name)
            ->label($label.' (تومان)')
            ->numeric()
            ->extraInputAttributes(['class' => 'font-toman'])
            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 0)
            ->formatStateUsing(fn ($state) => $state === null ? null : (int) ((int) $state / 10))
            ->dehydrateStateUsing(fn ($state) => $state === null || $state === '' ? 0 : (int) preg_replace('/\D/', '', (string) $state) * 10);

        return $required ? $field->required() : $field;
    }

    public static function tomanColumn(string $name, string $label): TextColumn
    {
        return TextColumn::make($name)
            ->label($label)
            ->extraCellAttributes(['class' => 'font-toman'])
            ->formatStateUsing(fn ($state) => number_format((int) $state / 10).' تومان')
            ->sortable();
    }
}
