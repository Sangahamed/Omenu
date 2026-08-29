@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-brand-black']) }}>
        {{ $status }}
    </div>
@endif
