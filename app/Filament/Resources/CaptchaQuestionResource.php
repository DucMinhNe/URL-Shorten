<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CaptchaQuestionResource\Pages;
use App\Filament\Resources\CaptchaQuestionResource\RelationManagers;
use App\Models\CaptchaQuestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CaptchaQuestionResource extends Resource
{
    protected static ?string $model = CaptchaQuestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Hệ thống';

    protected static ?string $navigationLabel = 'Câu hỏi xác minh';

    protected static ?string $modelLabel = 'câu hỏi xác minh';

    protected static ?string $pluralModelLabel = 'Câu hỏi xác minh';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question')
                    ->label('Câu hỏi')
                    ->required()->maxLength(255)
                    ->placeholder('VD: 3 + 4 bằng mấy?')
                    ->columnSpanFull(),

                Forms\Components\Repeater::make('options')
                    ->label('Các đáp án (đánh dấu đáp án đúng)')
                    ->schema([
                        Forms\Components\TextInput::make('text')
                            ->label('Đáp án')->required()->columnSpan(3),
                        Forms\Components\Toggle::make('correct')
                            ->label('Đúng')->inline(false)->columnSpan(1),
                    ])
                    ->columns(4)
                    ->minItems(2)->maxItems(6)
                    ->default([['text' => '', 'correct' => true], ['text' => '', 'correct' => false]])
                    ->reorderable()
                    ->helperText('Tối thiểu 2 đáp án. Bật "Đúng" ở ít nhất một đáp án.')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('image')
                    ->label('Ảnh minh hoạ (tuỳ chọn)')
                    ->placeholder('URL ảnh hoặc đường dẫn trong /public')
                    ->maxLength(500)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Đang bật')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->label('Câu hỏi')->searchable()->wrap()->weight('bold'),
                Tables\Columns\TextColumn::make('options')
                    ->label('Đáp án đúng')
                    ->formatStateUsing(fn ($state) => collect($state)->filter(fn ($o) => ! empty($o['correct']))->pluck('text')->implode(', '))
                    ->badge()->color('success'),
                Tables\Columns\TextColumn::make('options_count')
                    ->label('Số đáp án')
                    ->state(fn ($record) => count($record->options ?? []))
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Bật')->boolean(),
                Tables\Columns\TextColumn::make('shown_count')
                    ->label('Lượt hiện')->numeric()->sortable()->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Trạng thái'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa'),
                Tables\Actions\DeleteAction::make()->label('Xoá'),
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
            'index' => Pages\ListCaptchaQuestions::route('/'),
            'create' => Pages\CreateCaptchaQuestion::route('/create'),
            'edit' => Pages\EditCaptchaQuestion::route('/{record}/edit'),
        ];
    }
}
