@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Estadísticas</h1>
            <p class="mt-1 text-sm text-slate-500">
                Resumen detallado de la respuesta de tus invitados.
            </p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Botones PREMIUM (por ahora deshabilitados) --}}
            <button type="button"
                    class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold
                           bg-slate-100 text-slate-400 cursor-not-allowed"
                    title="Disponible en versión Premium">
                ⬇️ Excel (Premium)
            </button>
            <button type="button"
                    class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold
                           bg-slate-100 text-slate-400 cursor-not-allowed"
                    title="Disponible en versión Premium">
                ⬇️ PDF (Premium)
            </button>
        </div>
    </div>

    @if (! $event)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 text-sm text-slate-600">
            Aún no tienes eventos creados. Crea tu primer evento para ver estadísticas.
        </div>
        @return
    @endif

    {{-- Tarjeta resumen --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500">Total Invitados</h3>
            <p class="mt-1 text-3xl md:text-4xl font-bold text-gray-900">{{ $totalInvitados }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500">Confirmados</h3>
            <p class="mt-1 text-3xl md:text-4xl font-bold text-teal-600">{{ $confirmados }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500">Pendientes</h3>
            <p class="mt-1 text-3xl md:text-4xl font-bold text-orange-500">{{ $pendientes }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500">No asisten</h3>
            <p class="mt-1 text-3xl md:text-4xl font-bold text-red-600">{{ $noAsisten }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500">Tasa de respuesta</h3>
            <p class="mt-1 text-3xl md:text-4xl font-bold text-purple-700">
                {{ $tasaRespuesta }}%
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Gráfica circular --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-sm font-semibold text-slate-800 mb-4">Distribución de respuestas</h2>
            <canvas id="pieChart" height="260"></canvas>
        </div>

        {{-- Línea de tiempo --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-sm font-semibold text-slate-800 mb-4">Evolución de confirmaciones</h2>
            @if(empty($timelineLabels))
                <p class="text-sm text-slate-500">
                    Aún no hay suficientes respuestas para mostrar la línea de tiempo.
                </p>
            @else
                <canvas id="lineChart" height="260"></canvas>
            @endif
        </div>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos desde backend
    const totalInvitados  = {{ $totalInvitados }};
    const confirmados     = {{ $confirmados }};
    const noAsisten       = {{ $noAsisten }};
    const pendientes      = {{ $pendientes }};

    const timelineLabels  = @json($timelineLabels);
    const timelineData    = @json($timelineData);

    // Pie chart: distribución
    const pieCtx = document.getElementById('pieChart')?.getContext('2d');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Confirmados', 'Pendientes', 'No asisten'],
                datasets: [{
                    data: [confirmados, pendientes, noAsisten],
                    backgroundColor: ['#0d9488', '#f97316', '#dc2626'],
                    borderWidth: 1,
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 11 } }
                    }
                },
                cutout: '60%',
            }
        });
    }

    // Line chart: evolución
    const lineCtx = document.getElementById('lineChart')?.getContext('2d');
    if (lineCtx && timelineLabels.length > 0) {
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: timelineLabels,
                datasets: [{
                    label: 'Respuestas por día',
                    data: timelineData,
                    tension: 0.3,
                    borderWidth: 2,
                    borderColor: '#4f46e5',
                    pointRadius: 3,
                    pointBackgroundColor: '#4f46e5',
                    fill: false,
                }]
            },
            options: {
                scales: {
                    x: {
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 11 } }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
</script>
@endsection
