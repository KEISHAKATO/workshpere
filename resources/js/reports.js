import axios from 'axios';
import Chart from 'chart.js/auto';

const $ = (sel) => document.querySelector(sel);

const baseOptions = {
  responsive: true,
  maintainAspectRatio: false,  // fill the .chart-box height
  animation: false,            // avoid first-draw jump
  resizeDelay: 120,            // throttle resize to reduce jitter
  plugins: {
    legend: { display: true },
    tooltip: { enabled: true }
  },
  scales: {
    x: { ticks: { autoSkip: true, maxRotation: 0 } },
    y: { beginAtZero: true, ticks: { precision: 0 } }
  }
};

function renderBar(el, labels, data, label) {
  const ctx = el.getContext('2d');
  return new Chart(ctx, {
    type: 'bar',
    data: { labels, datasets: [{ label, data }] },
    options: baseOptions
  });
}

function renderPie(el, labels, data, label) {
  const ctx = el.getContext('2d');
  return new Chart(ctx, {
    type: 'pie',
    data: { labels, datasets: [{ label, data }] },
    options: { ...baseOptions, scales: undefined }
  });
}

async function boot() {
  try {
    const { data } = await axios.get('/admin/reports/data');

    // KPIs
    $('#kpi-acceptance').textContent = data.kpis.acceptanceRate ?? '0';
    $('#kpi-acc').textContent        = data.kpis.accepted ?? '0';
    $('#kpi-pend').textContent       = data.kpis.pending ?? '0';

    // Charts
    renderBar($('#chart-jobs-category'),
      data.charts.jobsByCategory.labels,
      data.charts.jobsByCategory.data,
      'Jobs'
    );

    renderBar($('#chart-jobs-county'),
      data.charts.jobsByCounty.labels,
      data.charts.jobsByCounty.data,
      'Jobs'
    );

    renderPie($('#chart-apps-status'),
      data.charts.appsByStatus.labels,
      data.charts.appsByStatus.data,
      'Applications'
    );

    new Chart($('#chart-skills').getContext('2d'), {
      type: 'bar',
      data: {
        labels: data.charts.skillsDemandVsSupply.labels,
        datasets: [
          { label: 'Demand (Jobs)',    data: data.charts.skillsDemandVsSupply.demand },
          { label: 'Supply (Profiles)', data: data.charts.skillsDemandVsSupply.supply }
        ]
      },
      options: baseOptions
    });

  } catch (e) {
    console.error('Failed to load reports data:', e);
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot);
} else {
  boot();
}
