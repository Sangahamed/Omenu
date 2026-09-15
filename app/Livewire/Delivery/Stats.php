<?php

namespace App\Livewire\Delivery;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Gains et performance du livreur connecté.
 */
class Stats extends Component
{
    /** Périodes acceptées. `$period` est une propriété publique : elle arrive
     *  du navigateur et ne doit jamais servir de clé de `match` sans garde. */
    public const PERIODS = [
        'today' => "Aujourd'hui",
        'week' => 'Cette semaine',
        'month' => 'Ce mois',
        'all' => 'Depuis le début',
    ];

    #[Url(except: 'today')]
    public string $period = 'today';

    public function setPeriod(string $period): void
    {
        $this->period = array_key_exists($period, self::PERIODS) ? $period : 'today';
    }

    /**
     * Commandes livrées par ce livreur sur la période choisie.
     */
    protected function deliveredQuery(): Builder
    {
        $query = Order::where('delivery_person_id', Auth::id())
            ->where('status', 'delivered')
            ->whereNotNull('delivered_at');

        return match ($this->period) {
            'week' => $query->whereBetween('delivered_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereBetween('delivered_at', [now()->startOfMonth(), now()->endOfMonth()]),
            'all' => $query,
            // « today » et toute valeur inattendue : une période inconnue ne doit
            // pas lever d'UnhandledMatchError depuis une requête forgée.
            default => $query->whereDate('delivered_at', now()->toDateString()),
        };
    }

    public function render()
    {
        $orders = $this->deliveredQuery()
            ->with('restaurant')
            ->latest('delivered_at')
            ->limit(50)
            ->get();

        $totalOrders = $this->deliveredQuery()->count();
        $commission = config('delivery.commission_per_order');

        /*
         * Durée moyenne de course : de la prise en charge à la livraison.
         * Les commandes sans picked_up_at (livraisons antérieures au suivi)
         * sont exclues plutôt que comptées à zéro.
         */
        $timed = $this->deliveredQuery()
            ->whereNotNull('picked_up_at')
            ->get(['picked_up_at', 'delivered_at']);

        $averageMinutes = $timed->isEmpty()
            ? null
            : (int) round($timed->avg(fn ($o) => $o->picked_up_at->diffInMinutes($o->delivered_at)));

        return view('livewire.delivery.stats', [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'totalEarnings' => $totalOrders * $commission,
            'commission' => $commission,
            'deliveredValue' => (float) $this->deliveredQuery()->sum('total'),
            'averageMinutes' => $averageMinutes,
            'periods' => self::PERIODS,
            'lifetimeOrders' => Order::where('delivery_person_id', Auth::id())
                ->where('status', 'delivered')
                ->count(),
        ])->extends('components.front.layouts.front');
    }
}
