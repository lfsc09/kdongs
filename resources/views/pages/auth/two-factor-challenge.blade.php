<x-layouts::auth :title="__('Two-factor authentication')">
    <div class="flex flex-col gap-6">
        <div
            class="relative h-auto w-full"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                focusOtp() {
                    this.$nextTick(() => this.$refs.otp?.querySelector('input')?.focus());
                },
                init() {
                    if (!this.showRecoveryInput) {
                        this.focusOtp();
                    }
                },
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;
            
                    this.code = '';
                    this.recovery_code = '';
            
                    $nextTick(() => {
                        this.showRecoveryInput ?
                            this.$refs.recovery_code?.focus() :
                            this.focusOtp();
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    :description="__('Enter the authentication code provided by your authenticator application.')"
                    :title="__('Authentication code')"
                />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header
                    :description="__(
                        'Please confirm access to your account by entering one of your emergency recovery codes.',
                    )"
                    :title="__('Recovery code')"
                />
            </div>

            <form
                action="{{ route('two-factor.login.store') }}"
                method="POST"
            >
                @csrf

                <div class="space-y-5 text-center">
                    <div x-show="!showRecoveryInput">
                        <div
                            class="my-5 flex items-center justify-center"
                            x-ref="otp"
                        >
                            <flux:otp
                                class="mx-auto"
                                label:sr-only
                                label="OTP Code"
                                length="6"
                                name="code"
                                x-model="code"
                            />
                        </div>
                    </div>

                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <flux:input
                                autocomplete="one-time-code"
                                name="recovery_code"
                                type="text"
                                x-bind:required="showRecoveryInput"
                                x-model="recovery_code"
                                x-ref="recovery_code"
                            />
                        </div>

                        @error('recovery_code')
                            <flux:text color="red">
                                {{ $message }}
                            </flux:text>
                        @enderror
                    </div>

                    <flux:button
                        class="w-full"
                        type="submit"
                        variant="primary"
                    >
                        {{ __('Continue') }}
                    </flux:button>
                </div>

                <div class="mt-5 space-x-0.5 text-center text-sm leading-5">
                    <span class="opacity-50">{{ __('or you can') }}</span>
                    <div class="inline cursor-pointer font-medium underline opacity-80">
                        <span
                            @click="toggleInput()"
                            x-show="!showRecoveryInput"
                        >{{ __('login using a recovery code') }}</span>
                        <span
                            @click="toggleInput()"
                            x-show="showRecoveryInput"
                        >{{ __('login using an authentication code') }}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>
