<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = '優惠券管理';

    protected static ?string $navigationLabel = '優惠券管理';

    protected static ?string $modelLabel = '優惠券';

    protected static ?string $pluralModelLabel = '優惠券';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資訊')
                    ->schema([
                        TextInput::make('name')
                            ->label('優惠券名稱')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label('優惠券代號')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->helperText('請輸入唯一的優惠券代號'),

                        Textarea::make('description')
                            ->label('優惠券描述')
                            ->rows(3)
                            ->maxLength(1000),
                    ])->columns(2),

                Forms\Components\Section::make('折扣設定')
                    ->schema([
                        TextInput::make('discount_amount')
                            ->label('折扣金額')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01),

                        Select::make('discount_type')
                            ->label('折扣類型')
                            ->options([
                                'fixed' => '固定金額',
                                'percentage' => '百分比',
                            ])
                            ->default('fixed')
                            ->required(),

                        TextInput::make('minimum_amount')
                            ->label('最低消費金額')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->default(0)
                            ->helperText('0 表示無最低消費限制'),
                    ])->columns(3),

                Forms\Components\Section::make('使用限制')
                    ->schema([
                        TextInput::make('max_usage')
                            ->label('最大使用次數')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('0 表示無使用次數限制'),

                        TextInput::make('used_count')
                            ->label('已使用次數')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->disabled()
                            ->helperText('系統自動計算'),
                    ])->columns(2),

                Forms\Components\Section::make('時間設定')
                    ->schema([
                        DateTimePicker::make('start_at')
                            ->label('開始時間')
                            ->required()
                            ->default(now()),

                        DateTimePicker::make('end_at')
                            ->label('結束時間')
                            ->helperText('留空表示無結束時間限制'),
                    ])->columns(2),

                Forms\Components\Section::make('狀態設定')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('啟用狀態')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('優惠券名稱')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('優惠券代號')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('discount_amount')
                    ->label('折扣金額')
                    ->formatStateUsing(fn (Coupon $record): string => 
                        $record->discount_type === 'percentage' 
                            ? "{$record->discount_amount}%" 
                            : "NT$ {$record->discount_amount}"
                    )
                    ->sortable(),

                BadgeColumn::make('discount_type')
                    ->label('折扣類型')
                    ->colors([
                        'primary' => 'fixed',
                        'success' => 'percentage',
                    ])
                    ->formatStateUsing(fn (string $state): string => 
                        match($state) {
                            'fixed' => '固定金額',
                            'percentage' => '百分比',
                            default => $state,
                        }
                    ),

                TextColumn::make('minimum_amount')
                    ->label('最低消費')
                    ->formatStateUsing(fn ($state): string => 
                        $state > 0 ? "NT$ {$state}" : '無限制'
                    )
                    ->sortable(),

                TextColumn::make('usage_info')
                    ->label('使用狀況')
                    ->formatStateUsing(fn (Coupon $record): string => 
                        $record->max_usage > 0 
                            ? "{$record->used_count}/{$record->max_usage}"
                            : "{$record->used_count}/∞"
                    ),

                TextColumn::make('start_at')
                    ->label('開始時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('end_at')
                    ->label('結束時間')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('無限制')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('狀態')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('discount_type')
                    ->label('折扣類型')
                    ->options([
                        'fixed' => '固定金額',
                        'percentage' => '百分比',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('啟用狀態'),

                SelectFilter::make('status')
                    ->label('優惠券狀態')
                    ->options([
                        'active' => '有效',
                        'expired' => '已過期',
                        'unavailable' => '不可用',
                    ])
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            $now = now();
                            return match($data['value']) {
                                'active' => $query->where('is_active', true)
                                                 ->where('start_at', '<=', $now)
                                                 ->where(function ($q) use ($now) {
                                                     $q->whereNull('end_at')
                                                       ->orWhere('end_at', '>=', $now);
                                                 }),
                                'expired' => $query->where(function ($q) use ($now) {
                                    $q->where('end_at', '<', $now)
                                      ->orWhere('is_active', false);
                                }),
                                'unavailable' => $query->where(function ($q) {
                                    $q->where('max_usage', '>', 0)
                                      ->whereRaw('used_count >= max_usage');
                                }),
                            };
                        }
                        return $query;
                    }),
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
