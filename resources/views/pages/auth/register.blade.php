<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :description="__('Enter your details below to create your account')"
            :title="__('Create an account')"
        />

        <!-- Session Status -->
        <x-auth-session-status
            :status="session('status')"
            class="text-center"
        />

        <form
            action="{{ route('register.store') }}"
            class="flex flex-col gap-6"
            method="POST"
        >
            @csrf
            <!-- Name -->
            <flux:input
                :label="__('Name')"
                :placeholder="__('Full name')"
                :value="old('name')"
                autocomplete="name"
                autofocus
                name="name"
                required
                type="text"
            />

            <!-- Email Address -->
            <flux:input
                :label="__('Email address')"
                :value="old('email')"
                autocomplete="email"
                name="email"
                placeholder="email@example.com"
                required
                type="email"
            />

            <!-- Password -->
            <flux:input
                :label="__('Password')"
                :placeholder="__('Password')"
                autocomplete="new-password"
                name="password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                required
                type="password"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                :label="__('Confirm password')"
                :placeholder="__('Confirm password')"
                autocomplete="new-password"
                name="password_confirmation"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                required
                type="password"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button
                    class="w-full"
                    data-test="register-user-button"
                    type="submit"
                    variant="primary"
                >
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-center text-sm text-zinc-600 rtl:space-x-reverse dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link
                :href="route('login')"
                wire:navigate
            >{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
