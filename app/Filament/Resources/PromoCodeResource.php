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
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?string $navigationLabel = 'Mã khuyến mãi';
    protected static ?string $modelLabel = 'mã khuyến mãi';
    protected static ?string $pluralModelLabel = 'mã khuyến mãi';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin mã')->schema([
                Forms\Components\TextInput::make('code')->required()->unique(ignoreRecord: true)
                    ->alphaDash()->maxLength(32)->placeholder('WELCOME50K'),
                Forms\Components\TextInput::make('name')->required()->maxLength(80)
                    ->helperText('Tên nội bộ cho admin'),
                Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Cấu hình giá trị')->schema([
                Forms\Components\Select::make('type')->options([
                    'welcome_bonus' => 'Welcome bonus (tặng khi đăng ký)',
                    'bonus_credit' => 'Bonus credit (tặng vào ví)',
                    'rate_boost' => 'Rate boost (tăng đơn giá/click)',
                    'payout_fee_waiver' => 'Miễn phí rút tiền',
                ])->required(),
                Forms\Components\Select::make('value_unit')->options([
                    'vnd' => 'VND', 'percent' => '%',
                ])->default('vnd'),
                Forms\Components\TextInput::make('value')->numeric()->required()
                    ->suffix(fn (\Filament\Forms\Get $get) => $get('value_unit') === 'percent' ? '%' : 'đ'),
                Forms\Components\TextInput::make('min_balance_required')->numeric()->default(0)->suffix('đ'),
            ])->columns(2),

            Forms\Components\Section::make('Giới hạn sử dụng')->schema([
                Forms\Components\TextInput::make('max_redemptions')->numeric()->placeholder('Để trống = không giới hạn'),
                Forms\Components\TextInput::make('max_per_user')->numeric()->default(1),
                Forms\Components\DateTimePicker::make('valid_from')->seconds(false),
                Forms\Components\DateTimePicker::make('valid_until')->seconds(false)->after('valid_from'),
                Forms\Components\Toggle::make('is_active')->default(true)->label('Đang bật'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('code')->fontFamily('mono')->copyable()->searchable()
                ->weight('bold'),
            Tables\Columns\TextColumn::make('name')->searchable()->limit(30),
            Tables\Columns\TextColumn::make('type')->badge(),
            Tables\Columns\TextColumn::make('value')
                ->formatStateUsing(fn ($state, $record) => number_format($state).' '.($record->value_unit === 'percent' ? '%' : 'đ')),
            Tables\Columns\TextColumn::make('redeemed_count')->label('Đã dùng')->sortable(),
            Tables\Columns\TextColumn::make('max_redemptions')->label('Tối đa')->placeholder('∞'),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
            Tables\Columns\TextColumn::make('valid_until')->dateTime('d/m/Y')->sortable()->placeholder('—'),
        ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options([
                    'welcome_bonus' => 'Welcome', 'bonus_credit' => 'Bonus',
                    'rate_boost' => 'Rate boost', 'payout_fee_waiver' => 'Phí',
                ]),
                Tables\Filters\TernaryFilter::make('is_active'),
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
