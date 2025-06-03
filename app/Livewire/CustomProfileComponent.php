<?php

namespace App\Livewire;

use Filament\Forms;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;

class CustomProfileComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 0;

    public function mount(): void
    {
        $user = Auth::user();
        $employe = $user->employe;

        $this->form->fill([
            'nip' => $employe ? $employe->nip : null,
            'position' => $employe ? $employe->position : null,
            'education' => $employe ? $employe->education : null,
            'join_date' => $employe ? $employe->join_date : null,
            // Tambahkan field lain dari tabel employees jika diperlukan
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Data Karyawan')
                    ->aside('Data Karyawan')
                    ->description('Pastikan data Anda sudah sesuai.')
                    ->schema(
                        Auth::user()->id_roles === 1 ? [ // Cek jika user adalah admin
                            TextInput::make('nip')
                                ->label('NIP')
                                ->disabled(),
                            TextInput::make('position')
                                ->label('Jabatan'),
                            Grid::make()
                                ->schema([
                                    TextInput::make('education')
                                        ->label('Pendidikan')
                                        ->disabled(),
                                    DatePicker::make('join_date')
                                        ->native(false)
                                        ->label('Tanggal Bergabung')
                                        ->disabled(),
                                ])->columns(2),
                        ] : [] // Jika bukan admin, elemen tidak ada
                    ),
            ])
            ->statePath('data');
    }
    
    public function save(): void
    {
        if (Auth::user()->id_roles !== 1) {
            abort(403, 'Unauthorized'); // Mencegah user biasa menyimpan data
        }
    
        $data = $this->form->getState();
        $user = Auth::user();
        $employe = $user->employe;

        // Memeriksa apakah tidak ada perubahan pada data
        if (
            $data['position'] === $employe->position
        ) {
            Notification::make()
                ->title('Tidak ada perubahan data')
                ->warning()
                ->body('Data yang Anda coba simpan tidak mengalami perubahan.')
                ->send();

            return; // Menghentikan proses penyimpanan jika tidak ada perubahan
        }
    
        // $user->update([
        //     'name' => $data['name'],
        // ]);
    
        if ($employe) {
            $employe->update([
                'position' => $data['position'],
            ]);
        }
        
        Notification::make()
            ->title('Data Karyawan berhasil disimpan!')
            ->success()
            ->send();
    }
    
    public function render(): View
    {
        return view('livewire.custom-profile-component', [
            'isAdmin' => Auth::user()->id_roles === 1, // Kirim variabel ke tampilan
        ]);
    }
    
}