<x-layouts::auth :title="__('Confirm password')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :description="__('This is a secure area of the application. Please confirm your password before continuing.')"
            :title="__('Confirm password')"
        />

        <x-auth-session-status
            :status="session('status')"
            class="text-center"
        />


        <form
            action="{{ route('password.confirm.store') }}"
            class="flex flex-col gap-6"
            method="POST"
        >
            @csrf

            <flux:input
                :label="__('Password')"
                :placeholder="__('Password')"
                autocomplete="current-password"
                name="password"
                required
                type="password"
                viewable
            />

            <flux:button
                class="w-full"
                data-test="confirm-password-button"
                type="submit"
                variant="primary"
            >
                {{ __('Confirm') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
