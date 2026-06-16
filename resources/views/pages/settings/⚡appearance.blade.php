<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Appearance settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Appearance settings') }}</flux:heading>

    <x-pages::settings.layout
        :heading="__('Appearance')"
        :subheading="__('Update the appearance settings for your account')"
    >
        <flux:radio.group
            variant="segmented"
            x-data
            x-model="$flux.appearance"
        >
            <flux:radio
                icon="sun"
                value="light"
            >{{ __('Light') }}</flux:radio>
            <flux:radio
                icon="moon"
                value="dark"
            >{{ __('Dark') }}</flux:radio>
            <flux:radio
                icon="computer-desktop"
                value="system"
            >{{ __('System') }}</flux:radio>
        </flux:radio.group>
    </x-pages::settings.layout>
</section>
