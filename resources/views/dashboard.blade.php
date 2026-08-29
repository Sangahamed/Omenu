<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-brand-black leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border border-border rounded-md">
                <div class="p-6 text-ink">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
