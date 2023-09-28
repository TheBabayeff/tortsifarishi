<?php

namespace App\Filament\Resources\Shop\ReasonResource\Pages;

use App\Filament\Resources\Shop\ReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReasons extends ListRecords
{
    protected static string $resource = ReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
