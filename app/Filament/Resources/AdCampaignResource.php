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
    protected static ?string $navigationGroup = 'Nội dung';
    protected static ?string $navigationLabel = 'Quảng cáo';
    protected static ?string $modelLabel = 'campaign';
    protected static ?string $pluralModelLabel = 'campaigns';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('placement')->options(['top'=>'Top (728×90)','side'=>'Side (300×250)','bottom'=>'Bottom (728×90)'])->required(),
            Forms\Components\Select::make('type')->options(['banner_image'=>'Banner image URL','html'=>'HTML snippet','iframe'=>'Iframe URL'])->required()->reactive(),
            Forms\Components\Textarea::make('content')->required()->rows(4)->helperText('Image URL, HTML, or iframe URL'),
            Forms\Components\TextInput::make('target_url')->url()->nullable(),
            Forms\Components\TextInput::make('weight')->numeric()->default(1)->minValue(1)->maxValue(100),
            Forms\Components\Select::make('status')->options(['active'=>'Active','paused'=>'Paused'])->required(),
            Forms\Components\DateTimePicker::make('start_at')->nullable(),
            Forms\Components\DateTimePicker::make('end_at')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('placement')->badge(),
            Tables\Columns\TextColumn::make('type'),
            Tables\Columns\TextColumn::make('weight'),
            Tables\Columns\TextColumn::make('impressions')->numeric(),
            Tables\Columns\TextColumn::make('clicks_count')->numeric()->label('Clicks'),
            Tables\Columns\TextColumn::make('ctr')->state(fn($record)=>$record->impressions>0?round($record->clicks_count/$record->impressions*100,2).'%':'-'),
            Tables\Columns\TextColumn::make('status')->badge()->colors(['success'=>'active','warning'=>'paused']),
        ])->filters([
            Tables\Filters\SelectFilter::make('placement')->options(['top'=>'Top','side'=>'Side','bottom'=>'Bottom']),
            Tables\Filters\SelectFilter::make('status')->options(['active'=>'Active','paused'=>'Paused']),
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
