<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Employe;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EmployeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmployeResource\RelationManagers;

class EmployeResource extends Resource
{
    protected static ?string $model = Employe::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?string $pluralLabel = 'Karyawan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('avatar')
                    ->required()
                    ->disk('karyawan')
                    ->image()
                    ->label('Foto')
                    ->validationMessages([
                        'required' => 'Foto wajib diisi.',
                    ])
                    ->columnSpanFull(),
                    TextInput::make('nip')
                    ->required()
                    ->maxLength(16)
                    ->columnSpanFull(),                
                TextInput::make('name')
                    ->required()
                    ->label('Nama')
                    ->maxLength(64),
                TextInput::make('position')
                    ->required()
                    ->label('Jabatan')
                    ->maxLength(64),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(64),
                DatePicker::make('join_date')
                    ->required()
                    ->label('Tanggal Bergabung')
                    ->native(false),
                TextInput::make('education')
                    ->label('Pendidikan')
                    ->required()
                    ->maxLength(64)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->disk('karyawan')
                    ->circular()
                    ->size(80)
                    ->label('Foto'),
                TextColumn::make('nip')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nama'),
                TextColumn::make('email')
                    ->searchable(),
                // TextColumn::make('position')
                //     ->searchable()
                //     ->label('Jabatan'),
                // TextColumn::make('education')
                //     ->label('Pendidikan')
                //     ->sortable(),
                // TextColumn::make('join_date')
                //     ->date()
                //     ->label('Tanggal Bergabung')
                //     ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label('Created By'),
                TextColumn::make('updatedBy.name')
                    ->label("Updated by"),
                TextColumn::make('deletedBy.name')
                    ->label("Deleted by"),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListEmployes::route('/'),
            'create' => Pages\CreateEmploye::route('/create'),
            'edit' => Pages\EditEmploye::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
