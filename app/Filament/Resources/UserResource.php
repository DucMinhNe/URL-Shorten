<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('balance')->numeric()->default(0)->suffix('VND'),
            Forms\Components\TextInput::make('total_earned')->numeric()->disabled(),
            Forms\Components\Select::make('status')->options(['active'=>'Active','banned'=>'Banned'])->required(),
            Forms\Components\Toggle::make('is_admin'),
            Forms\Components\Select::make('payout_method')->options(['momo'=>'Momo','zalo'=>'ZaloPay','paypal'=>'PayPal'])->nullable(),
            Forms\Components\TextInput::make('payout_account')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('balance')->money('VND', divideBy: 1)->sortable(),
                Tables\Columns\TextColumn::make('total_earned')->money('VND', divideBy: 1)->sortable(),
                Tables\Columns\IconColumn::make('is_admin')->boolean(),
                Tables\Columns\TextColumn::make('status')->badge()->colors(['success'=>'active','danger'=>'banned']),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['active'=>'Active','banned'=>'Banned']),
                Tables\Filters\TernaryFilter::make('is_admin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('ban')->visible(fn($r)=>$r->status==='active')
                    ->action(fn($r)=>$r->update(['status'=>'banned']))->color('danger'),
                Tables\Actions\Action::make('unban')->visible(fn($r)=>$r->status==='banned')
                    ->action(fn($r)=>$r->update(['status'=>'active']))->color('success'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
