<div>
    {{-- Statistiques --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white p-4 rounded-xl shadow-md border-l-4 border-indigo-600">
            <p class="text-xs text-gray-400 uppercase">Utilisateurs</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-md border-l-4 border-amber-600">
            <p class="text-xs text-gray-400 uppercase">Restaurants</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_restaurants'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-md border-l-4 border-blue-600">
            <p class="text-xs text-gray-400 uppercase">Commandes</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_orders'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-md border-l-4 border-green-600">
            <p class="text-xs text-gray-400 uppercase">Chiffre d'affaires</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_revenue']) }} FCFA</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-md border-l-4 border-yellow-600">
            <p class="text-xs text-gray-400 uppercase">Commandes en attente</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-md border-l-4 border-purple-600">
            <p class="text-xs text-gray-400 uppercase">Restaurants actifs</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['active_restaurants'] }}</p>
        </div>
    </div>

    {{-- Filtre de période --}}
    <div class="flex justify-end mb-4">
        <select wire:model.live="period" class="border-gray-300 rounded-lg text-sm">
            <option value="week">Cette semaine</option>
            <option value="month">Ce mois</option>
            <option value="year">Cette année</option>
        </select>
    </div>

    {{-- Graphiques --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="font-semibold text-gray-700 mb-4">Chiffre d'affaires</h3>
            <canvas id="revenueChart" width="400" height="200"></canvas>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="font-semibold text-gray-700 mb-4">Nombre de commandes</h3>
            <canvas id="ordersChart" width="400" height="200"></canvas>
        </div>
    </div>

    {{-- Export --}}
    <div class="mt-8 bg-white p-6 rounded-xl shadow-md">
        <h3 class="font-semibold text-gray-700 mb-4">Exporter les données</h3>
        <button wire:click="exportCsv" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition text-sm">
            <i class="ri-file-excel-2-line"></i> Exporter les commandes (CSV)
        </button>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData);

        // Graphique des revenus
        const ctx1 = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'CA (FCFA)',
                    data: chartData.revenues,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
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

        // Graphique des commandes
        const ctx2 = document.getElementById('ordersChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Commandes',
                    data: chartData.orders,
                    backgroundColor: 'rgba(79, 70, 229, 0.6)',
                    borderColor: '#4f46e5',
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