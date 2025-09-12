<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl">Reports &amp; Analytics</h2>
                <p class="text-sm opacity-70 mt-1">Jobs, applications, and skills insights</p>
            </div>
        </div>
    </x-slot>

    <script> if ('scrollRestoration' in history) { history.scrollRestoration = 'manual'; } </script>

    <style>
        .chart-card { position: relative; }
        .chart-box  { position: relative; height: 260px; }
        @media (min-width: 768px) { .chart-box { height: 300px; } }
        @media (min-width: 1024px){ .chart-box { height: 320px; } }
    </style>

    <div class="max-w-7xl mx-auto p-4 space-y-8">
        {{-- KPIs --}}
        <div id="kpis" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="opacity-70 text-sm">Acceptance Rate</div>
                    <div class="text-3xl font-bold mt-1"><span id="kpi-acceptance">—</span>%</div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="opacity-70 text-sm">Applications (Accepted)</div>
                    <div class="text-3xl font-bold mt-1"><span id="kpi-acc">—</span></div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="opacity-70 text-sm">Applications (Pending)</div>
                    <div class="text-3xl font-bold mt-1"><span id="kpi-pend">—</span></div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card bg-base-100 shadow-xl chart-card">
                <div class="card-body">
                    <h3 class="font-semibold">Jobs by Category</h3>
                    <div class="chart-box"><canvas id="chart-jobs-category"></canvas></div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-xl chart-card">
                <div class="card-body">
                    <h3 class="font-semibold">Jobs by County (Top 12)</h3>
                    <div class="chart-box"><canvas id="chart-jobs-county"></canvas></div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-xl chart-card">
                <div class="card-body">
                    <h3 class="font-semibold">Applications Status</h3>
                    <div class="chart-box"><canvas id="chart-apps-status"></canvas></div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-xl chart-card">
                <div class="card-body">
                    <h3 class="font-semibold">Skills: In-Demand vs Available (Top 12)</h3>
                    <div class="chart-box"><canvas id="chart-skills"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/reports.js')
</x-app-layout>
