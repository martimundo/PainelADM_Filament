<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\RelationManagers\CategoriesRelationManager;
use App\Models\Product;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->minLength(3)
                    ->maxLength(250)
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        $state = Str::slug($state);
                        $set('slug', $state);
                    })
                    ->label(__('Nome Produto')),
                TextInput::make('description')
                    ->minLength(3)
                    ->maxLength(250)
                    ->label(__('Descrição')),
                TextInput::make('price')
                    ->required()
                    ->label(__('Preço')),
                TextInput::make('amount')
                    ->required()
                    ->label(__('Qtde')),
                TextInput::make('slug')
                    ->disabled()
                    ->label(__('Slug')),
                FileUpload::make('photo')
                    ->image()
                    ->directory('products'),
                Select::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                ImageColumn::make('photo')
                    ->circular()
                    ->height(70),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->sortable(),
                TextColumn::make('price')
                    ->searchable()
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('slug')
                    ->sortable(),
                TextColumn::make('created_at')->date('d/M/Y')
            ])
            ->filters([
                Filter::make('price')
                    ->query(fn (Builder $query) => $query->where('price', '>', '10'))->label('Preço'),
                Filter::make('amount')
                    ->query(fn (Builder $query) => $query->where('amount', '>', '0'))->label('Qtde')

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'DESC');
    }

    public static function getRelations(): array
    {
        return [
            CategoriesRelationManager::class
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
}
