<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\ProductCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = '商品管理';
    protected static ?string $navigationLabel = '商品管理';
    protected static ?string $modelLabel = '商品';
    protected static ?string $pluralModelLabel = '商品';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資訊')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('商品名稱')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('網址代碼')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('category_id')
                            ->label('商品分類')
                            ->options(ProductCategory::active()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('barcode')
                            ->label('條碼')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('特色商品')
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->label('啟用狀態')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('價格與庫存')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('原價')
                            ->required()
                            ->numeric()
                            ->prefix('NT$')
                            ->minValue(0),
                        Forms\Components\TextInput::make('sale_price')
                            ->label('特價')
                            ->numeric()
                            ->prefix('NT$')
                            ->minValue(0),
                        Forms\Components\TextInput::make('stock')
                            ->label('庫存')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])->columns(3),

                Forms\Components\Section::make('商品描述')
                    ->schema([
                        Forms\Components\Textarea::make('short_description')
                            ->label('簡短描述')
                            ->rows(3)
                            ->maxLength(500),
                        TinyEditor::make('description')
                            ->label('詳細描述')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('商品規格')
                    ->schema([
                        Forms\Components\KeyValue::make('specifications')
                            ->label('商品規格')
                            ->keyLabel('規格名稱')
                            ->valueLabel('規格內容')
                            ->addActionLabel('新增規格')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('商品標籤')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('商品標籤')
                            ->separator(',')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('發布設定')
                    ->schema([
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('發布時間')
                            ->default(now()),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('商品名稱')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('分類')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('原價')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->label('特價')
                    ->money('TWD')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('current_price')
                    ->label('當前價格')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('庫存')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('特色')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('狀態')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('商品分類')
                    ->options(ProductCategory::active()->pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('特色商品'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('啟用狀態'),
                Tables\Filters\Filter::make('in_stock')
                    ->label('有庫存')
                    ->query(fn ($query) => $query->where('stock', '>', 0)),
                Tables\Filters\Filter::make('out_of_stock')
                    ->label('缺貨')
                    ->query(fn ($query) => $query->where('stock', '<=', 0)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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
