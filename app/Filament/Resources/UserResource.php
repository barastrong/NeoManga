<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'User Management';

    protected static ?string $pluralLabel = 'Data Pengguna';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->label('Nama'),
            TextInput::make('email')->email()->required(),
            TextInput::make('password')
                ->password()
                ->label('Password Baru')
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->required(fn (string $context) => $context === 'create')
                ->dehydrated(fn ($state) => filled($state)),
            TextInput::make('role')->default('user')->required(),
            Toggle::make('email_verified')->label('Email Terverifikasi'),
            TextInput::make('otp_code')->label('OTP')->maxLength(6),
            TextInput::make('otp_expires_at')->label('OTP Expiry')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('role')->badge(),
                ToggleColumn::make('email_verified')->label('Verifikasi Email'),
                TextColumn::make('created_at')->dateTime('d M Y H:i'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => UserResource\Pages\ListUsers::route('/'),
            'create' => UserResource\Pages\CreateUser::route('/create'),
            'edit' => UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
