<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports & Analytics</h2>
                <p class="text-sm text-gray-600 mt-1">Jobs, applications, and skills insights</p>
            </div>
        </div>
    </x-slot>

    {{-- Prevent browser from restoring scroll on navigation to this page --}}
    <script>
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
    </script>

    {{-- Keep charts from stretching + avoid layout shift --}}
    <style>
        .chart-card { position: relative; }
        .chart-box  { position: relative; height: 260px; }          /* default */
        @media (min-width: 768px) { .chart-box { height: 300px; } }  /* md+ */
        @media (min-width: 1024px){ .chart-box { height: 320px; } }  /* lg+ */
    </style>

    <div class="max-w-7xl mx-auto p-6 space-y-8">
        {{-- KPI strip --}}
        <div id="kpis" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow p-5">
                <div class="text-sm text-gray-500">Acceptance Rate</div>
                <div class="text-3xl font-bold mt-1"><span id="kpi-acceptance">—</span>%</div>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <div class="text-sm text-gray-500">Applications (Accepted)</div>
                <div class="text-3xl font-bold mt-1"><span id="kpi-acc">—</span></div>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <div class="text-sm text-gray-500">Applications (Pending)</div>
                <div class="text-3xl font-bold mt-1"><span id="kpi-pend">—</span></div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow p-5 chart-card">
                <h3 class="font-semibold mb-3">Jobs by Category</h3>
                <div class="chart-box">
                    <canvas id="chart-jobs-category" aria-label="Jobs by Category"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-5 chart-card">
                <h3 class="font-semibold mb-3">Jobs by County (Top 12)</h3>
                <div class="chart-box">
                    <canvas id="chart-jobs-county" aria-label="Jobs by County"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-5 chart-card">
                <h3 class="font-semibold mb-3">Applications Status</h3>
                <div class="chart-box">
                    <canvas id="chart-apps-status" aria-label="Applications Status"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-5 chart-card">
                <h3 class="font-semibold mb-3">Skills: In-Demand vs Available (Top 12)</h3>
                <div class="chart-box">
                    <canvas id="chart-skills" aria-label="Skills Demand vs Supply"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Page-scoped JS bundle --}}
    @vite('resources/js/reports.js')
</x-app-layout>
