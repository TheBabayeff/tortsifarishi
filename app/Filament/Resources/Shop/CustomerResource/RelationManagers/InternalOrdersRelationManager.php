<?php

namespace App\Filament\Resources\Shop\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternalOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'internalOrders';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'processing',
                        'success' => fn ($state) => in_array($state, ['delivered', 'shipped']),
                    ]),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tarix')
                    ->date(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('azn'),
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
