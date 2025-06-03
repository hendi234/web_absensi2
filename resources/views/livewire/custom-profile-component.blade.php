<div>
    @if(Auth::check() && Auth::user()->id_roles === 1)
        <x-filament-panels::form wire:submit="save">
            {{ $this->form }}

            <div class="fi-form-actions">
                <div class="flex flex-row-reverse flex-wrap items-center gap-3 fi-ac">
                    <x-filament::button type="submit">
                        {{ __('filament-edit-profile::default.save') }}
                    </x-filament::button>
                </div>
            </div>
        </x-filament-panels::form>
    @endif

    <x-filament-actions::modals />
</div>
