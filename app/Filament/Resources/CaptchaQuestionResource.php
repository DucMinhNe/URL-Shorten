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
                    ->label('Câu lệnh (hiện trên lưới)')
                    ->required()->maxLength(255)
                    ->placeholder('VD: Chọn tất cả ô có con chó 🐶 · Chọn các số 2, 3, 4, 5')
                    ->columnSpanFull(),

                Forms\Components\Repeater::make('options')
                    ->label('Các ô của lưới (đánh dấu ô ĐÚNG)')
                    ->schema([
                        Forms\Components\TextInput::make('text')
                            ->label('Nội dung ô (chữ / số / emoji 🐶)')
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('image')
                            ->label('hoặc URL ảnh')
                            ->placeholder('https://… (để trống nếu dùng emoji/chữ)')
                            ->columnSpan(2),
                        Forms\Components\Toggle::make('correct')
                            ->label('Đúng')->inline(false)->columnSpan(1),
                    ])
                    ->columns(6)
                    ->minItems(4)->maxItems(9)
                    ->default([
                        ['text' => '🐶', 'correct' => true], ['text' => '🐱', 'correct' => false], ['text' => '🚗', 'correct' => false],
                        ['text' => '🐶', 'correct' => true], ['text' => '🍎', 'correct' => false], ['text' => '🐶', 'correct' => true],
                        ['text' => '🐰', 'correct' => false], ['text' => '🐶', 'correct' => true], ['text' => '🚲', 'correct' => false],
                    ])
                    ->reorderable()
                    ->grid(3)
                    ->helperText('Nên đủ 9 ô (lưới 3×3). Bật "Đúng" cho các ô khớp câu lệnh. Người dùng phải chọn ĐÚNG hết và không thừa.')
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
