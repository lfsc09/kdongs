<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :description="__('Enter your email and password below to log in')"
            :title="__('Log in to your account')"
        />

        <!-- Session Status -->
        <x-auth-session-status
            :status="session('status')"
            class="text-center"
        />


        <form
            action="{{ route('login.store') }}"
            class="flex flex-col gap-6"
            method="POST"
        >
            @csrf

            <!-- Email Address -->
            <flux:input
                :label="__('Email address')"
                :value="old('email')"
                autocomplete="email"
                autofocus
                name="email"
                placeholder="email@example.com"
                required
                type="email"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    :label="__('Password')"
                    :placeholder="__('Password')"
                    autocomplete="current-password"
                    name="password"
                    required
                    type="password"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link
                        :href="route('password.request')"
                        class="absolute end-0 top-0 text-sm"
                        wire:navigate
                    >
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox
                :checked="old('remember')"
                :label="__('Remember me')"
                name="remember"
            />

            <div class="flex items-center justify-end">
                <flux:button
                    class="w-full"
                    data-test="login-button"
                    type="submit"
                    variant="primary"
                >
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-center text-sm text-zinc-600 rtl:space-x-reverse dark:text-zinc-400">
            <span>{{ __('Don\'t have an account?') }}</span>
            <flux:link
                :href="route('register')"
                wire:navigate
            >{{ __('Sign up') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
