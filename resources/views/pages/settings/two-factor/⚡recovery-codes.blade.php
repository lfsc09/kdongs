<?php

use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public array $recoveryCodes = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->loadRecoveryCodes();
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generateNewRecoveryCodes): void
    {
        $generateNewRecoveryCodes(auth()->user());

        $this->loadRecoveryCodes();
    }

    /**
     * Load the recovery codes for the user.
     */
    private function loadRecoveryCodes(): void
    {
        $user = auth()->user();

        if ($user->hasEnabledTwoFactorAuthentication() && $user->two_factor_recovery_codes) {
            try {
                $this->recoveryCodes = json_decode((string) decrypt($user->two_factor_recovery_codes), true);
            } catch (Exception) {
                $this->addError('recoveryCodes', 'Failed to load recovery codes');

                $this->recoveryCodes = [];
            }
        }
    }
}; ?>

<div
    class="space-y-6 rounded-xl border border-zinc-200 py-6 shadow-sm dark:border-white/10"
    wire:cloak
    x-data="{ showRecoveryCodes: false }"
>
    <div class="space-y-2 px-6">
        <div class="flex items-center gap-2">
            <flux:icon.lock-closed
                class="size-4"
                variant="outline"
            />
            <flux:heading
                level="3"
                size="lg"
            >{{ __('2FA recovery codes') }}</flux:heading>
        </div>
        <flux:text variant="subtle">
            {{ __('Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.') }}
        </flux:text>
    </div>

    <div class="px-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <flux:button
                @click="showRecoveryCodes = true;"
                aria-controls="recovery-codes-section"
                aria-expanded="false"
                icon:variant="outline"
                icon="eye"
                variant="primary"
                x-show="!showRecoveryCodes"
            >
                {{ __('View recovery codes') }}
            </flux:button>

            <flux:button
                @click="showRecoveryCodes = false"
                aria-controls="recovery-codes-section"
                aria-expanded="true"
                icon:variant="outline"
                icon="eye-slash"
                variant="primary"
                x-show="showRecoveryCodes"
            >
                {{ __('Hide recovery codes') }}
            </flux:button>

            @if (filled($recoveryCodes))
                <flux:button
                    icon="arrow-path"
                    variant="filled"
                    wire:click="regenerateRecoveryCodes"
                    x-show="showRecoveryCodes"
                >
                    {{ __('Regenerate codes') }}
                </flux:button>
            @endif
        </div>

        <div
            class="relative overflow-hidden"
            id="recovery-codes-section"
            x-bind:aria-hidden="!showRecoveryCodes"
            x-show="showRecoveryCodes"
            x-transition
        >
            <div class="mt-3 space-y-3">
                @error('recoveryCodes')
                    <flux:callout
                        heading="{{ $message }}"
                        icon="x-circle"
                        variant="danger"
                    />
                @enderror

                @if (filled($recoveryCodes))
                    <div
                        aria-label="{{ __('Recovery codes') }}"
                        class="grid gap-1 rounded-lg bg-zinc-100 p-4 font-mono text-sm dark:bg-white/5"
                        role="list"
                    >
                        @foreach ($recoveryCodes as $code)
                            <div
                                class="select-text"
                                role="listitem"
                                wire:loading.class="opacity-50 animate-pulse"
                            >
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                    <flux:text
                        class="text-xs"
                        variant="subtle"
                    >
                        {{ __('Each recovery code can be used once to access your account and will be removed after use. If you need more, click Regenerate codes above.') }}
                    </flux:text>
                @endif
            </div>
        </div>
    </div>
</div>
