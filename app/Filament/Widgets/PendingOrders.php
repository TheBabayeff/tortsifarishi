<?php

namespace App\Filament\Widgets;

use App\Models\InternalOrder;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingOrders extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                InternalOrder::query()
                    ->where('status', 'processing')
                    ->orWhere('status', 'new')
                    ->orderBy('date', 'ASC')
            )
            ->columns([
                TextColumn::make('date')
                    ->label('Tarix')
                    ->date('d-M'),
                TextColumn::make('customer.name')
                    ->label('Müştəri adı')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Statusu')
                    ->options([
                        'new' => 'New',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                    ->sortable(),
                TextColumn::make('id')
                    ->label('Sifariş kodu')
                    ->searchable()
                    ->url(fn(InternalOrder $record): string => route('filament.admin.resources.shop.internal-orders.edit', $record)),

                TextColumn::make('customer.phone')
                    ->label('Əlaqə')
                    ->searchable(),
            ]);
    }
}
