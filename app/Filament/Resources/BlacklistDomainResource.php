<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlacklistDomainResource\Pages;
use App\Filament\Resources\BlacklistDomainResource\RelationManagers;
use App\Models\BlacklistDomain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlacklistDomainResource extends Resource
{
    protected static ?string $model = BlacklistDomain::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('domain')->required()->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('reason')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('domain')->searchable(),
                Tables\Columns\TextColumn::make('reason')->limit(50),
                Tables\Columns\TextColumn::make('creator.email')->label('Created by')->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
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
            'index' => Pages\ListBlacklistDomains::route('/'),
            'create' => Pages\CreateBlacklistDomain::route('/create'),
            'edit' => Pages\EditBlacklistDomain::route('/{record}/edit'),
        ];
    }
}
