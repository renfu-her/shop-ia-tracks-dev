<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCategoryResource\Pages;
use App\Models\ProductCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = '商品管理';
    protected static ?string $navigationLabel = '商品分類';
    protected static ?string $modelLabel = '商品分類';
    protected static ?string $pluralModelLabel = '商品分類';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資訊')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('分類名稱')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('網址代碼')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('parent_id')
                            ->label('上層分類')
                            ->options(ProductCategory::where('id', '!=', request()->route('record'))->pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('選擇上層分類（可選）'),
                        Forms\Components\Textarea::make('description')
                            ->label('分類描述')
                            ->rows(3)
                            ->maxLength(1000),
                        Forms\Components\FileUpload::make('image')
                            ->label('分類圖片')
                            ->image()
                            ->imageEditor()
                            ->directory('categories')
                            ->columnSpanFull()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->downloadable()
                            ->openable(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('排序')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('啟用狀態')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('分類名稱')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        $prefix = str_repeat('— ', $record->level ?? 0);
                        return $prefix . $record->name;
                    }),
                Tables\Columns\TextColumn::make('slug')
                    ->label('網址代碼')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('上層分類')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('圖片')
                    ->circular()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable()
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
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('上層分類')
                    ->options(ProductCategory::pluck('name', 'id'))
                    ->placeholder('所有分類'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('啟用狀態'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
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
            'index' => Pages\ListProductCategories::route('/'),
            'create' => Pages\CreateProductCategory::route('/create'),
            'edit' => Pages\EditProductCategory::route('/{record}/edit'),
        ];
    }
}
