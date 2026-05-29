<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Người dùng';
    protected static ?string $navigationLabel = 'Vai trò';
    protected static ?string $modelLabel = 'vai trò';
    protected static ?string $pluralModelLabel = 'vai trò';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(64),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->alphaDash(),
            Forms\Components\Textarea::make('description')->rows(2),
            Forms\Components\TextInput::make('level')->numeric()->default(0)->helperText('Cao hơn = quyền lớn hơn'),
            Forms\Components\Select::make('color')->options([
                'slate' => 'Slate', 'gray' => 'Gray', 'red' => 'Red', 'orange' => 'Orange',
                'amber' => 'Amber', 'yellow' => 'Yellow', 'lime' => 'Lime', 'green' => 'Green',
                'emerald' => 'Emerald', 'cyan' => 'Cyan', 'blue' => 'Blue', 'indigo' => 'Indigo',
                'violet' => 'Violet', 'purple' => 'Purple', 'pink' => 'Pink', 'rose' => 'Rose',
            ])->default('slate'),
            Forms\Components\TagsInput::make('permissions')
                ->helperText('Ví dụ: users.manage, payouts.approve, settings.edit, * (tất cả)'),
            Forms\Components\Toggle::make('is_system')->label('Vai trò hệ thống (không thể xoá)'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('slug')->badge()->color(fn ($record) => $record->color),
            Tables\Columns\TextColumn::make('level')->sortable()->alignCenter(),
            Tables\Columns\TextColumn::make('users_count')->counts('users')->label('Users'),
            Tables\Columns\IconColumn::make('is_system')->boolean()->label('Hệ thống'),
            Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y')->sortable(),
        ])
            ->defaultSort('level', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_system'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->visible(fn ($record) => ! $record->is_system),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
