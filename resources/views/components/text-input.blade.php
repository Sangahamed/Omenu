@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-border focus:border-brand-red focus:ring-brand-red rounded-sm shadow-sm']) }}>
