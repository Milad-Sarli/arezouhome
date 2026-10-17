<?php

namespace App\Filament\Resources\ProductAttrs\Pages;

use App\Filament\Resources\ProductAttrs\ProductAttrResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductAttrs extends ListRecords
{
    protected static string $resource = ProductAttrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
