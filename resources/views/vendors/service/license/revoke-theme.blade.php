@if(! config('app.app_sync'))
    @if(class_exists(\Filament\Facades\Filament::class))
        {{-- Filament / Tailwind context --}}
        <x-filament::modal id="revoke-theme-{{ $name }}" width="md">
            <x-slot name="trigger">
                <x-filament::button color="danger" size="xs" outlined>
                    {{ __('Revoke License') }}
                </x-filament::button>
            </x-slot>

            <x-slot name="heading">
                {{ __('Revoke Theme License') }}
            </x-slot>

            <x-slot name="description">
                <span class="text-danger-600 dark:text-danger-400 font-medium" id="revoke-theme-msg-{{ $name }}">
                    {{ __('If you revoke your license, your theme data will be removed. Please take a backup of your data before revoking the theme license.') }}
                </span>
            </x-slot>

            <form
                method="POST"
                action="{{ route('service.revoke.theme') }}"
                x-data="{ submitting: false }"
                x-on:submit="submitting = true; document.getElementById('revoke-theme-msg-{{ $name }}').textContent = '{{ __('Please wait. We are revoking your theme license. Do not refresh this page or close the browser.') }}'"
            >
                @csrf
                <input type="hidden" name="name" value="{{ $name }}">
                <div class="flex justify-end gap-3">
                    <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'revoke-theme-{{ $name }}' })" x-bind:disabled="submitting">
                        {{ __('Cancel') }}
                    </x-filament::button>
                    <x-filament::button type="submit" color="danger" x-bind:disabled="submitting" x-bind:class="{ 'opacity-70 pointer-events-none': submitting }">
                        <span x-show="!submitting">{{ __('Revoke License') }}</span>
                        <span x-show="submitting" x-cloak class="inline-flex items-center gap-1.5">
                            <x-filament::loading-indicator class="h-4 w-4" />
                            {{ __('Revoking...') }}
                        </span>
                    </x-filament::button>
                </div>
            </form>
        </x-filament::modal>
    @else
        {{-- Vanilla JS context --}}
        <button type="button" class="btn btn-link text-danger btn-sm" data-modal-open="revoke-theme-{{ $name }}">
            {{ __('Revoke License') }}
        </button>

        <div class="modal-overlay" id="revoke-theme-{{ $name }}">
            <div class="modal-box">
                <div class="modal-header">
                    <h3>{{ __('Revoke Theme License') }}</h3>
                    <button type="button" class="modal-close" data-modal-close>
                        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('service.revoke.theme') }}" data-modal-form>
                    @csrf
                    <input type="hidden" name="name" value="{{ $name }}">

                    <div class="modal-body">
                        <p class="text-danger font-medium"
                           data-modal-message
                           data-loading-message="{{ __('Please wait. We are revoking your theme license. Do not refresh this page or close the browser.') }}">
                            {{ __('If you revoke your license, your theme data will be removed. Please take a backup of your data before revoking the theme license.') }}
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost" data-modal-close>
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger btn-submit">
                            {{ __('Revoke License') }}
                        </button>
                        <button type="button" class="btn btn-danger btn-loading hidden" disabled>
                            <span class="spinner spinner-sm"></span>
                            {{ __('Revoking...') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endif
