<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Hệ thống';
    protected static ?string $navigationLabel = 'Nhật ký hệ thống';
    protected static ?string $modelLabel = 'audit log';
    protected static ?string $pluralModelLabel = 'audit logs';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function canCreate(): bool { return false; }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Thông tin chung')->schema([
                Infolists\Components\TextEntry::make('id'),
                Infolists\Components\TextEntry::make('created_at')->dateTime('d/m/Y H:i:s'),
                Infolists\Components\TextEntry::make('action')->badge(),
                Infolists\Components\TextEntry::make('severity')->badge(),
                Infolists\Components\TextEntry::make('user.name')->label('Người thực hiện'),
                Infolists\Components\TextEntry::make('user_email'),
                Infolists\Components\TextEntry::make('ip_address')->label('IP'),
                Infolists\Components\TextEntry::make('user_agent')->columnSpanFull(),
            ])->columns(2),
            Infolists\Components\Section::make('Đối tượng')->schema([
                Infolists\Components\TextEntry::make('target_type'),
                Infolists\Components\TextEntry::make('target_id'),
                Infolists\Components\TextEntry::make('target_label')->columnSpanFull(),
            ])->columns(2),
            Infolists\Components\Section::make('Dữ liệu thay đổi')->schema([
                Infolists\Components\KeyValueEntry::make('old_values')->label('Giá trị cũ'),
                Infolists\Components\KeyValueEntry::make('new_values')->label('Giá trị mới'),
            ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
            Tables\Columns\TextColumn::make('created_at')->label('Ngày tạo')->dateTime('d/m/Y H:i:s')->sortable(),
            Tables\Columns\TextColumn::make('user.name')->label('Người thực hiện')->searchable()->default('—'),
            Tables\Columns\TextColumn::make('action')->label('Hành động')->badge()->searchable(),
            Tables\Columns\TextColumn::make('target_type')
                ->formatStateUsing(fn ($state) => $state ? class_basename($state) : '—')
                ->label('Đối tượng'),
            Tables\Columns\TextColumn::make('target_label')->label('Chi tiết')->limit(40),
            Tables\Columns\TextColumn::make('severity')->label('Mức độ')->badge()
                ->formatStateUsing(fn ($state) => \App\Support\Labels::get('severity', $state))
                ->colors([
                    'gray' => 'low', 'info' => 'medium', 'warning' => 'high', 'danger' => 'critical',
                ]),
            Tables\Columns\TextColumn::make('ip_address')->label('IP')->toggleable(isToggledHiddenByDefault: true),
        ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('severity')->label('Mức độ')
                    ->options(\App\Support\Labels::options('severity')),
                Tables\Filters\SelectFilter::make('action')->label('Hành động')->options(fn () => AuditLog::query()
                    ->select('action')->distinct()->pluck('action', 'action')->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
