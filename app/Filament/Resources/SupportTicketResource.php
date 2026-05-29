<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportTicketResource\Pages;
use App\Models\SupportTicket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;
    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';
    protected static ?string $navigationGroup = 'Hỗ trợ';
    protected static ?string $navigationLabel = 'Yêu cầu hỗ trợ';
    protected static ?string $modelLabel = 'ticket';
    protected static ?string $pluralModelLabel = 'tickets';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) SupportTicket::whereIn('status', ['open', 'in_progress'])->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin yêu cầu')->schema([
                Forms\Components\TextInput::make('ticket_code')->disabled()->dehydrated(false),
                Forms\Components\Select::make('user_id')->relationship('user', 'email')->searchable()->preload(),
                Forms\Components\TextInput::make('guest_email')->email(),
                Forms\Components\TextInput::make('subject')->required()->columnSpanFull(),
                Forms\Components\Select::make('category')->options([
                    'payout' => 'Rút tiền',
                    'account' => 'Tài khoản',
                    'link_issue' => 'Vấn đề link',
                    'fraud_report' => 'Báo cáo gian lận',
                    'feature_request' => 'Đề xuất tính năng',
                    'bug' => 'Lỗi',
                    'other' => 'Khác',
                ])->required(),
                Forms\Components\Select::make('priority')->options([
                    'low' => 'Thấp', 'normal' => 'Bình thường', 'high' => 'Cao', 'urgent' => 'Khẩn cấp',
                ])->default('normal')->required(),
            ])->columns(2),

            Forms\Components\Section::make('Trạng thái xử lý')->schema([
                Forms\Components\Select::make('status')->options([
                    'open' => 'Mở',
                    'in_progress' => 'Đang xử lý',
                    'waiting_user' => 'Chờ user',
                    'resolved' => 'Đã giải quyết',
                    'closed' => 'Đóng',
                ])->required()->default('open'),
                Forms\Components\Select::make('assigned_to')->label('Giao cho admin')
                    ->options(fn () => User::where('is_admin', true)->pluck('name', 'id'))->searchable(),
                Forms\Components\DateTimePicker::make('resolved_at')->seconds(false),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('ticket_code')->searchable()->copyable()->fontFamily('mono'),
            Tables\Columns\TextColumn::make('subject')->searchable()->limit(40),
            Tables\Columns\TextColumn::make('user.email')->searchable()->default(fn ($record) => $record->guest_email ?? '—'),
            Tables\Columns\TextColumn::make('category')->badge(),
            Tables\Columns\TextColumn::make('priority')->badge()->colors([
                'gray' => 'low', 'info' => 'normal', 'warning' => 'high', 'danger' => 'urgent',
            ]),
            Tables\Columns\TextColumn::make('status')->badge()->colors([
                'warning' => 'open', 'info' => 'in_progress', 'gray' => 'waiting_user',
                'success' => 'resolved', 'gray' => 'closed',
            ]),
            Tables\Columns\TextColumn::make('reply_count')->label('Replies')->alignCenter(),
            Tables\Columns\TextColumn::make('assignee.name')->label('Giao cho')->default('—'),
            Tables\Columns\TextColumn::make('last_reply_at')->dateTime('d/m H:i')->sortable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y')->sortable(),
        ])
            ->defaultSort('last_reply_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'open' => 'Mở', 'in_progress' => 'Đang xử lý', 'waiting_user' => 'Chờ user',
                    'resolved' => 'Đã giải quyết', 'closed' => 'Đóng',
                ]),
                Tables\Filters\SelectFilter::make('priority')->options([
                    'low' => 'Thấp', 'normal' => 'Bình thường', 'high' => 'Cao', 'urgent' => 'Khẩn cấp',
                ]),
                Tables\Filters\SelectFilter::make('category')->options([
                    'payout' => 'Rút tiền', 'account' => 'Tài khoản', 'link_issue' => 'Link',
                    'fraud_report' => 'Gian lận', 'feature_request' => 'Đề xuất',
                    'bug' => 'Bug', 'other' => 'Khác',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resolve')->label('Resolved')
                    ->color('success')->icon('heroicon-o-check')
                    ->visible(fn ($record) => $record->status !== 'resolved')
                    ->action(fn ($record) => $record->update(['status' => 'resolved', 'resolved_at' => now()])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'edit' => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
