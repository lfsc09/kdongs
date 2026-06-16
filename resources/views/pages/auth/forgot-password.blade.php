<x-layouts::auth :title="__('Forgot password')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :description="__('Enter your email to receive a password reset link')"
            :title="__('Forgot password')"
        />

        <!-- Session Status -->
        <x-auth-session-status
            :status="session('status')"
            class="text-center"
        />

        <form
            action="{{ route('password.email') }}"
            class="flex flex-col gap-6"
            method="POST"
        >
            @csrf

            <!-- Email Address -->
            <flux:input
                :label="__('Email address')"
                autofocus
                name="email"
                placeholder="email@example.com"
                required
                type="email"
            />

            <flux:button
                class="w-full"
                data-test="email-password-reset-link-button"
                type="submit"
                variant="primary"
            >
                {{ __('Email password reset link') }}
            </flux:button>
        </form>

        <div class="space-x-1 text-center text-sm text-zinc-400 rtl:space-x-reverse">
            <span>{{ __('Or, return to') }}</span>
            <flux:link
                :href="route('login')"
                wire:navigate
            >{{ __('log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
