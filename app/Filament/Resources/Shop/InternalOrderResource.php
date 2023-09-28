<?php

namespace App\Filament\Resources\Shop;

use App\Filament\Resources\Shop\InternalOrderResource\Pages\CreateInternalOrder;
use App\Filament\Resources\Shop\InternalOrderResource\Pages\EditInternalOrder;
use App\Filament\Resources\Shop\InternalOrderResource\Pages\ListInternalOrders;
use App\Filament\Resources\Shop\InternalOrderResource\Widgets\InternalOrderStats;
use App\Filament\Resources\Shop\OrderResource\Pages;
use App\Filament\Resources\Shop\OrderResource\RelationManagers;
use App\Filament\Resources\Shop\OrderResource\Widgets\OrderStats;
use App\Forms\Components\AddressForm;
use App\Models\InternalOrder;
use App\Models\Shop\Product;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Squire\Models\Currency;

class InternalOrderResource extends Resource
{
    protected static ?string $model = InternalOrder::class;

    protected static ?string $slug = 'shop/internal-orders';

    //protected static ?string $recordTitleAttribute = 'number';

    //protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema(static::getFormSchema())
                            ->columns(2),

                    ])
                    ->columnSpan(['lg' => fn (?InternalOrder $record) => $record === null ? 3 : 2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (InternalOrder $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (InternalOrder $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?InternalOrder $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'processing',
                        'success' => fn ($state) => in_array($state, ['delivered', 'shipped']),
                    ]),

                Tables\Columns\TextColumn::make('total')
                    ->searchable()
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('azn'),
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->date()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Order from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
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
                    ->label('Order Date')
                    ->date()
                    ->collapsible(),
            ]);
    }



    public static function getWidgets(): array
    {
        return [
            InternalOrderStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInternalOrders::route('/'),
            'create' => CreateInternalOrder::route('/create'),
            'edit' => EditInternalOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('created_at', 'DESC')->withoutGlobalScope(SoftDeletingScope::class);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'customer.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Order $record */

        return [
            'Customer' => optional($record->customer)->name,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['customer',]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::$model::where('status', 'new')
            ->orWhere('status', 'processing')
            ->count();
    }

    public static function getFormSchema(string $section = null): array
    {

        return [

            Forms\Components\Select::make('shop_customer_id')
                ->relationship('customer', 'name')
                ->searchable()
                ->required()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required(),


                    Forms\Components\TextInput::make('phone'),

                    Forms\Components\Select::make('gender')
                        ->placeholder('Select gender')
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female',
                        ])
                        ->required()
                        ->native(false),
                ])
                ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                    return $action
                        ->modalHeading('Create customer')
                        ->modalButton('Create customer')
                        ->modalWidth('lg');
                }),

            Forms\Components\Select::make('status')
                ->options([
                    'new' => 'New',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ])
                ->required()
                ->native(false),

            TextInput::make('total')
                ->label('Sifarişin Qiyməti')
                ->placeholder('Sifarişin qiymətini yazın ₼')
                ->required(),

            TextInput::make('beh')
                ->placeholder('Beh verilibsə qeyd edin ₼'),

            Forms\Components\DatePicker::make('date')
                ->placeholder('Sifarişin Tarixini seçin')
                ->required(),

            Forms\Components\TimePicker::make('time')
                ->withoutSeconds()
                ->placeholder('Sifarişin Saatını seçin')
                ->required(),

            SpatieMediaLibraryFileUpload::make('image')
                ->collection('order-photo')
                ->multiple(),

            Forms\Components\Textarea::make('description')
                ->placeholder('Əlavə Bütün qeydlərinizi yazın'),

        ];
    }
}
