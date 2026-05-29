<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Nội dung';
    protected static ?string $navigationLabel = 'Tags';
    protected static ?string $modelLabel = 'tag';
    protected static ?string $pluralModelLabel = 'tags';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(48)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->alphaDash(),
            Forms\Components\Select::make('color')->options([
                'slate' => 'Slate', 'red' => 'Red', 'orange' => 'Orange', 'amber' => 'Amber',
                'green' => 'Green', 'cyan' => 'Cyan', 'blue' => 'Blue', 'indigo' => 'Indigo',
                'violet' => 'Violet', 'pink' => 'Pink', 'rose' => 'Rose',
            ])->default('slate'),
            Forms\Components\TextInput::make('icon')->helperText('heroicon-o-* (ví dụ: heroicon-o-fire)'),
            Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
            Forms\Components\Toggle::make('is_featured')->label('Featured'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->badge()->color(fn ($record) => $record->color),
            Tables\Columns\TextColumn::make('slug')->fontFamily('mono')->copyable(),
            Tables\Columns\TextColumn::make('short_links_count')->counts('shortLinks')->label('Links'),
            Tables\Columns\TextColumn::make('usage_count')->label('Lượt dùng')->sortable(),
            Tables\Columns\IconColumn::make('is_featured')->boolean(),
            Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y')->sortable(),
        ])
            ->defaultSort('usage_count', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
