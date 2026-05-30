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
    protected static ?string $navigationGroup = 'Nội dung';
    protected static ?string $navigationLabel = 'Yêu cầu hỗ trợ';
    protected static ?string $modelLabel = 'yêu cầu hỗ trợ';
    protected static ?string $pluralModelLabel = 'yêu cầu hỗ trợ';
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
                Forms\Components\TextInput::make('ticket_code')->label('Mã ticket')->disabled()->dehydrated(false),
                Forms\Components\Select::make('user_id')->label('Người dùng')->relationship('user', 'email')->searchable()->preload(),
                Forms\Components\TextInput::make('guest_email')->label('Email khách')->email(),
                Forms\Components\TextInput::make('subject')->label('Chủ đề')->required()->columnSpanFull(),
                Forms\Components\Select::make('category')->label('Danh mục')->options(\App\Support\Labels::options('ticket_category'))->required(),
                Forms\Components\Select::make('priority')->label('Ưu tiên')->options(\App\Support\Labels::options('priority'))->default('normal')->required(),
            ])->columns(2),

            Forms\Components\Section::make('Trạng thái xử lý')->schema([
                Forms\Components\Select::make('status')->label('Trạng thái')->options(\App\Support\Labels::options('ticket_status'))->required()->default('open'),
                Forms\Components\Select::make('assigned_to')->label('Giao cho admin')
                    ->options(fn () => User::where('is_admin', true)->pluck('name', 'id'))->searchable(),
                Forms\Components\DateTimePicker::make('resolved_at')->label('Giải quyết lúc')->seconds(false),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('ticket_code')->label('Mã ticket')->searchable()->copyable()->fontFamily('mono'),
            Tables\Columns\TextColumn::make('subject')->label('Chủ đề')->searchable()->limit(40),
            Tables\Columns\TextColumn::make('user.email')->label('Email')->searchable()->default(fn ($record) => $record->guest_email ?? '—'),
            Tables\Columns\TextColumn::make('category')->label('Danh mục')->badge()->formatStateUsing(fn ($state) => \App\Support\Labels::get('ticket_category', $state)),
            Tables\Columns\TextColumn::make('priority')->label('Ưu tiên')->badge()->formatStateUsing(fn ($state) => \App\Support\Labels::get('priority', $state))->colors([
                'gray' => 'low', 'info' => 'normal', 'warning' => 'high', 'danger' => 'urgent',
            ]),
            Tables\Columns\TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn ($state) => \App\Support\Labels::get('ticket_status', $state))->color(fn ($state) => match ($state) {
                'open' => 'warning', 'in_progress' => 'info', 'waiting_user' => 'gray',
                'resolved' => 'success', 'closed' => 'gray', default => 'gray',
            }),
            Tables\Columns\TextColumn::make('reply_count')->label('Số phản hồi')->alignCenter(),
            Tables\Columns\TextColumn::make('assignee.name')->label('Giao cho')->default('—'),
            Tables\Columns\TextColumn::make('last_reply_at')->label('Phản hồi cuối')->dateTime('d/m H:i')->sortable(),
            Tables\Columns\TextColumn::make('created_at')->label('Ngày tạo')->dateTime('d/m/Y')->sortable(),
        ])
            ->defaultSort('last_reply_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Trạng thái')->options(\App\Support\Labels::options('ticket_status')),
                Tables\Filters\SelectFilter::make('priority')->label('Ưu tiên')->options(\App\Support\Labels::options('priority')),
                Tables\Filters\SelectFilter::make('category')->label('Danh mục')->options(\App\Support\Labels::options('ticket_category')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resolve')->label('Đánh dấu đã giải quyết')
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
