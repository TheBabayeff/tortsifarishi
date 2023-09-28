<?php

namespace App\Filament\Resources\Shop\ProductCompositionResource\Pages;

use App\Filament\Resources\Shop\ProductCompositionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductCompositions extends ListRecords
{
    protected static string $resource = ProductCompositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
