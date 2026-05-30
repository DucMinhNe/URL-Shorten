<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdCampaignResource\Pages;
use App\Filament\Resources\AdCampaignResource\RelationManagers;
use App\Models\AdCampaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdCampaignResource extends Resource
{
    protected static ?string $model = AdCampaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Quảng cáo';
    protected static ?string $navigationLabel = 'Quảng cáo';
    protected static ?string $modelLabel = 'chiến dịch';
    protected static ?string $pluralModelLabel = 'chiến dịch';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Tên')->required(),
            Forms\Components\Select::make('placement')->label('Vị trí')->options(\App\Support\Labels::options('ad_placement'))->required(),
            Forms\Components\Select::make('type')->label('Loại')->options(\App\Support\Labels::options('ad_type'))->required()->reactive(),
            Forms\Components\Textarea::make('content')->label('Nội dung')->required()->rows(4)->helperText('URL ảnh, mã HTML, hoặc URL iframe'),
            Forms\Components\TextInput::make('target_url')->label('URL đích')->url()->nullable(),
            Forms\Components\TextInput::make('weight')->label('Trọng số')->numeric()->default(1)->minValue(1)->maxValue(100),
            Forms\Components\Select::make('status')->label('Trạng thái')->options(\App\Support\Labels::options('ad_status'))->required(),
            Forms\Components\DateTimePicker::make('start_at')->label('Bắt đầu')->nullable(),
            Forms\Components\DateTimePicker::make('end_at')->label('Kết thúc')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Tên')->searchable(),
            Tables\Columns\TextColumn::make('placement')->label('Vị trí')->badge()->formatStateUsing(fn ($state) => \App\Support\Labels::get('ad_placement', $state)),
            Tables\Columns\TextColumn::make('type')->label('Loại')->formatStateUsing(fn ($state) => \App\Support\Labels::get('ad_type', $state)),
            Tables\Columns\TextColumn::make('weight')->label('Trọng số'),
            Tables\Columns\TextColumn::make('impressions')->label('Lượt hiện')->numeric(),
            Tables\Columns\TextColumn::make('clicks_count')->numeric()->label('Lượt click'),
            Tables\Columns\TextColumn::make('ctr')->label('CTR')->state(fn($record)=>$record->impressions>0?round($record->clicks_count/$record->impressions*100,2).'%':'-'),
            Tables\Columns\TextColumn::make('status')->label('Trạng thái')->badge()->colors(['success'=>'active','warning'=>'paused'])->formatStateUsing(fn ($state) => \App\Support\Labels::get('ad_status', $state)),
        ])->filters([
            Tables\Filters\SelectFilter::make('placement')->label('Vị trí')->options(\App\Support\Labels::options('ad_placement')),
            Tables\Filters\SelectFilter::make('status')->label('Trạng thái')->options(\App\Support\Labels::options('ad_status')),
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
            'index' => Pages\ListAdCampaigns::route('/'),
            'create' => Pages\CreateAdCampaign::route('/create'),
            'edit' => Pages\EditAdCampaign::route('/{record}/edit'),
        ];
    }
}
