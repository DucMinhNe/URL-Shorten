<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqCategoryResource\Pages;
use App\Models\FaqCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FaqCategoryResource extends Resource
{
    protected static ?string $model = FaqCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Nội dung';
    protected static ?string $navigationLabel = 'Chủ đề FAQ';
    protected static ?string $modelLabel = 'chủ đề';
    protected static ?string $pluralModelLabel = 'chủ đề';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->live(onBlur: true)
                ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->alphaDash(),
            Forms\Components\TextInput::make('icon')->placeholder('heroicon-o-banknotes'),
            Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('sort_order')->sortable()->label('#'),
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('slug')->fontFamily('mono'),
            Tables\Columns\TextColumn::make('faqs_count')->counts('faqs')->label('FAQs'),
            Tables\Columns\IconColumn::make('is_published')->boolean(),
        ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqCategories::route('/'),
            'create' => Pages\CreateFaqCategory::route('/create'),
            'edit' => Pages\EditFaqCategory::route('/{record}/edit'),
        ];
    }
}
