<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?string $navigationLabel = 'Email Templates';
    protected static ?string $modelLabel = 'email template';
    protected static ?string $pluralModelLabel = 'email templates';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Định danh')->schema([
                Forms\Components\TextInput::make('key')->required()->unique(ignoreRecord: true)
                    ->alphaDash()->helperText('ví dụ: welcome, payout_paid, link_disabled'),
                Forms\Components\TextInput::make('name')->required()->columnSpan(2),
                Forms\Components\Select::make('locale')->options(['vi' => 'Tiếng Việt', 'en' => 'English'])->default('vi'),
            ])->columns(4),

            Forms\Components\Section::make('Nội dung')->schema([
                Forms\Components\TextInput::make('subject')->required()->columnSpanFull(),
                Forms\Components\RichEditor::make('body_html')->required()->columnSpanFull()
                    ->disableToolbarButtons(['attachFiles']),
                Forms\Components\Textarea::make('body_text')->rows(4)->columnSpanFull()
                    ->helperText('Bản plain-text cho email client cũ'),
                Forms\Components\TagsInput::make('variables')
                    ->helperText('Variables sử dụng được trong template: {{ user_name }}, {{ amount }}, etc.'),
            ]),

            Forms\Components\Section::make('Gửi')->schema([
                Forms\Components\TextInput::make('from_name'),
                Forms\Components\TextInput::make('from_email')->email(),
                Forms\Components\Toggle::make('is_active')->default(true),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('key')->fontFamily('mono')->badge()->color('info')->searchable(),
            Tables\Columns\TextColumn::make('name')->searchable()->limit(40),
            Tables\Columns\TextColumn::make('subject')->limit(50),
            Tables\Columns\TextColumn::make('locale')->badge(),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
            Tables\Columns\TextColumn::make('sent_count')->label('Đã gửi')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('last_sent_at')->dateTime('d/m H:i')->placeholder('—'),
        ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\SelectFilter::make('locale')->options(['vi' => 'Tiếng Việt', 'en' => 'English']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
