<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberCouponResource\Pages;
use App\Models\MemberCoupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class MemberCouponResource extends Resource
{
    protected static ?string $model = MemberCoupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = '優惠券管理';

    protected static ?string $navigationLabel = '會員優惠券';

    protected static ?string $modelLabel = '會員優惠券';

    protected static ?string $pluralModelLabel = '會員優惠券';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資訊')
                    ->schema([
                        Select::make('member_id')
                            ->label('會員')
                            ->relationship('member', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('coupon_code_id')
                            ->label('優惠券代碼')
                            ->relationship('couponCode', 'code')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('狀態設定')
                    ->schema([
                        Select::make('status')
                            ->label('狀態')
                            ->options([
                                'active' => '有效',
                                'used' => '已使用',
                                'expired' => '已過期',
                            ])
                            ->default('active')
                            ->required(),

                        DateTimePicker::make('used_at')
                            ->label('使用時間')
                            ->helperText('系統自動記錄'),

                        DateTimePicker::make('expired_at')
                            ->label('過期時間')
                            ->helperText('系統自動記錄'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.name')
                    ->label('會員姓名')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('member.email')
                    ->label('會員信箱')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('couponCode.code')
                    ->label('優惠券代碼')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->monospace(),

                TextColumn::make('couponCode.coupon.name')
                    ->label('優惠券名稱')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('couponCode.coupon.discount_amount')
                    ->label('折扣金額')
                    ->formatStateUsing(fn (MemberCoupon $record): string => 
                        $record->couponCode && $record->couponCode->coupon && $record->couponCode->coupon->discount_type === 'percentage' 
                            ? "{$record->couponCode->coupon->discount_amount}%" 
                            : ($record->couponCode && $record->couponCode->coupon ? "NT$ {$record->couponCode->coupon->discount_amount}" : '-')
                    ),

                BadgeColumn::make('status')
                    ->label('狀態')
                    ->colors([
                        'success' => 'active',
                        'primary' => 'used',
                        'danger' => 'expired',
                    ])
                    ->formatStateUsing(fn (string $state): string => 
                        match($state) {
                            'active' => '有效',
                            'used' => '已使用',
                            'expired' => '已過期',
                            default => $state,
                        }
                    ),

                TextColumn::make('used_at')
                    ->label('使用時間')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('未使用')
                    ->sortable(),

                TextColumn::make('expired_at')
                    ->label('過期時間')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('未過期')
                    ->sortable(),

                TextColumn::make('couponCode.gifted_by_member.name')
                    ->label('贈送者')
                    ->placeholder('直接獲得')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('member_id')
                    ->label('會員')
                    ->relationship('member', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('狀態')
                    ->options([
                        'active' => '有效',
                        'used' => '已使用',
                        'expired' => '已過期',
                    ]),

                SelectFilter::make('coupon_code_id')
                    ->label('優惠券代碼')
                    ->relationship('couponCode', 'code')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('couponCode.gifted_by_member_id')
                    ->label('是否為贈送')
                    ->placeholder('所有優惠券')
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
            'index' => Pages\ListMemberCoupons::route('/'),
            'create' => Pages\CreateMemberCoupon::route('/create'),
            'edit' => Pages\EditMemberCoupon::route('/{record}/edit'),
        ];
    }
}
