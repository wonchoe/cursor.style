<div class="row mb-3">
    <div class="col-md-3">
        <label>Start Date</label>
        <input type="date" id="start_date" class="form-control">
    </div>
    <div class="col-md-3">
        <label>End Date</label>
        <input type="date" id="end_date" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Quick Range</label>
        <select id="range_selector" class="form-control">
            <option value="">Select...</option>
            <option value="7">Last 7 Days</option>
            <option value="14">Last 14 Days</option>
            <option value="30">Last 30 Days</option>
            <option value="90">Last 90 Days</option>
            <option value="365">Last 1 Year</option>
            <option value="all">All Available</option>
        </select>
    </div>
</div>

<div class="card">
    <div class="card-body" style="height: 350px;">
        <canvas id="chart"></canvas>
    </div>
</div>


@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chart;
    let hiddenProjects = JSON.parse(localStorage.getItem('hidden_projects') || '[]');
    const ctx = document.getElementById('chart').getContext('2d');

    async function loadChart() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;

        const url = `/report-chart-data?start_date=${start}&end_date=${end}`;
        const response = await fetch(url);
        const raw = await response.json();

        const labels = [...new Set(Object.values(raw).flat().map(d => d.date))];

        const colorPalette = ['#3b8bba', '#f39c12', '#00a65a', '#dd4b39', '#605ca8', '#d81b60', '#001f3f', '#39cccc'];
        const datasets = Object.keys(raw).map((project, i) => {
            const color = colorPalette[i % colorPalette.length];
            const label = project.replaceAll('_', ' ');
            const data = labels.map(label => {
                const found = raw[project].find(d => d.date === label);
                return found ? found.installs : 0;
            });

            return {
                label,
                data,
                fill: true,
                tension: 0.4,
                borderColor: color,
                backgroundColor: color + '55',
                pointRadius: 3,
                pointHoverRadius: 6,
                hidden: hiddenProjects.includes(project) // ← keeps visibility
            };
        });

        if (chart) chart.destroy();
        chart = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { boxWidth: 20, padding: 15 },
                        onClick: function (e, legendItem, legend) {
                            const index = legendItem.datasetIndex;
                            const ci = legend.chart;
                            const datasetLabel = ci.data.datasets[index].label.toLowerCase().replaceAll(' ', '_');

                            const meta = ci.getDatasetMeta(index);
                            meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;
                            ci.update();

                            if (meta.hidden) {
                                if (!hiddenProjects.includes(datasetLabel)) hiddenProjects.push(datasetLabel);
                            } else {
                                hiddenProjects = hiddenProjects.filter(p => p !== datasetLabel);
                            }
                            localStorage.setItem('hidden_projects', JSON.stringify(hiddenProjects));
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 500 }
                    }
                }
            }
        });
    }

    function setDateRangeFromSelector(days) {
        const today = new Date(); // today included
        let start;

        if (days === 'all') {
            document.getElementById('start_date').value = '2000-01-01';
            document.getElementById('end_date').value = today.toISOString().split('T')[0];
            return;
        }

        start = new Date(today);
        start.setDate(today.getDate() - parseInt(days) + 1); // +1 to include today

        document.getElementById('start_date').value = start.toISOString().split('T')[0];
        document.getElementById('end_date').value = today.toISOString().split('T')[0];
    }

    document.getElementById('range_selector').addEventListener('change', function () {
        const value = this.value;
        if (value) {
            localStorage.setItem('preferred_range', value);
            setDateRangeFromSelector(value);
            loadChart();
        }
    });

    document.getElementById('start_date').addEventListener('change', loadChart);
    document.getElementById('end_date').addEventListener('change', loadChart);

    window.addEventListener('DOMContentLoaded', () => {
        const savedRange = localStorage.getItem('preferred_range') || '7';
        document.getElementById('range_selector').value = savedRange;
        setDateRangeFromSelector(savedRange);
        loadChart();
    });
</script>
@endpush
