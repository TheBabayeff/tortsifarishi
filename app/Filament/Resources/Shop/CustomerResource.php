<?php

namespace App\Filament\Resources\Shop;

use App\Filament\Resources\Shop\CustomerResource\Pages;
use App\Filament\Resources\Shop\CustomerResource\RelationManagers;
use App\Models\InternalOrder;
use App\Models\Shop\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Squire\Models\Country;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $slug = 'shop/customers';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->maxValue(50)
                            ->required(),

                        Forms\Components\TextInput::make('phone')
                            ->maxValue(50)
                            //->mask('(999) 999-99-99')
                            ->placeholder('(055) 555-55-55'),
                        Forms\Components\TextInput::make('instagram')
                            ->maxValue(50),

                        Forms\Components\DatePicker::make('birthday')
                            ->maxDate('today'),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => fn (?Customer $record) => $record === null ? 3 : 2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (Customer $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (Customer $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?Customer $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('internal_orders_count')
                    ->label('Sifarişləri')
                    ->sortable()
                    ->getStateUsing(function(Customer $record) {
                        // return whatever you need to show
                        return $record->internalOrders()->count();
                    }),

                Tables\Columns\TextColumn::make('internal_orders')
                    ->label('Son sifarişi')
                    ->url(function (Customer $record) {
                        $latestOrder = $record->internalOrders()->latest('created_at')->first();
                        if ($latestOrder) {
                            return route('filament.admin.resources.shop.internal-orders.edit', $latestOrder);
                        } else {
                            return ''; // Əgər müştərinin sifarişi yoxdursa boş bir URL döndürmək lazımdır .
                        }
                    })
                    ->getStateUsing(function(Customer $record) {
                        $latestOrder = $record->internalOrders()->latest('created_at')->first();

                        return $latestOrder?->id;

                    }),
//                Tables\Columns\TextColumn::make('country')
//                    ->getStateUsing(fn ($record): ?string => Country::find($record->addresses->first()?->country)?->name ?? null),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->action(function () {
                        Notification::make()
                            ->title('Now, now, don\'t be cheeky, leave some records for others to play with!')
                            ->warning()
                            ->send();
                    }),
            ])
            ->groups([
                Tables\Grouping\Group::make('created_at')
                    ->label('Tarixə görə sıralama')
                    ->date()
                    ->collapsible(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('internalOrders')
            //->orderByDesc('internal_orders_count')
            ->with('addresses')
            ->withoutGlobalScope(SoftDeletingScope::class);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InternalOrdersRelationManager::class,
            RelationManagers\AddressesRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone'];
    }
}
