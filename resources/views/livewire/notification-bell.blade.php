<div class="relative" wire:poll.30s>
    <button
        type="button"
        wire:click="toggle"
        class="relative flex items-center justify-center w-10 h-10 border border-border hover:border-brand-black rounded-sm text-ink transition-colors"
        aria-label="Notifications"
        aria-expanded="{{ $open ? 'true' : 'false' }}">
        <i class="ri-notification-3-line text-lg"></i>

        @if($unreadCount > 0)
            <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center bg-brand-red text-white text-[10px] font-bold rounded-pill">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    @if($open)
        {{-- Calque de fermeture : un clic en dehors referme le panneau --}}
        <div class="fixed inset-0 z-40" wire:click="toggle"></div>

        <div class="absolute right-0 mt-2 w-[min(22rem,calc(100vw-2rem))] bg-white border border-brand-black rounded-sm shadow-xl z-50">
            <div class="flex items-center justify-between px-4 py-3 border-b border-border">
                <h3 class="font-display font-semibold text-sm text-ink">Notifications</h3>

                <div class="flex items-center gap-3 text-[11px]">
                    @if($unreadCount > 0)
                        <button type="button" wire:click="markAllAsRead" class="text-brand-red hover:underline">
                            Tout marquer lu
                        </button>
                    @endif

                    @if($notifications->isNotEmpty())
                        <button type="button" wire:click="clearAll" class="text-ink-soft hover:text-ink">
                            Vider
                        </button>
                    @endif
                </div>
            </div>

            {{-- Activation des notifications systeme du navigateur (Web Push) --}}
            <div
                x-data="{ state: 'unknown' }"
                x-init="
                    if (!window.omenuPush || !window.omenuPush.isSupported()) {
                        state = 'unsupported';
                    } else {
                        window.omenuPush.isSubscribed().then(v => state = v ? 'on' : 'off');
                    }
                    window.addEventListener('push-subscription-changed', e => state = e.detail.subscribed ? 'on' : 'off');
                "
                class="px-4 py-2.5 border-b border-border bg-[#FAFAF8] flex items-center justify-between gap-3">
                <span class="text-[11px] text-ink-soft leading-snug">
                    <i class="ri-smartphone-line text-brand-red"></i>
                    Notifications sur cet appareil
                </span>

                <template x-if="state === 'off'">
                    <button type="button"
                            @click="window.omenuPush.subscribe()"
                            class="text-[11px] font-semibold text-white bg-brand-black hover:bg-brand-black-2 px-2.5 py-1 rounded-sm">
                        Activer
                    </button>
                </template>

                <template x-if="state === 'on'">
                    <button type="button"
                            @click="window.omenuPush.unsubscribe()"
                            class="text-[11px] font-semibold text-ink-soft border border-border hover:border-brand-black px-2.5 py-1 rounded-sm">
                        Désactiver
                    </button>
                </template>

                <template x-if="state === 'unsupported'">
                    <span class="text-[10px] text-ink-soft italic">Indisponible</span>
                </template>
            </div>

            <div class="max-h-[22rem] overflow-y-auto divide-y divide-border">
                @forelse($notifications as $notification)
                    @php($data = $notification->data)
                    <a
                        href="{{ $data['url'] ?? '#' }}"
                        wire:click="markAsRead('{{ $notification->id }}')"
                        class="flex gap-3 px-4 py-3 hover:bg-[#FAFAF8] transition-colors {{ $notification->read_at ? 'opacity-60' : '' }}">
                        <i class="{{ $data['icon'] ?? 'ri-notification-3-line' }} text-lg text-brand-red mt-0.5"></i>

                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-display font-semibold text-ink leading-snug">
                                {{ $data['title'] ?? 'Notification' }}
                            </p>
                            <p class="text-[11px] text-ink-soft leading-relaxed mt-0.5">
                                {{ $data['body'] ?? '' }}
                            </p>
                            <p class="text-[10px] text-ink-soft mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        @unless($notification->read_at)
                            <span class="w-2 h-2 rounded-full bg-brand-red mt-1.5 flex-shrink-0"></span>
                        @endunless
                    </a>
                @empty
                    <div class="px-4 py-10 text-center">
                        <i class="ri-inbox-line text-3xl text-ink-soft block mb-2"></i>
                        <p class="text-xs text-ink-soft">Aucune notification pour le moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
