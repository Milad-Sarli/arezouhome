<?php

namespace App\Filament\Resources\Amazings\Pages;

use App\Filament\Resources\Amazings\AmazingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAmazings extends ListRecords
{
    protected static string $resource = AmazingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
