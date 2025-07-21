<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = '網站管理';
    protected static ?string $navigationLabel = '會員管理';
    protected static ?string $modelLabel = '會員';
    protected static ?string $pluralModelLabel = '會員';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資訊')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('姓名')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('電子郵件')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')
                            ->label('電話')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->label('性別')
                            ->options([
                                'male' => '男性',
                                'female' => '女性',
                                'other' => '其他',
                            ]),
                        Forms\Components\DatePicker::make('birthday')
                            ->label('生日'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('啟用狀態')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('地址資訊')
                    ->schema([
                        Forms\Components\KeyValue::make('address')
                            ->label('地址資訊')
                            ->keyLabel('欄位')
                            ->valueLabel('內容')
                            ->addActionLabel('新增欄位')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('密碼設定')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('密碼')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('確認密碼')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->same('password'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('姓名')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('電子郵件')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('電話')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('gender')
                    ->label('性別')
                    ->colors([
                        'primary' => 'male',
                        'success' => 'female',
                        'warning' => 'other',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'male' => '男性',
                        'female' => '女性',
                        'other' => '其他',
                        default => $state,
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('birthday')
                    ->label('生日')
                    ->date('Y-m-d')
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
                    ->label('註冊時間')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->label('性別')
                    ->options([
                        'male' => '男性',
                        'female' => '女性',
                        'other' => '其他',
                    ]),
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
