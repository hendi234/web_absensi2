<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Employe;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AbsensiHarian;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AbsensiHarianResource\Pages;

class AbsensiHarianResource extends Resource
{
    protected static ?string $model = AbsensiHarian::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    protected static ?string $navigationGroup = 'Manajemen Absensi';

    protected static ?string $label = 'Rekap Absen Harian';

    protected static ?string $pluralLabel = 'Rekap Absen Harian';    

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form 
            ->columns([
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'reviewing' => 'Reviewing',
                        'published' => 'Published',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                Auth::user()->id_roles == 2 ? [ // Jika user biasa, hanya tampilkan data tanpa pencarian
                    TextColumn::make('user.nip')
                        ->label('NIP'),
                    TextColumn::make('user.name')
                        ->label('Nama Karyawan'),
                    TextColumn::make('user.employe.position')
                        ->label('Jabatan'),
                    TextColumn::make('tanggal_absen')
                        ->label('Tanggal')
                        ->getStateUsing(fn ($record) => \Carbon\Carbon::parse($record->created_at)->translatedFormat('d M Y'))
                        ->sortable(),
                    TextColumn::make('absenMasuk.time_attendance')
                        ->label('Waktu Masuk')
                        ->dateTime('H:i:s')
                        ->sortable(),
                    TextColumn::make('absenKeluar.time_attendance')
                        ->label('Waktu Keluar')
                        ->dateTime('H:i:s')
                        ->sortable(),
                    TextColumn::make('work_time')
                        ->label('Durasi Waktu Kerja')
                        ->sortable(),
                    TextColumn::make('lokasi_masuk')
                        ->label('Lokasi Masuk')
                        ->icon('heroicon-o-map-pin')
                        ->getStateUsing(fn($record) => 
                            "<a href='https://www.google.com/maps?q={$record->absenMasuk?->latitude},{$record->absenMasuk?->longitude}' 
                                target='_blank' >Maps</a>")
                        ->html()
                        ->tooltip('Klik untuk melihat lokasi'),
                    TextColumn::make('lokasi_keluar')
                        ->label('Lokasi Keluar')
                        ->icon('heroicon-o-map-pin')
                        ->getStateUsing(fn($record) => 
                            "<a href='https://www.google.com/maps?q={$record->absenKeluar?->latitude},{$record->absenKeluar?->longitude}' 
                            target='_blank' >Maps</a>")
                        ->html()
                        ->tooltip('Klik untuk melihat lokasi'),                
                    ImageColumn::make('absenMasuk.foto')
                        ->label('Foto Absen')
                        ->disk('public'),
                    TextColumn::make('absenMasuk.desc')
                        ->label('Keterangan'), 
                    TextColumn::make('status')
                        ->label('Status')
                        ->formatStateUsing(fn ($state) => $state == '1' ? 'Approve' : 'Pending')
                        ->extraAttributes(fn ($state) => [
                            'style' => $state == '1' 
                                ? 'border: 2px solid #22c55e; background-color: #22c55e; color: white; padding: 4px 8px; border-radius: 4px;' 
                                : 'border: 2px solid #dc2626; background-color: #ff2d2d; color: white; padding: 4px 8px; border-radius: 4px;',
                        ]),
                    TextColumn::make('updatedBy.name')
                        ->label('Di Approve Oleh'),
                ] : [ // Jika admin, tambahkan fitur pencarian
                    TextColumn::make('user.nip')
                        ->label('NIP')
                        ->searchable(),
                    TextColumn::make('user.name')
                        ->label('Nama Karyawan')
                        ->searchable(),
                    TextColumn::make('user.employe.position')
                        ->label('Jabatan')
                        ->searchable(),
                    TextColumn::make('tanggal_absen')
                        ->label('Tanggal')
                        ->getStateUsing(fn ($record) => \Carbon\Carbon::parse($record->created_at)->translatedFormat('d M Y'))
                        ->sortable(),                    
                    TextColumn::make('absenMasuk.time_attendance')
                        ->label('Waktu Masuk')
                        ->dateTime('H:i:s')
                        ->sortable(),
                    TextColumn::make('absenKeluar.time_attendance')
                        ->label('Waktu Keluar')
                        ->dateTime('H:i:s')
                        ->sortable(),
                    TextColumn::make('work_time')
                        ->label('Durasi Waktu Kerja')
                        ->sortable(),
                    TextColumn::make('lokasi_masuk')
                        ->label('Lokasi Masuk')
                        ->icon('heroicon-o-map-pin')
                        ->getStateUsing(fn($record) => 
                            "<a href='https://www.google.com/maps?q={$record->absenMasuk?->latitude},{$record->absenMasuk?->longitude}' 
                                target='_blank' >Maps</a>")
                        ->html()
                        ->tooltip('Klik untuk melihat lokasi'),
                    TextColumn::make('lokasi_keluar')
                        ->label('Lokasi Keluar')
                        ->icon('heroicon-o-map-pin')
                        ->getStateUsing(fn($record) => 
                            "<a href='https://www.google.com/maps?q={$record->absenKeluar?->latitude},{$record->absenKeluar?->longitude}' 
                            target='_blank' >Maps</a>")
                        ->html()
                        ->tooltip('Klik untuk melihat lokasi'),                
                    ImageColumn::make('absenMasuk.foto')
                        ->label('Foto Absen')
                        ->disk('public'),
                    TextColumn::make('absenMasuk.desc')
                        ->label('Keterangan'), 
                    SelectColumn::make('status')
                        ->options([
                            '0' => 'Pending',
                            '1' => 'Approve',
                        ])
                        ->disabled(fn () => auth()->user()->id_roles !== 1),
                    TextColumn::make('updatedBy.name')
                        ->label('Di Approve Oleh')
                        ->searchable(),
                ])
            ->filters(
                [ 
               // Filter berdasarkan rentang tanggal
               Filter::make('tanggal')
               ->form([
                   DatePicker::make('from')->label('Dari'),
                   DatePicker::make('to')->label('Sampai'),
               ])
               ->query(function (Builder $query, array $data) {
                   return $query
                       ->when($data['from'] ?? null, fn ($query) => 
                           $query->whereDate('created_at', '>=', $data['from']))
                       ->when($data['to'] ?? null, fn ($query) => 
                           $query->whereDate('created_at', '<=', $data['to']));
               }),
               ] 
            )
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Data Karyawan')
                    ->icon('heroicon-m-user')
                    ->description('Prevent abuse by limiting the number of requests per period')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Nama Karyawan'),
                        TextEntry::make('nip')
                            ->label('NIP'),
                        TextEntry::make('position')
                            ->label('Jabatan'),
                    ]),
                Section::make('Waktu Kerja')
                    ->icon('heroicon-m-clock')
                    ->aside()
                    ->description('Prevent abuse by limiting the number of requests per period')
                    ->schema([
                        TextEntry::make('tanggal_absen')
                            ->label('Tanggal')
                            ->getStateUsing(fn ($record) => \Carbon\Carbon::parse($record->created_at)->locale('id')->translatedFormat('d M Y')),
                        TextEntry::make('absenMasuk.time_attendance')
                            ->label('Waktu Masuk')
                            ->dateTime('H:i:s'),
                        TextEntry::make('absenKeluar.time_attendance')
                            ->label('Waktu Keluar')
                            ->dateTime('H:i:s'),
                        TextEntry::make('work_time')
                            ->label('Durasi Waktu Kerja'),
                        TextEntry::make('absenMasuk.desc')
                            ->label('Keterangan'),
                    ]),
                Section::make('Lokasi Kerja')
                    ->icon('heroicon-m-map-pin')
                    ->aside()
                    ->description('Prevent abuse by limiting the number of requests per period')
                    ->schema([
                        TextEntry::make('lokasi_masuk')
                            ->label('Lokasi Masuk')
                            ->icon('heroicon-o-map-pin')
                            ->getStateUsing(fn($record) => 
                                "<a href='https://www.google.com/maps?q={$record->absenMasuk?->latitude},{$record->absenMasuk?->longitude}' 
                                    target='_blank' >Maps</a>")
                            ->html()
                            ->tooltip('Klik untuk melihat lokasi'),
                        TextEntry::make('lokasi_keluar')
                            ->label('Lokasi Keluar')
                            ->icon('heroicon-o-map-pin')
                            ->getStateUsing(fn($record) => 
                                "<a href='https://www.google.com/maps?q={$record->absenKeluar?->latitude},{$record->absenKeluar?->longitude}' 
                                target='_blank' >Maps</a>")
                            ->html()
                            ->tooltip('Klik untuk melihat lokasi'),                
                    ]),
                Section::make('Foto Absen')
                    ->icon('heroicon-m-photo')
                    ->description('Prevent abuse by limiting the number of requests per period')
                    ->schema([
                        ImageEntry::make('absenMasuk.foto')
                            ->label('Foto Absen Masuk')
                            ->disk('public'),
                        ImageEntry::make('absenKeluar.foto')
                            ->label('Foto Absen Keluar')
                            ->disk('public'),
                    ])->columns(2),
                Fieldset::make('Status Absensi')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->formatStateUsing(fn ($state) => $state ? 'Approve' : 'Pending'),
                        TextEntry::make('updatedBy.name')
                            ->label('Di Approve Oleh'),
                    ])->columns(1)
            ])
            ->columns(1)
            ->inlineLabel();
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
            'index' => Pages\ListAbsensiHarians::route('/'),
        ];
    }

    // Method untuk Validasi data yang di tampilkan
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class])
            ->when(Auth::check() && Auth::user()->id_roles == 2, function ($query) {
                $query->where(function ($q) {
                $q->whereIn('id_attendance_in', function ($subQuery) {
                    $subQuery->select('id')
                        ->from('attendance_in')
                        ->where('user_id', Auth::id());
                })->orWhereIn('id_attendance_out', function ($subQuery) {
                    $subQuery->select('id')
                        ->from('attendance_out')
                        ->where('user_id', Auth::id());
                });
            });
        });
    }
}
