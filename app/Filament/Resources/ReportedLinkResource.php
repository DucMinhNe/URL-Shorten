<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportedLinkResource\Pages;
use App\Models\ReportedLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportedLinkResource extends Resource
{
    protected static ?string $model = ReportedLink::class;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Liên kết & Click';
    protected static ?string $navigationLabel = 'Báo cáo link';
    protected static ?string $modelLabel = 'báo cáo';
    protected static ?string $pluralModelLabel = 'báo cáo';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) ReportedLink::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Báo cáo')->schema([
                Forms\Components\Select::make('short_link_id')->relationship('shortLink', 'slug')->searchable()->required(),
                Forms\Components\Select::make('reason')->options([
                    'spam' => 'Spam', 'malware' => 'Malware', 'phishing' => 'Phishing',
                    'inappropriate' => 'Nội dung không phù hợp', 'copyright' => 'Vi phạm bản quyền',
                    'scam' => 'Lừa đảo', 'other' => 'Khác',
                ])->required(),
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                Forms\Components\TextInput::make('reporter_email')->email(),
                Forms\Components\TextInput::make('reporter_ip'),
            ])->columns(2),

            Forms\Components\Section::make('Xử lý')->schema([
                Forms\Components\Select::make('status')->options([
                    'pending' => 'Chờ xử lý', 'reviewing' => 'Đang xem xét',
                    'confirmed' => 'Xác nhận vi phạm', 'dismissed' => 'Bỏ qua',
                ])->required()->default('pending'),
                Forms\Components\Select::make('action_taken')->options([
                    'none' => 'Chưa có hành động',
                    'warned' => 'Cảnh cáo user',
                    'disabled_link' => 'Vô hiệu hoá link',
                    'blacklisted_domain' => 'Chặn domain',
                    'banned_user' => 'Ban user',
                ]),
                Forms\Components\Textarea::make('admin_note')->rows(2)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('shortLink.slug')->label('Slug')->fontFamily('mono')->searchable(),
            Tables\Columns\TextColumn::make('reason')->badge()
                ->color(fn (string $state) => match (true) {
                    in_array($state, ['malware', 'phishing', 'scam']) => 'danger',
                    in_array($state, ['spam', 'copyright']) => 'warning',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('reporter_email')->default('—')->limit(25),
            Tables\Columns\TextColumn::make('status')->badge()->colors([
                'warning' => 'pending', 'info' => 'reviewing',
                'danger' => 'confirmed', 'gray' => 'dismissed',
            ]),
            Tables\Columns\TextColumn::make('action_taken')->badge()->default('—'),
            Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y H:i')->sortable(),
        ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Chờ xử lý', 'reviewing' => 'Xem xét',
                    'confirmed' => 'Vi phạm', 'dismissed' => 'Bỏ qua',
                ]),
                Tables\Filters\SelectFilter::make('reason')->options([
                    'spam' => 'Spam', 'malware' => 'Malware', 'phishing' => 'Phishing',
                    'inappropriate' => 'Không phù hợp', 'copyright' => 'Bản quyền',
                    'scam' => 'Lừa đảo', 'other' => 'Khác',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirm')->label('Xác nhận VP')->color('danger')
                    ->icon('heroicon-o-no-symbol')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update([
                        'status' => 'confirmed', 'reviewed_at' => now(),
                        'action_taken' => 'disabled_link',
                    ])),
                Tables\Actions\Action::make('dismiss')->label('Bỏ qua')->color('gray')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'dismissed', 'reviewed_at' => now()])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportedLinks::route('/'),
            'create' => Pages\CreateReportedLink::route('/create'),
            'edit' => Pages\EditReportedLink::route('/{record}/edit'),
        ];
    }
}
