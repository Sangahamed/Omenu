@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand-red text-start text-base font-medium text-brand-red bg-brand-red-soft focus:outline-none focus:text-brand-red focus:bg-brand-red-soft focus:border-brand-red transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-ink-soft hover:text-brand-black hover:bg-[#EFEFEC] hover:border-border focus:outline-none focus:text-brand-black focus:bg-[#EFEFEC] focus:border-border transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
