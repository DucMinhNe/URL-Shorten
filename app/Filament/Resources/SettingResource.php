<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Hệ thống';
    protected static ?string $navigationLabel = 'Cài đặt';
    protected static ?string $modelLabel = 'cài đặt';
    protected static ?string $pluralModelLabel = 'cài đặt';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')->label('Khoá')->required()->disabled(fn($record)=>$record !== null),
            Forms\Components\Textarea::make('value')->label('Giá trị')->required()->rows(2),
            Forms\Components\Select::make('type')->label('Loại')->options(\App\Support\Labels::options('setting_type'))->required(),
            Forms\Components\TextInput::make('description')->label('Mô tả')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('key')->label('Khoá')->searchable(),
            Tables\Columns\TextColumn::make('value')->label('Giá trị')->limit(50),
            Tables\Columns\TextColumn::make('type')->label('Loại')->badge()->formatStateUsing(fn ($state) => \App\Support\Labels::get('setting_type', $state)),
            Tables\Columns\TextColumn::make('description')->label('Mô tả')->limit(40),
        ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
