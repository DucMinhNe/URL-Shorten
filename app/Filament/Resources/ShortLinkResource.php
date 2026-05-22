<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShortLinkResource\Pages;
use App\Filament\Resources\ShortLinkResource\RelationManagers;
use App\Models\ShortLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShortLinkResource extends Resource
{
    protected static ?string $model = ShortLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationGroup = 'Nội dung';
    protected static ?string $navigationLabel = 'Liên kết';
    protected static ?string $modelLabel = 'liên kết';
    protected static ?string $pluralModelLabel = 'liên kết';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(null),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(32),
                Forms\Components\Textarea::make('original_url')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('total_clicks')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('valid_views')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_earned')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('slug')->copyable()->searchable(),
            Tables\Columns\TextColumn::make('user.email')->label('Owner')->searchable(),
            Tables\Columns\TextColumn::make('original_url')->limit(40)->tooltip(fn($r)=>$r->original_url),
            Tables\Columns\TextColumn::make('total_clicks')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('valid_views')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('total_earned')->money('VND', divideBy:1)->sortable(),
            Tables\Columns\TextColumn::make('status')->badge()->colors(['success'=>'active','warning'=>'disabled','danger'=>'blocked']),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->options(['active'=>'Active','disabled'=>'Disabled','blocked'=>'Blocked']),
        ])->actions([
            Tables\Actions\Action::make('block')->visible(fn($r)=>$r->status!=='blocked')
                ->color('danger')->action(fn($r)=>$r->update(['status'=>'blocked'])),
            Tables\Actions\Action::make('activate')->visible(fn($r)=>$r->status!=='active')
                ->color('success')->action(fn($r)=>$r->update(['status'=>'active'])),
            Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListShortLinks::route('/'),
            'create' => Pages\CreateShortLink::route('/create'),
            'edit' => Pages\EditShortLink::route('/{record}/edit'),
        ];
    }
}
