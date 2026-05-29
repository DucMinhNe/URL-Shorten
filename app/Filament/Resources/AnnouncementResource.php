<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Nội dung';
    protected static ?string $navigationLabel = 'Thông báo';
    protected static ?string $modelLabel = 'thông báo';
    protected static ?string $pluralModelLabel = 'thông báo';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Nội dung')->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(120)->columnSpanFull(),
                Forms\Components\RichEditor::make('body')->required()->columnSpanFull()
                    ->disableToolbarButtons(['attachFiles']),
                Forms\Components\Select::make('type')->options([
                    'info' => 'Info (xanh dương)',
                    'success' => 'Success (xanh lá)',
                    'warning' => 'Warning (vàng)',
                    'danger' => 'Danger (đỏ)',
                    'feature' => 'Feature (tím — tính năng mới)',
                ])->default('info')->required(),
                Forms\Components\Select::make('target')->options([
                    'all' => 'Tất cả',
                    'users' => 'User thường',
                    'admins' => 'Chỉ admin',
                    'creators' => 'Creator có link',
                ])->default('all')->required(),
            ])->columns(2),

            Forms\Components\Section::make('Hiển thị')->schema([
                Forms\Components\Toggle::make('is_active')->default(true)->label('Đang bật'),
                Forms\Components\Toggle::make('is_dismissible')->default(true)->label('Cho phép tắt'),
                Forms\Components\Toggle::make('show_on_dashboard')->default(true)->label('Hiện trên dashboard'),
                Forms\Components\Toggle::make('show_on_login')->label('Hiện trang login'),
                Forms\Components\DateTimePicker::make('starts_at')->label('Bắt đầu')->seconds(false),
                Forms\Components\DateTimePicker::make('ends_at')->label('Kết thúc')->seconds(false)->after('starts_at'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
            Tables\Columns\TextColumn::make('type')->badge()->colors([
                'info' => 'info', 'success' => 'success', 'warning' => 'warning',
                'danger' => 'danger', 'purple' => 'feature',
            ]),
            Tables\Columns\TextColumn::make('target')->badge(),
            Tables\Columns\IconColumn::make('is_active')->boolean()->label('Bật'),
            Tables\Columns\TextColumn::make('starts_at')->dateTime('d/m H:i')->sortable(),
            Tables\Columns\TextColumn::make('ends_at')->dateTime('d/m H:i')->sortable(),
            Tables\Columns\TextColumn::make('view_count')->numeric()->sortable()->label('Lượt xem'),
            Tables\Columns\TextColumn::make('creator.name')->label('Tạo bởi'),
        ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options([
                    'info' => 'Info', 'success' => 'Success', 'warning' => 'Warning',
                    'danger' => 'Danger', 'feature' => 'Feature',
                ]),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
