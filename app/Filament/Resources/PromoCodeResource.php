<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Quảng cáo';
    protected static ?string $navigationLabel = 'Mã khuyến mãi';
    protected static ?string $modelLabel = 'mã khuyến mãi';
    protected static ?string $pluralModelLabel = 'mã khuyến mãi';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin mã')->schema([
                Forms\Components\TextInput::make('code')->label('Mã')->required()->unique(ignoreRecord: true)
                    ->alphaDash()->maxLength(32)->placeholder('WELCOME50K'),
                Forms\Components\TextInput::make('name')->label('Tên')->required()->maxLength(80)
                    ->helperText('Tên nội bộ cho admin'),
                Forms\Components\Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Cấu hình giá trị')->schema([
                Forms\Components\Select::make('type')->label('Loại')->options(\App\Support\Labels::options('promo_type'))->required(),
                Forms\Components\Select::make('value_unit')->label('Đơn vị')->options(\App\Support\Labels::options('value_unit'))->default('vnd'),
                Forms\Components\TextInput::make('value')->label('Giá trị')->numeric()->required()
                    ->suffix(fn (\Filament\Forms\Get $get) => $get('value_unit') === 'percent' ? '%' : 'đ'),
                Forms\Components\TextInput::make('min_balance_required')->label('Số dư tối thiểu')->numeric()->default(0)->suffix('đ'),
            ])->columns(2),

            Forms\Components\Section::make('Giới hạn sử dụng')->schema([
                Forms\Components\TextInput::make('max_redemptions')->label('Giới hạn đổi')->numeric()->placeholder('Để trống = không giới hạn'),
                Forms\Components\TextInput::make('max_per_user')->label('Tối đa mỗi người')->numeric()->default(1),
                Forms\Components\DateTimePicker::make('valid_from')->label('Hiệu lực từ')->seconds(false),
                Forms\Components\DateTimePicker::make('valid_until')->label('Hiệu lực đến')->seconds(false)->after('valid_from'),
                Forms\Components\Toggle::make('is_active')->default(true)->label('Đang bật'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('code')->label('Mã')->fontFamily('mono')->copyable()->searchable()
                ->weight('bold'),
            Tables\Columns\TextColumn::make('name')->label('Tên')->searchable()->limit(30),
            Tables\Columns\TextColumn::make('type')->label('Loại')->badge()->formatStateUsing(fn ($state) => \App\Support\Labels::get('promo_type', $state)),
            Tables\Columns\TextColumn::make('value')->label('Giá trị')
                ->formatStateUsing(fn ($state, $record) => number_format($state).' '.($record->value_unit === 'percent' ? '%' : 'đ')),
            Tables\Columns\TextColumn::make('redeemed_count')->label('Lượt đã đổi')->sortable(),
            Tables\Columns\TextColumn::make('max_redemptions')->label('Giới hạn đổi')->placeholder('∞'),
            Tables\Columns\IconColumn::make('is_active')->label('Kích hoạt')->boolean(),
            Tables\Columns\TextColumn::make('valid_until')->label('Hiệu lực đến')->dateTime('d/m/Y')->sortable()->placeholder('—'),
        ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Loại')->options(\App\Support\Labels::options('promo_type')),
                Tables\Filters\TernaryFilter::make('is_active')->label('Kích hoạt'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle')->label(fn ($record) => $record->is_active ? 'Tắt' : 'Bật')
                    ->color(fn ($record) => $record->is_active ? 'gray' : 'success')
                    ->action(fn ($record) => $record->update(['is_active' => ! $record->is_active])),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
