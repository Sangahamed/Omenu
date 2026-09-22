<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-display font-semibold text-brand-black tracking-tight">Messages</h1>
            <p class="text-xs text-ink-soft uppercase tracking-widest font-semibold mt-1">
                Formulaire de contact du site
            </p>
        </div>

        @if($pendingCount > 0)
            <span class="bg-brand-red-soft border border-brand-red text-brand-red text-xs font-semibold px-4 py-2 rounded-sm">
                {{ $pendingCount }} à traiter
            </span>
        @endif
    </div>

    <div class="flex flex-wrap items-center gap-2 mb-4">
        @foreach($filters as $key => $label)
            <button type="button" wire:click="setFilter('{{ $key }}')"
                    class="text-xs font-semibold px-3 py-1.5 rounded-sm border transition-colors
                           {{ $filter === $key
                              ? 'bg-brand-black text-white border-brand-black'
                              : 'bg-white text-ink-soft border-border hover:border-brand-black' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="bg-white border border-border rounded-md divide-y divide-border">
        @forelse($messages as $item)
            <div class="{{ $item->handled_at ? 'opacity-60' : '' }}">
                <button type="button" wire:click="toggle({{ $item->id }})"
                        class="w-full text-left px-5 py-4 hover:bg-[#FBFBFA] transition-colors flex items-start gap-4">
                    <i class="{{ $item->handled_at ? 'ri-mail-open-line' : 'ri-mail-line' }} text-lg text-brand-red mt-0.5 flex-shrink-0"></i>

                    <div class="min-w-0 flex-1">
                        <p class="font-display font-semibold text-sm text-ink truncate">{{ $item->subject }}</p>
                        <p class="text-xs text-ink-soft mt-0.5 truncate">
                            {{ $item->name }} &lt;{{ $item->email }}&gt;
                            @if($item->user) · <span class="text-brand-red">compte #{{ $item->user_id }}</span> @endif
                        </p>
                    </div>

                    <div class="text-right flex-shrink-0">
                        <p class="text-[11px] text-ink-soft">{{ $item->created_at->diffForHumans() }}</p>
                        @if($item->handled_at)
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-[#1F7A4D]">Traité</span>
                        @endif
                    </div>

                    <i class="ri-arrow-down-s-line text-ink-soft transition-transform {{ $openId === $item->id ? 'rotate-180' : '' }}"></i>
                </button>

                @if($openId === $item->id)
                    <div class="px-5 pb-5 pl-14">
                        <div class="bg-[#FBFBFA] border border-border rounded-sm p-4">
                            <p class="text-sm text-ink whitespace-pre-line leading-relaxed">{{ $item->message }}</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 mt-3">
                            <a href="mailto:{{ $item->email }}?subject={{ rawurlencode('Re : '.$item->subject) }}"
                               class="bg-brand-black hover:bg-brand-black-2 text-white text-xs px-3 py-1.5 rounded-sm">
                                <i class="ri-reply-line"></i> Répondre par e-mail
                            </a>

                            <button wire:click="markHandled({{ $item->id }})"
                                    class="bg-white border border-brand-black text-brand-black text-xs px-3 py-1.5 rounded-sm">
                                {{ $item->handled_at ? 'Remettre à traiter' : 'Marquer traité' }}
                            </button>

                            <button wire:click="delete({{ $item->id }})"
                                    wire:confirm="Supprimer ce message ?"
                                    class="bg-brand-red hover:bg-brand-red-hover text-white text-xs px-3 py-1.5 rounded-sm">
                                Supprimer
                            </button>

                            <span class="text-[11px] text-ink-soft ml-auto">
                                Reçu le {{ $item->created_at->format('d/m/Y à H:i') }}
                                @if($item->ip_address) · {{ $item->ip_address }} @endif
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="px-5 py-14 text-center">
                <i class="ri-inbox-line text-3xl text-ink-soft block mb-2"></i>
                <p class="text-sm text-ink-soft">Aucun message pour ce filtre.</p>
            </div>
        @endforelse
    </div>

    <div class="pagination mt-4">
        {{ $messages->links() }}
    </div>
</div>
