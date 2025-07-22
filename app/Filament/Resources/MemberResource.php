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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

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
                        TextInput::make('name')
                            ->label('姓名')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('電子郵件')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')
                            ->label('電話')
                            ->tel()
                            ->maxLength(255),
                        Select::make('gender')
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
                        Select::make('county')
                            ->label('縣市')
                            ->options([
                                '台北市' => '台北市',
                                '新北市' => '新北市',
                                '桃園市' => '桃園市',
                                '台中市' => '台中市',
                                '台南市' => '台南市',
                                '高雄市' => '高雄市',
                                '基隆市' => '基隆市',
                                '新竹市' => '新竹市',
                                '新竹縣' => '新竹縣',
                                '苗栗縣' => '苗栗縣',
                                '彰化縣' => '彰化縣',
                                '南投縣' => '南投縣',
                                '雲林縣' => '雲林縣',
                                '嘉義市' => '嘉義市',
                                '嘉義縣' => '嘉義縣',
                                '屏東縣' => '屏東縣',
                                '宜蘭縣' => '宜蘭縣',
                                '花蓮縣' => '花蓮縣',
                                '台東縣' => '台東縣',
                                '澎湖縣' => '澎湖縣',
                                '金門縣' => '金門縣',
                                '連江縣' => '連江縣',
                            ])
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // 根據縣市設定郵遞區號
                                $zipcodes = [
                                    '台北市' => '100',
                                    '新北市' => '220',
                                    '桃園市' => '320',
                                    '台中市' => '400',
                                    '台南市' => '700',
                                    '高雄市' => '800',
                                    '基隆市' => '200',
                                    '新竹市' => '300',
                                    '新竹縣' => '302',
                                    '苗栗縣' => '350',
                                    '彰化縣' => '500',
                                    '南投縣' => '540',
                                    '雲林縣' => '630',
                                    '嘉義市' => '600',
                                    '嘉義縣' => '612',
                                    '屏東縣' => '900',
                                    '宜蘭縣' => '260',
                                    '花蓮縣' => '970',
                                    '台東縣' => '950',
                                    '澎湖縣' => '880',
                                    '金門縣' => '890',
                                    '連江縣' => '209',
                                ];
                                
                                if (isset($zipcodes[$state])) {
                                    $set('zipcode', $zipcodes[$state]);
                                }
                            }),

                        Select::make('district')
                            ->label('區')
                            ->options(function (callable $get) {
                                $county = $get('county');
                                $districts = [
                                    '台北市' => [
                                        '中正區' => '中正區',
                                        '大同區' => '大同區',
                                        '中山區' => '中山區',
                                        '松山區' => '松山區',
                                        '大安區' => '大安區',
                                        '萬華區' => '萬華區',
                                        '信義區' => '信義區',
                                        '士林區' => '士林區',
                                        '北投區' => '北投區',
                                        '內湖區' => '內湖區',
                                        '南港區' => '南港區',
                                        '文山區' => '文山區',
                                    ],
                                    '新北市' => [
                                        '板橋區' => '板橋區',
                                        '三重區' => '三重區',
                                        '中和區' => '中和區',
                                        '永和區' => '永和區',
                                        '新莊區' => '新莊區',
                                        '新店區' => '新店區',
                                        '樹林區' => '樹林區',
                                        '鶯歌區' => '鶯歌區',
                                        '三峽區' => '三峽區',
                                        '淡水區' => '淡水區',
                                        '汐止區' => '汐止區',
                                        '瑞芳區' => '瑞芳區',
                                        '土城區' => '土城區',
                                        '蘆洲區' => '蘆洲區',
                                        '五股區' => '五股區',
                                        '泰山區' => '泰山區',
                                        '林口區' => '林口區',
                                        '深坑區' => '深坑區',
                                        '石碇區' => '石碇區',
                                        '坪林區' => '坪林區',
                                        '三芝區' => '三芝區',
                                        '石門區' => '石門區',
                                        '八里區' => '八里區',
                                        '平溪區' => '平溪區',
                                        '雙溪區' => '雙溪區',
                                        '貢寮區' => '貢寮區',
                                        '金山區' => '金山區',
                                        '萬里區' => '萬里區',
                                        '烏來區' => '烏來區',
                                    ],
                                    '桃園市' => [
                                        '桃園區' => '桃園區',
                                        '中壢區' => '中壢區',
                                        '大溪區' => '大溪區',
                                        '楊梅區' => '楊梅區',
                                        '蘆竹區' => '蘆竹區',
                                        '龜山區' => '龜山區',
                                        '八德區' => '八德區',
                                        '龍潭區' => '龍潭區',
                                        '平鎮區' => '平鎮區',
                                        '新屋區' => '新屋區',
                                        '觀音區' => '觀音區',
                                        '復興區' => '復興區',
                                        '大園區' => '大園區',
                                    ],
                                    '台中市' => [
                                        '中區' => '中區',
                                        '東區' => '東區',
                                        '南區' => '南區',
                                        '西區' => '西區',
                                        '北區' => '北區',
                                        '北屯區' => '北屯區',
                                        '西屯區' => '西屯區',
                                        '南屯區' => '南屯區',
                                        '太平區' => '太平區',
                                        '大里區' => '大里區',
                                        '霧峰區' => '霧峰區',
                                        '烏日區' => '烏日區',
                                        '豐原區' => '豐原區',
                                        '后里區' => '后里區',
                                        '石岡區' => '石岡區',
                                        '東勢區' => '東勢區',
                                        '和平區' => '和平區',
                                        '新社區' => '新社區',
                                        '潭子區' => '潭子區',
                                        '大雅區' => '大雅區',
                                        '神岡區' => '神岡區',
                                        '大肚區' => '大肚區',
                                        '沙鹿區' => '沙鹿區',
                                        '龍井區' => '龍井區',
                                        '梧棲區' => '梧棲區',
                                        '清水區' => '清水區',
                                        '大甲區' => '大甲區',
                                        '外埔區' => '外埔區',
                                        '大安區' => '大安區',
                                    ],
                                    '台南市' => [
                                        '中西區' => '中西區',
                                        '東區' => '東區',
                                        '南區' => '南區',
                                        '北區' => '北區',
                                        '安平區' => '安平區',
                                        '安南區' => '安南區',
                                        '永康區' => '永康區',
                                        '歸仁區' => '歸仁區',
                                        '新化區' => '新化區',
                                        '左鎮區' => '左鎮區',
                                        '玉井區' => '玉井區',
                                        '楠西區' => '楠西區',
                                        '南化區' => '南化區',
                                        '仁德區' => '仁德區',
                                        '關廟區' => '關廟區',
                                        '龍崎區' => '龍崎區',
                                        '官田區' => '官田區',
                                        '麻豆區' => '麻豆區',
                                        '佳里區' => '佳里區',
                                        '西港區' => '西港區',
                                        '七股區' => '七股區',
                                        '將軍區' => '將軍區',
                                        '學甲區' => '學甲區',
                                        '北門區' => '北門區',
                                        '新營區' => '新營區',
                                        '後壁區' => '後壁區',
                                        '白河區' => '白河區',
                                        '東山區' => '東山區',
                                        '六甲區' => '六甲區',
                                        '下營區' => '下營區',
                                        '柳營區' => '柳營區',
                                        '鹽水區' => '鹽水區',
                                        '善化區' => '善化區',
                                        '大內區' => '大內區',
                                        '山上區' => '山上區',
                                        '新市區' => '新市區',
                                        '安定區' => '安定區',
                                    ],
                                    '高雄市' => [
                                        '楠梓區' => '楠梓區',
                                        '左營區' => '左營區',
                                        '鼓山區' => '鼓山區',
                                        '三民區' => '三民區',
                                        '鹽埕區' => '鹽埕區',
                                        '前金區' => '前金區',
                                        '新興區' => '新興區',
                                        '苓雅區' => '苓雅區',
                                        '前鎮區' => '前鎮區',
                                        '旗津區' => '旗津區',
                                        '小港區' => '小港區',
                                        '鳳山區' => '鳳山區',
                                        '林園區' => '林園區',
                                        '大寮區' => '大寮區',
                                        '大樹區' => '大樹區',
                                        '大社區' => '大社區',
                                        '仁武區' => '仁武區',
                                        '鳥松區' => '鳥松區',
                                        '岡山區' => '岡山區',
                                        '橋頭區' => '橋頭區',
                                        '燕巢區' => '燕巢區',
                                        '田寮區' => '田寮區',
                                        '阿蓮區' => '阿蓮區',
                                        '路竹區' => '路竹區',
                                        '湖內區' => '湖內區',
                                        '茄萣區' => '茄萣區',
                                        '永安區' => '永安區',
                                        '彌陀區' => '彌陀區',
                                        '梓官區' => '梓官區',
                                        '旗山區' => '旗山區',
                                        '美濃區' => '美濃區',
                                        '六龜區' => '六龜區',
                                        '甲仙區' => '甲仙區',
                                        '杉林區' => '杉林區',
                                        '內門區' => '內門區',
                                        '茂林區' => '茂林區',
                                        '桃源區' => '桃源區',
                                        '那瑪夏區' => '那瑪夏區',
                                    ],
                                ];
                                
                                return $districts[$county] ?? [];
                            })
                            ->searchable()
                            ->reactive()
                            ->dependsOn('county'),

                        TextInput::make('zipcode')
                            ->label('郵遞區號')
                            ->maxLength(10)
                            ->disabled()
                            ->helperText('系統根據縣市自動填入'),

                        Textarea::make('address')
                            ->label('詳細地址')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('例：中山路123號5樓')
                            ->helperText('請輸入街道名稱、門牌號碼等詳細地址'),
                    ])->columns(2),

                Forms\Components\Section::make('密碼設定')
                    ->schema([
                        TextInput::make('password')
                            ->label('密碼')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8),
                        TextInput::make('password_confirmation')
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
                Tables\Columns\TextColumn::make('full_address')
                    ->label('完整地址')
                    ->searchable(['county', 'district', 'address'])
                    ->toggleable()
                    ->limit(50),
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
                Tables\Filters\SelectFilter::make('county')
                    ->label('縣市')
                    ->options([
                        '台北市' => '台北市',
                        '新北市' => '新北市',
                        '桃園市' => '桃園市',
                        '台中市' => '台中市',
                        '台南市' => '台南市',
                        '高雄市' => '高雄市',
                        '基隆市' => '基隆市',
                        '新竹市' => '新竹市',
                        '新竹縣' => '新竹縣',
                        '苗栗縣' => '苗栗縣',
                        '彰化縣' => '彰化縣',
                        '南投縣' => '南投縣',
                        '雲林縣' => '雲林縣',
                        '嘉義市' => '嘉義市',
                        '嘉義縣' => '嘉義縣',
                        '屏東縣' => '屏東縣',
                        '宜蘭縣' => '宜蘭縣',
                        '花蓮縣' => '花蓮縣',
                        '台東縣' => '台東縣',
                        '澎湖縣' => '澎湖縣',
                        '金門縣' => '金門縣',
                        '連江縣' => '連江縣',
                    ])
                    ->searchable(),
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
