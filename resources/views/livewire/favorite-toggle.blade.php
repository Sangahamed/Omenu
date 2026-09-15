<div>
    <button
        type="button"
        wire:click="toggle"
        wire:loading.attr="disabled"
        aria-pressed="{{ $isFavorite ? 'true' : 'false' }}"
        title="{{ $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
        @class([
            'inline-flex items-center gap-2 rounded-sm font-semibold transition-colors disabled:opacity-60',
            'px-5 py-3 text-xs uppercase tracking-wider' => ! $compact,
            'w-9 h-9 justify-center text-base' => $compact,
            'bg-brand-red text-white hover:bg-brand-red-hover' => $isFavorite,
            'bg-white text-brand-black hover:bg-brand-red hover:text-white' => ! $isFavorite,
        ])>
        <i class="{{ $isFavorite ? 'ri-heart-fill' : 'ri-heart-line' }} {{ $compact ? '' : 'text-base' }}"></i>

        @unless($compact)
            <span>{{ $isFavorite ? 'Dans mes favoris' : 'Ajouter aux favoris' }}</span>
        @endunless
    </button>
</div>
