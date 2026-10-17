<?php

namespace App\Filament\Resources\ProductAttrs\Pages;

use App\Filament\Resources\ProductAttrs\ProductAttrResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProductAttr extends EditRecord
{
    protected static string $resource = ProductAttrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
