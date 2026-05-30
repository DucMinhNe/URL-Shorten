<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup = 'Nội dung';
    protected static ?string $navigationLabel = 'Câu hỏi FAQ';
    protected static ?string $modelLabel = 'FAQ';
    protected static ?string $pluralModelLabel = 'FAQs';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Câu hỏi')->schema([
                Forms\Components\Select::make('category_id')->label('Danh mục')->relationship('category', 'name')->searchable()->preload(),
                Forms\Components\TextInput::make('question')->label('Câu hỏi')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\RichEditor::make('answer')->label('Trả lời')->required()->columnSpanFull()
                    ->disableToolbarButtons(['attachFiles']),
                Forms\Components\TagsInput::make('tags')->label('Thẻ'),
            ]),
            Forms\Components\Section::make('Hiển thị')->schema([
                Forms\Components\TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0),
                Forms\Components\Toggle::make('is_published')->label('Hiển thị')->default(true),
                Forms\Components\Toggle::make('is_featured')->label('Nổi bật'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
            Tables\Columns\TextColumn::make('question')->label('Câu hỏi')->searchable()->limit(60)->wrap(),
            Tables\Columns\TextColumn::make('category.name')->label('Danh mục')->badge()->color('info'),
            Tables\Columns\TextColumn::make('view_count')->label('Lượt xem')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('helpful_count')->label('👍')->sortable(),
            Tables\Columns\TextColumn::make('not_helpful_count')->label('👎')->sortable(),
            Tables\Columns\IconColumn::make('is_published')->label('Hiển thị')->boolean(),
            Tables\Columns\IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
        ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')->label('Danh mục')->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_published')->label('Hiển thị'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Nổi bật'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
