<?php

namespace App\Filament\Resources\Shop\InternalOrderResource\Pages;

use App\Filament\Resources\Shop\InternalOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInternalOrders extends ListRecords
{
    protected static string $resource = InternalOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => ListRecords\Tab::make('All'),
            'new' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'new')),
            'processing' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'processing')),
            'shipped' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'shipped')),
            'delivered' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'delivered')),
            'cancelled' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'cancelled')),
        ];
    }
}
