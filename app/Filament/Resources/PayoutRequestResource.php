<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayoutRequestResource\Pages;
use App\Filament\Resources\PayoutRequestResource\RelationManagers;
use App\Models\PayoutRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayoutRequestResource extends Resource
{
    protected static ?string $model = PayoutRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Tiền & Thanh toán';
    protected static ?string $navigationLabel = 'Yêu cầu rút tiền';
    protected static ?string $modelLabel = 'yêu cầu rút tiền';
    protected static ?string $pluralModelLabel = 'yêu cầu rút tiền';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('method')
                    ->required(),
                Forms\Components\TextInput::make('account_info')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Textarea::make('admin_note')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('processed_by')
                    ->numeric()
                    ->default(null),
                Forms\Components\DateTimePicker::make('processed_at'),
                Forms\Components\TextInput::make('transaction_ref')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('user.email')->searchable(),
            Tables\Columns\TextColumn::make('amount')->money('VND', divideBy:1),
            Tables\Columns\TextColumn::make('method')->badge(),
            Tables\Columns\TextColumn::make('account_info')->copyable(),
            Tables\Columns\TextColumn::make('status')->badge()->colors([
                'warning'=>'pending','primary'=>'approved','success'=>'paid','danger'=>'rejected'
            ]),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('processed_at')->dateTime()->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->options([
                'pending'=>'Pending','approved'=>'Approved','paid'=>'Paid','rejected'=>'Rejected',
            ])->default('pending'),
        ])->headerActions([
            \App\Filament\Support\ExportsCsv::action('rut-tien', [
                'ID' => 'id',
                'Email' => 'user.email',
                'Số tiền' => 'amount',
                'Phương thức' => 'method',
                'Tài khoản nhận' => 'account_info',
                'Trạng thái' => 'status',
                'Mã giao dịch' => 'transaction_ref',
                'Tạo lúc' => 'created_at',
                'Xử lý lúc' => 'processed_at',
            ], fn () => \App\Models\PayoutRequest::with('user')->latest()->get()),
        ])->actions([
            Tables\Actions\Action::make('markPaid')->visible(fn($record)=>in_array($record->status,['pending','approved']))
                ->form([Forms\Components\TextInput::make('transaction_ref')->required()->label('Transaction ref')])
                ->action(fn($record,$data)=>app(\App\Services\PayoutService::class)->markPaid($record, auth()->user(), $data['transaction_ref']))
                ->color('success')->icon('heroicon-o-check'),
            Tables\Actions\Action::make('reject')->visible(fn($record)=>$record->status==='pending')
                ->form([Forms\Components\Textarea::make('reason')->required()])
                ->action(fn($record,$data)=>app(\App\Services\PayoutService::class)->reject($record, auth()->user(), $data['reason']))
                ->color('danger')->icon('heroicon-o-x-mark')
                ->requiresConfirmation(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\BulkAction::make('bulkMarkPaid')->label('Duyệt & đánh dấu đã trả')
                    ->icon('heroicon-o-check-badge')->color('success')->requiresConfirmation()
                    ->modalDescription('Đánh dấu các yêu cầu đang chờ/đã duyệt là ĐÃ TRẢ. Mã giao dịch tự sinh.')
                    ->action(function (\Illuminate\Support\Collection $records) {
                        $service = app(\App\Services\PayoutService::class);
                        $admin = auth()->user();
                        $records
                            ->filter(fn ($r) => in_array($r->status, ['pending', 'approved']))
                            ->each(function ($r) use ($service, $admin) {
                                $service->markPaid($r, $admin, 'BULK-' . now()->format('YmdHis') . '-' . $r->id);
                            });
                    })
                    ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListPayoutRequests::route('/'),
            'create' => Pages\CreatePayoutRequest::route('/create'),
            'edit' => Pages\EditPayoutRequest::route('/{record}/edit'),
        ];
    }
}
