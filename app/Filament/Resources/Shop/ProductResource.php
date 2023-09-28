<?php

namespace App\Filament\Resources\Shop;

use App\Filament\Resources\Shop\ProductResource\Pages;
use App\Filament\Resources\Shop\ProductResource\RelationManagers;
use App\Filament\Resources\Shop\ProductResource\Widgets\ProductStats;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $slug = 'shop/products';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'Products';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
//                                        if ($operation !== 'create') {
//                                            return;
//                                        }

                                        $set('slug', Str::slug($state));
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->unique(Product::class, 'slug', ignoreRecord: true),

                                Forms\Components\MarkdownEditor::make('description')
                                    ->columnSpan('full'),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Images')
                            ->schema([
                                Forms\Components\FileUpload::make('preview_image')
                                    ->label('Əsas şəkil')
                                    ->imageEditor()
                                    ->imageEditorViewportWidth('1080')
                                    ->imageEditorViewportHeight('1080'),
                                Forms\Components\FileUpload::make('product_images')
                                    ->label('Digər şəkillər')
                                    ->columns(1)
                                    ->multiple()
                                    ->directory('products')
                                    ->storeFileNamesIn('original_filename')
                                    ->enableReordering(),
                            ])
                            ->collapsible(),

                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                    ->label('Visible')
                                    ->helperText('Məhsul saytda satışa çıxarılsın ?')
                                    ->default(true),
                                //tortun növü
                                Forms\Components\Select::make('categories')
                                    ->relationship('categories', 'name')
                                    ->label('Tortun növü')
                                    ->multiple()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name'),
                                        Forms\Components\Select::make('parent_id')
                                            ->label('Parent')
                                            ->relationship('parent', 'name', fn (Builder $query) => $query->where('parent_id', null))
                                            ->searchable()
                                            ->placeholder('Select parent category'),
                                        Forms\Components\FileUpload::make('image'),
                                    ])
                                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                        return $action
                                            ->modalHeading('Kateqoriya yarat')
                                            ->modalButton('Create category')
                                            ->modalWidth('lg');
                                    }),
                                //tortun səbəbi
                                Forms\Components\Select::make('product_reasons')
                                    ->relationship('reasons', 'name')
                                    ->label('Tortun verilmə səbəbi')
                                    ->multiple()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {

                                                $set('slug', Str::slug($state));
                                            }),

                                        Forms\Components\TextInput::make('slug')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            ->unique(Product::class, 'slug', ignoreRecord: true),
                                    ])

                                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                        return $action
                                            ->modalHeading('Səbəb yarat')
                                            ->modalButton('Create reasons')
                                            ->modalWidth('lg');
                                    }),

                                //tortun dadi
                                Forms\Components\Select::make('product_compositions')
                                    ->relationship('compositions', 'name')
                                    ->label('Tortun Tərkibi')
                                    ->multiple()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {

                                                $set('slug', Str::slug($state));
                                            }),

                                        Forms\Components\TextInput::make('slug')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            //->unique(Product::class, 'slug', ignoreRecord: true)
                                            ,
                                    ])

                                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                        return $action
                                            ->modalHeading('Tərkib yarat')
                                            ->modalButton('Create composition')
                                            ->modalWidth('lg');
                                    }),

                            ]),

                        Forms\Components\Section::make('Qiymətləndirmə')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->label('Qiymət')
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->required(),

                                Forms\Components\TextInput::make('old_price')
                                    ->label('Köhnə qiymət')
                                    ->numeric()
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('preview_image')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),


                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visibility')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->searchable()
                    ->sortable(),




                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publish Date')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([


                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Visibility')
                    ->boolean()
                    ->trueLabel('Only visible')
                    ->falseLabel('Only hidden')
                    ->native(false),
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
                    ->label('Sıralama')
                    ->date()
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ProductStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }




    public static function getNavigationBadge(): ?string
    {
        return static::$model::all()->count();
    }
}
