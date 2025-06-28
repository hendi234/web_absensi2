<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Role;
use App\Models\User;
use Filament\Tables;
use App\Models\Employe;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_employes')
                    ->required()
                    ->label('Karyawan')
                    ->options(Employe::all()->pluck('name', 'id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function($state, Set $set, Get $get) {
                        $employe = Employe::find($state);
                        $set('name', $employe->name ?? 0);
                        $set('nip', $employe->nip ?? 0);
                        $set('email', $employe->email ?? 0);
                        $set('avatar_url', $employe->avatar ?? 0);
                    }),
                Select::make('id_roles')
                    ->required()
                    ->label('Role')
                    ->options(Role::all()->pluck('name', 'id'))
                    ->default('karyawan'),
                TextInput::make('avatar_url')
                    ->required()
                    ->label('Foto')
                    ->columnSpanFull()
                    ->disabled()  // ini bikin field read-only (disabled)
                    ->dehydrated(true),
                TextInput::make('name')
                    ->required()
                    ->label('Nama')
                    ->maxLength(64)
                    ->columnSpanFull()
                    ->disabled()  // ini bikin field read-only (disabled)
                    ->dehydrated(true),
                TextInput::make('nip')
                    ->required()
                    ->maxLength(16)
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'NIP sudah digunakan oleh akun lain.',
                    ])
                    ->disabled()  // ini bikin field read-only (disabled)
                    ->dehydrated(true),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(64)
                    ->disabled()  // ini bikin field read-only (disabled)
                    ->dehydrated(true),
                DateTimePicker::make('email_verified_at')
                    ->hidden(),
                TextInput::make('password')
                    ->password()
                    ->minLength(8) // ⬅️ minimal 8 karakter
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->revealable()
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->disk('karyawan')
                    ->circular()
                    ->size(80)
                    ->label('Foto'),
                TextColumn::make('nip')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('role.name')
                    ->label('Role')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
