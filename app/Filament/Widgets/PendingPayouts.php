<?php
namespace App\Filament\Widgets;

use App\Models\PayoutRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingPayouts extends BaseWidget
{
    protected static ?string $heading = 'Yêu cầu rút tiền đang chờ (mới nhất 10)';
    protected static ?int $sort = 7;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table->query(PayoutRequest::where('status','pending')->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('user.email')->label('Email'),
                Tables\Columns\TextColumn::make('amount')->label('Số tiền')->money('VND', divideBy:1),
                Tables\Columns\TextColumn::make('method')->label('Phương thức')->badge()->formatStateUsing(fn ($state) => \App\Support\Labels::get('method', $state)),
                Tables\Columns\TextColumn::make('created_at')->label('Thời gian')->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('open')->label('Mở')->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn($record)=>\App\Filament\Resources\PayoutRequestResource::getUrl('edit',['record'=>$record])),
            ]);
    }
}
