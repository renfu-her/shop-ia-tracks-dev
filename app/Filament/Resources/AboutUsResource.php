<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutUsResource\Pages;
use App\Models\AboutUs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class AboutUsResource extends Resource
{
    protected static ?string $model = AboutUs::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationGroup = '網站管理';
    protected static ?string $navigationLabel = '關於我們';
    protected static ?string $modelLabel = '關於我們';
    protected static ?string $pluralModelLabel = '關於我們';
    protected static ?int $navigationSort = 4;

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
                        Forms\Components\FileUpload::make('image')
                            ->label('圖片')
                            ->image()
                            ->imageEditor()
                            ->directory('about')
                            ->columnSpanFull()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->downloadable()
                            ->openable(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('啟用狀態')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('內容')
                    ->schema([
                        TinyEditor::make('content')
                            ->label('內容')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('聯絡資訊')
                    ->schema([
                        Forms\Components\KeyValue::make('contact_info')
                            ->label('聯絡資訊')
                            ->keyLabel('聯絡方式')
                            ->valueLabel('內容')
                            ->addActionLabel('新增聯絡方式')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('社群媒體')
                    ->schema([
                        Forms\Components\KeyValue::make('social_media')
                            ->label('社群媒體')
                            ->keyLabel('平台')
                            ->valueLabel('連結')
                            ->addActionLabel('新增社群媒體')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('標題')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('圖片')
                    ->circular()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('內容')
                    ->limit(100)
                    ->html()
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
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新時間')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            'index' => Pages\ListAboutUs::route('/'),
            'create' => Pages\CreateAboutUs::route('/create'),
            'edit' => Pages\EditAboutUs::route('/{record}/edit'),
        ];
    }
}
