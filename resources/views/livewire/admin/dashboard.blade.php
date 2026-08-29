<div>
    {{-- Statistiques --}}
    <div class="stats-grid grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="stat-card bg-white p-4 rounded-md border border-border border-l-4 border-l-brand-black">
            <p class="text-xs text-ink-soft uppercase">Utilisateurs</p>
            <p class="text-2xl font-display font-bold text-brand-black">{{ $stats['total_users'] }}</p>
        </div>
        <div class="stat-card bg-white p-4 rounded-md border border-border border-l-4 border-l-brand-red">
            <p class="text-xs text-ink-soft uppercase">Restaurants</p>
            <p class="text-2xl font-display font-bold text-brand-black">{{ $stats['total_restaurants'] }}</p>
        </div>
        <div class="stat-card bg-white p-4 rounded-md border border-border border-l-4 border-l-brand-black">
            <p class="text-xs text-ink-soft uppercase">Commandes</p>
            <p class="text-2xl font-display font-bold text-brand-black">{{ $stats['total_orders'] }}</p>
        </div>
        <div class="stat-card bg-white p-4 rounded-md border border-border border-l-4 border-l-brand-red">
            <p class="text-xs text-ink-soft uppercase">Chiffre d'affaires</p>
            <p class="text-2xl font-display font-bold text-brand-black">{{ number_format($stats['total_revenue']) }} FCFA</p>
        </div>
        <div class="stat-card bg-white p-4 rounded-md border border-border border-l-4 border-l-border-strong">
            <p class="text-xs text-ink-soft uppercase">Commandes en attente</p>
            <p class="text-2xl font-display font-bold text-ink-soft">{{ $stats['pending_orders'] }}</p>
        </div>
        <div class="stat-card bg-white p-4 rounded-md border border-border border-l-4 border-l-brand-black">
            <p class="text-xs text-ink-soft uppercase">Restaurants actifs</p>
            <p class="text-2xl font-display font-bold text-brand-black">{{ $stats['active_restaurants'] }}</p>
        </div>
    </div>

    {{-- Filtre de période --}}
    <div class="flex justify-end mb-4">
        <select wire:model.live="period" class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm text-sm">
            <option value="week">Cette semaine</option>
            <option value="month">Ce mois</option>
            <option value="year">Cette année</option>
        </select>
    </div>

    {{-- Graphiques --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="card bg-white p-6 rounded-md border border-border">
            <h3 class="font-display font-semibold text-ink mb-4">Chiffre d'affaires</h3>
            <canvas id="revenueChart" width="400" height="200"></canvas>
        </div>
        <div class="card bg-white p-6 rounded-md border border-border">
            <h3 class="font-display font-semibold text-ink mb-4">Nombre de commandes</h3>
            <canvas id="ordersChart" width="400" height="200"></canvas>
        </div>
    </div>

    {{-- Export --}}
    <div class="card mt-8 bg-white p-6 rounded-md border border-border">
        <h3 class="font-display font-semibold text-ink mb-4">Exporter les données</h3>
        <button wire:click="exportCsv" class="bg-brand-black hover:bg-brand-black-2 text-white px-4 py-2 rounded-sm transition text-sm">
            <i class="ri-file-excel-2-line"></i> Exporter les commandes (CSV)
        </button>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData);

        // Graphique des revenus (couleur de marque : rouge OMenu)
        const ctx1 = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'CA (FCFA)',
                    data: chartData.revenues,
                    borderColor: '#A9271E',
                    backgroundColor: 'rgba(169, 39, 30, 0.08)',
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Graphique des commandes (noir de marque)
        const ctx2 = document.getElementById('ordersChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Commandes',
                    data: chartData.orders,
                    backgroundColor: 'rgba(18, 18, 18, 0.75)',
                    borderColor: '#121212',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, stepSize: 1 }
                }
            }
        });

        // Mise à jour des graphiques quand les données changent
        Livewire.on('updated', () => {
            window.location.reload(); // simple mais efficace pour ce contexte
        });
    });
</script>
@endpush
