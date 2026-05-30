<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Người dùng';
    protected static ?string $navigationLabel = 'Tài khoản';
    protected static ?string $modelLabel = 'người dùng';
    protected static ?string $pluralModelLabel = 'người dùng';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return number_format(static::getModel()::count());
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Tên')->required(),
            Forms\Components\TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('balance')->label('Số dư')->numeric()->default(0)->suffix('VND'),
            Forms\Components\TextInput::make('total_earned')->label('Doanh thu')->numeric()->disabled(),
            Forms\Components\Select::make('status')->label('Trạng thái')->options(\App\Support\Labels::options('user_status'))->required(),
            Forms\Components\Toggle::make('is_admin')->label('Admin'),
            Forms\Components\Select::make('payout_method')->label('Phương thức rút')->options(\App\Support\Labels::options('method'))->nullable(),
            Forms\Components\TextInput::make('payout_account')->label('Tài khoản rút')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Tên')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('balance')->label('Số dư')->money('VND', divideBy: 1)->sortable(),
                Tables\Columns\TextColumn::make('total_earned')->label('Doanh thu')->money('VND', divideBy: 1)->sortable(),
                Tables\Columns\IconColumn::make('is_admin')->label('Admin')->boolean(),
                Tables\Columns\TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn ($state) => \App\Support\Labels::get('user_status', $state))->colors(['success'=>'active','danger'=>'banned']),
                Tables\Columns\TextColumn::make('created_at')->label('Ngày tạo')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Trạng thái')->options(\App\Support\Labels::options('user_status')),
                Tables\Filters\TernaryFilter::make('is_admin')->label('Admin'),
            ])
            ->headerActions([
                \App\Filament\Support\ExportsCsv::action('nguoi-dung', [
                    'ID' => 'id',
                    'Tên' => 'name',
                    'Email' => 'email',
                    'Số dư' => 'balance',
                    'Tổng kiếm được' => 'total_earned',
                    'Trạng thái' => 'status',
                    'Admin' => 'is_admin',
                    'Phương thức rút' => 'payout_method',
                    'Tài khoản nhận' => 'payout_account',
                    'Tạo lúc' => 'created_at',
                ], fn () => \App\Models\User::latest()->get()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('ban')->label('Cấm')->icon('heroicon-o-lock-closed')
                    ->visible(fn($record)=>$record->status==='active')->requiresConfirmation()
                    ->action(fn($record)=>$record->update(['status'=>'banned']))->color('danger'),
                Tables\Actions\Action::make('unban')->label('Bỏ cấm')->icon('heroicon-o-lock-open')
                    ->visible(fn($record)=>$record->status==='banned')
                    ->action(fn($record)=>$record->update(['status'=>'active']))->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulkBan')->label('Cấm các tài khoản đã chọn')
                        ->icon('heroicon-o-lock-closed')->color('danger')->requiresConfirmation()
                        ->action(fn (\Illuminate\Support\Collection $records) => $records->each->update(['status' => 'banned']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('bulkUnban')->label('Bỏ cấm các tài khoản đã chọn')
                        ->icon('heroicon-o-lock-open')->color('success')->requiresConfirmation()
                        ->action(fn (\Illuminate\Support\Collection $records) => $records->each->update(['status' => 'active']))
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
