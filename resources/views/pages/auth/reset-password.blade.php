<x-layouts::auth :title="__('Reset password')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :description="__('Please enter your new password below')"
            :title="__('Reset password')"
        />

        <!-- Session Status -->
        <x-auth-session-status
            :status="session('status')"
            class="text-center"
        />

        <form
            action="{{ route('password.update') }}"
            class="flex flex-col gap-6"
            method="POST"
        >
            @csrf
            <!-- Token -->
            <input
                name="token"
                type="hidden"
                value="{{ request()->route('token') }}"
            >

            <!-- Email Address -->
            <flux:input
                :label="__('Email')"
                autocomplete="email"
                name="email"
                required
                type="email"
                value="{{ request('email') }}"
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
                    data-test="reset-password-button"
                    type="submit"
                    variant="primary"
                >
                    {{ __('Reset password') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts::auth>
