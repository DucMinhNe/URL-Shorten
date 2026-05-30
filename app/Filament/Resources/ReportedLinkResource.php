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
                Forms\Components\Select::make('short_link_id')->label('Liên kết')->relationship('shortLink', 'slug')->searchable()->required(),
                Forms\Components\Select::make('reason')->label('Lý do')->options(\App\Support\Labels::options('report_reason'))->required(),
                Forms\Components\Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                Forms\Components\TextInput::make('reporter_email')->label('Email người báo cáo')->email(),
                Forms\Components\TextInput::make('reporter_ip')->label('IP người báo cáo'),
            ])->columns(2),

            Forms\Components\Section::make('Xử lý')->schema([
                Forms\Components\Select::make('status')->label('Trạng thái')->options(\App\Support\Labels::options('report_status'))->required()->default('pending'),
                Forms\Components\Select::make('action_taken')->label('Xử lý')->options(\App\Support\Labels::options('report_action')),
                Forms\Components\Textarea::make('admin_note')->label('Ghi chú admin')->rows(2)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
            Tables\Columns\TextColumn::make('shortLink.slug')->label('Liên kết')->fontFamily('mono')->searchable(),
            Tables\Columns\TextColumn::make('reason')->label('Lý do')->badge()
                ->color(fn (string $state) => match (true) {
                    in_array($state, ['malware', 'phishing', 'scam']) => 'danger',
                    in_array($state, ['spam', 'copyright']) => 'warning',
                    default => 'gray',
                })
                ->formatStateUsing(fn ($state) => \App\Support\Labels::get('report_reason', $state)),
            Tables\Columns\TextColumn::make('reporter_email')->label('Email người báo cáo')->default('—')->limit(25),
            Tables\Columns\TextColumn::make('status')->label('Trạng thái')->badge()->colors([
                'warning' => 'pending', 'info' => 'reviewing',
                'danger' => 'confirmed', 'gray' => 'dismissed',
            ])->formatStateUsing(fn ($state) => \App\Support\Labels::get('report_status', $state)),
            Tables\Columns\TextColumn::make('action_taken')->label('Xử lý')->badge()->default('—')->formatStateUsing(fn ($state) => \App\Support\Labels::get('report_action', $state)),
            Tables\Columns\TextColumn::make('created_at')->label('Ngày tạo')->dateTime('d/m/Y H:i')->sortable(),
        ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Trạng thái')->options(\App\Support\Labels::options('report_status')),
                Tables\Filters\SelectFilter::make('reason')->label('Lý do')->options(\App\Support\Labels::options('report_reason')),
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
