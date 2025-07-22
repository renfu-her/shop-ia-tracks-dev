<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponCodeResource\Pages;
use App\Models\CouponCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class CouponCodeResource extends Resource
{
    protected static ?string $model = CouponCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationGroup = '優惠券管理';

    protected static ?string $navigationLabel = '優惠券代碼';

    protected static ?string $modelLabel = '優惠券代碼';

    protected static ?string $pluralModelLabel = '優惠券代碼';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資訊')
                    ->schema([
                        Select::make('coupon_id')
                            ->label('優惠券')
                            ->relationship('coupon', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('code')
                            ->label('優惠券代碼')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->helperText('請輸入唯一的優惠券代碼'),

                        Select::make('member_id')
                            ->label('擁有者會員')
                            ->relationship('member', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('留空表示未分配給任何會員'),

                        Select::make('gifted_by_member_id')
                            ->label('贈送者會員')
                            ->relationship('giftedByMember', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('留空表示非贈送獲得'),
                    ])->columns(2),

                Forms\Components\Section::make('狀態設定')
                    ->schema([
                        Select::make('status')
                            ->label('狀態')
                            ->options([
                                'unused' => '未使用',
                                'used' => '已使用',
                                'expired' => '已過期',
                                'gifted' => '已贈送',
                            ])
                            ->default('unused')
                            ->required(),

                        DateTimePicker::make('used_at')
                            ->label('使用時間')
                            ->helperText('系統自動記錄'),

                        DateTimePicker::make('gifted_at')
                            ->label('贈送時間')
                            ->helperText('系統自動記錄'),

                        DateTimePicker::make('accepted_at')
                            ->label('接受時間')
                            ->helperText('系統自動記錄'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('優惠券代碼')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->monospace(),

                TextColumn::make('coupon.name')
                    ->label('優惠券名稱')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('coupon.discount_amount')
                    ->label('折扣金額')
                    ->formatStateUsing(fn (CouponCode $record): string => 
                        $record->coupon && $record->coupon->discount_type === 'percentage' 
                            ? "{$record->coupon->discount_amount}%" 
                            : ($record->coupon ? "NT$ {$record->coupon->discount_amount}" : '-')
                    ),

                TextColumn::make('member.name')
                    ->label('擁有者')
                    ->placeholder('未分配')
                    ->searchable(),

                TextColumn::make('gifted_by_member.name')
                    ->label('贈送者')
                    ->placeholder('非贈送')
                    ->searchable(),

                BadgeColumn::make('status')
                    ->label('狀態')
                    ->colors([
                        'primary' => 'unused',
                        'success' => 'used',
                        'danger' => 'expired',
                        'warning' => 'gifted',
                    ])
                    ->formatStateUsing(fn (string $state): string => 
                        match($state) {
                            'unused' => '未使用',
                            'used' => '已使用',
                            'expired' => '已過期',
                            'gifted' => '已贈送',
                            default => $state,
                        }
                    ),

                TextColumn::make('used_at')
                    ->label('使用時間')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('未使用')
                    ->sortable(),

                TextColumn::make('gifted_at')
                    ->label('贈送時間')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('非贈送')
                    ->sortable(),

                TextColumn::make('accepted_at')
                    ->label('接受時間')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('未接受')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('coupon_id')
                    ->label('優惠券')
                    ->relationship('coupon', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('狀態')
                    ->options([
                        'unused' => '未使用',
                        'used' => '已使用',
                        'expired' => '已過期',
                        'gifted' => '已贈送',
                    ]),

                TernaryFilter::make('member_id')
                    ->label('是否已分配')
                    ->placeholder('所有代碼')
                    ->trueLabel('已分配')
                    ->falseLabel('未分配'),

                TernaryFilter::make('gifted_by_member_id')
                    ->label('是否為贈送')
                    ->placeholder('所有代碼')
                    ->trueLabel('贈送獲得')
                    ->falseLabel('直接獲得'),
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
            'index' => Pages\ListCouponCodes::route('/'),
            'create' => Pages\CreateCouponCode::route('/create'),
            'edit' => Pages\EditCouponCode::route('/{record}/edit'),
        ];
    }
}
