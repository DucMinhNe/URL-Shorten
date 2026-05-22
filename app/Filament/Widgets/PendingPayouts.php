<?php
namespace App\Filament\Widgets;

use App\Models\PayoutRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingPayouts extends BaseWidget
{
    protected static ?string $heading = 'Pending payouts (latest 10)';

    public function table(Table $table): Table
    {
        return $table->query(PayoutRequest::where('status','pending')->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('user.email'),
                Tables\Columns\TextColumn::make('amount')->money('VND', divideBy:1),
                Tables\Columns\TextColumn::make('method')->badge(),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('open')->url(fn($record)=>\App\Filament\Resources\PayoutRequestResource::getUrl('edit',['record'=>$record])),
            ]);
    }
}
