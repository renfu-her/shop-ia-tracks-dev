<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = '網站管理';
    protected static ?string $navigationLabel = '輪播管理';
    protected static ?string $modelLabel = '輪播';
    protected static ?string $pluralModelLabel = '輪播';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資訊')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('標題')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('描述')
                            ->rows(3)
                            ->maxLength(1000),
                        Forms\Components\Select::make('type')
                            ->label('輪播類型')
                            ->options([
                                'homepage' => '首頁',
                                'product' => '商品',
                                'about' => '關於我們',
                            ])
                            ->required()
                            ->default('homepage')
                            ->reactive(),
                        Forms\Components\Select::make('product_id')
                            ->label('關聯商品')
                            ->options(Product::active()->pluck('name', 'id'))
                            ->searchable()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'product'),
                        Forms\Components\TextInput::make('link')
                            ->label('連結網址')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('排序')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('啟用狀態')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('輪播圖片')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('輪播圖片')
                            ->image()
                            ->imageEditor()
                            ->directory('banners')
                            ->columnSpanFull()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->downloadable()
                            ->openable()
                            ->required(),
                    ]),

                Forms\Components\Section::make('時間設定')
                    ->schema([
                        Flatpickr::make('start_at')
                            ->label('開始時間')
                            ->dateFormat('Y-m-d H:i')
                            ->allowInput()
                            ->altInput(true)
                            ->altFormat('Y-m-d H:i')
                            ->customConfig([
                                'locale' => 'zh_tw',
                                'enableTime' => true,
                                'dateFormat' => 'Y-m-d H:i',
                            ]),
                        Flatpickr::make('end_at')
                            ->label('結束時間')
                            ->dateFormat('Y-m-d H:i')
                            ->allowInput()
                            ->altInput(true)
                            ->altFormat('Y-m-d H:i')
                            ->customConfig([
                                'locale' => 'zh_tw',
                                'enableTime' => true,
                                'dateFormat' => 'Y-m-d H:i',
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('圖片')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->label('標題')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('類型')
                    ->colors([
                        'primary' => 'homepage',
                        'success' => 'product',
                        'warning' => 'about',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'homepage' => '首頁',
                        'product' => '商品',
                        'about' => '關於我們',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('關聯商品')
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
                Tables\Columns\TextColumn::make('start_at')
                    ->label('開始時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->label('結束時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('輪播類型')
                    ->options([
                        'homepage' => '首頁',
                        'product' => '商品',
                        'about' => '關於我們',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('啟用狀態'),
                Tables\Filters\Filter::make('valid')
                    ->label('有效輪播')
                    ->query(fn ($query) => $query->valid()),
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
