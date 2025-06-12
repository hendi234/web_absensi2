<?php

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use App\Models\AbsensiHarian;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class AbsensiTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';// Menentukan lebar tabel agar penuh
    protected static ?int $sort = 3;  // Menentukan urutan widget dalam tampilan

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AbsensiHarian::query()
                  // Jika user memiliki role ID 2, hanya tampilkan data absen miliknya sendiri
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
                    })
            )
            ->filters([ 
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
            ])            
            ->defaultPaginationPageOption(10)// Menentukan jumlah data yang ditampilkan per halaman
            ->columns(
                Auth::user()->id_roles == 2 ? [ // Jika user biasa, hanya tampilkan data tanpa pencarian
                    // TextColumn::make('user.nip')
                    //     ->label('NIP'),
                    TextColumn::make('user.name')
                        ->label('Nama Karyawan'),
                    // TextColumn::make('user.employe.position')
                    //     ->label('Jabatan'),
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
                    // TextColumn::make('durasi_kerja')
                    //     ->label('Durasi Kerja')
                    //     ->sortable(),
                    // TextColumn::make('lokasi_masuk')
                    //     ->label('Lokasi Masuk')
                    //     ->icon('heroicon-o-map-pin')
                    //     ->getStateUsing(fn($record) => 
                    //         "<a href='https://www.google.com/maps?q={$record->absenMasuk?->latitude},{$record->absenMasuk?->longitude}' 
                    //             target='_blank' >Maps</a>")
                    //     ->html()
                    //     ->tooltip('Klik untuk melihat lokasi'),
                    // TextColumn::make('lokasi_keluar')
                    //     ->label('Lokasi Keluar')
                    //     ->icon('heroicon-o-map-pin')
                    //     ->getStateUsing(fn($record) => 
                    //         "<a href='https://www.google.com/maps?q={$record->absenKeluar?->latitude},{$record->absenKeluar?->longitude}' 
                    //         target='_blank' >Maps</a>")
                    //     ->html()
                    //     ->tooltip('Klik untuk melihat lokasi'),                
                    // ImageColumn::make('absenMasuk.foto')
                    //     ->label('Foto Absen')
                    //     ->disk('public'),
                    // TextColumn::make('absenMasuk.desc')
                    //     ->label('Keterangan'), 
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
                        ->label('Durasi Kerja')
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
                        ->disabled(fn () => Auth::user()?->id_roles !== 1),
                    TextColumn::make('updatedBy.name')
                        ->label('Di Approve Oleh')
                        ->searchable(),
                ]);
    }
}
