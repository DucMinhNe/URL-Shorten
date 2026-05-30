<?php
namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopEarnersTable extends BaseWidget
{
    protected static ?string $heading = '🏆 Top 10 người dùng kiếm nhiều nhất';
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->withCount('shortLinks')
                    ->orderByDesc('total_earned')
                    ->limit(10)
            )
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('rank')
                    ->label('#')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->color('gray')
                    ->searchable(),
                Tables\Columns\TextColumn::make('short_links_count')
                    ->label('Số link')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Số dư')
                    ->formatStateUsing(fn ($state) => number_format((int) $state) . ' đ')
                    ->color('warning')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('total_earned')
                    ->label('Tổng kiếm được')
                    ->formatStateUsing(fn ($state) => number_format((int) $state) . ' đ')
                    ->weight('bold')
                    ->color('success')
                    ->alignEnd(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Xem')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => \App\Filament\Resources\UserResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
