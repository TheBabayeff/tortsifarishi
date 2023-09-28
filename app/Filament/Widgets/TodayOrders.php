<?php

namespace App\Filament\Widgets;

use App\Models\InternalOrder;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TodayOrders extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                InternalOrder::where('date', Carbon::today())
            )
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Müştəri adı') ,
                TextColumn::make('id')
                    ->label('Sifariş kodu')
                    ->url(fn(InternalOrder $record): string => route('filament.admin.resources.shop.internal-orders.edit', $record)),
            ]);
    }
}
