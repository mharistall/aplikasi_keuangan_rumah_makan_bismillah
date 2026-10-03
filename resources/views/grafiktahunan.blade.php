<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafik Komparasi Antar Tahun (2024-2028)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f8fafc; }
        .card { border-radius: 0.75rem; }
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #1e3a8a, #1e40af); transition: all 0.3s; }
        .sidebar-link { transition: all 0.2s; color: white; padding: 1rem; display: flex; align-items: center; text-decoration: none; }
        .sidebar-link:hover { background-color: rgba(255, 255, 255, 0.1); border-left: 4px solid #fff; color: white; }
        .active-link { background-color: rgba(255, 255, 255, 0.2); border-left: 4px solid #fff; color: white; }
        .chart-container { position: relative; height: 60vh; width: 100%; }
        @media (min-width: 992px) { .chart-container { height: 70vh; } }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container-fluid">
        <div class="row">
            @include('layouts.sidebar')
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('rekap.tahunan') }}">Laporan Tahunan</a></li>
                            <li class="breadcrumb-item active">Grafik Komparasi Antar Tahun</li>
                        </ol>
                    </nav>
                    @include('layouts.user_dropdown')
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('rekap.tahunan') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Laporan Tahunan
                    </a>
                    <h2 class="h3 mb-0 text-center flex-grow-1">Grafik Komparasi Laba Rugi 2024 - 2028</h2>
                    <div style="width: 150px;"></div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Visualisasi Performa Tahunan Rumah Makan Bismillah</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="yearlyComparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const yearlyDataRaw = @json($yearlyData);
            const yearLabels = @json($years);

            const yearlyData = Object.values(yearlyDataRaw); 

            const incomeData = yearlyData.map(data => data.income);
            const expenseData = yearlyData.map(data => data.expense);
            const profitData = yearlyData.map(data => data.profit);

            const ctx = document.getElementById('yearlyComparisonChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: yearLabels,
                    datasets: [{
                        label: 'Total Pemasukan (Rp)',
                        data: incomeData,
                        backgroundColor: 'rgba(0, 123, 255, 0.7)',
                        stack: 'Stack 0',
                    }, {
                        label: 'Total Pengeluaran (Rp)',
                        data: expenseData,
                        backgroundColor: 'rgba(231, 74, 59, 0.7)',
                        stack: 'Stack 0',
                    }, {
                        type: 'line',
                        label: 'Total Laba Bersih (Rp)',
                        data: profitData,
                        borderColor: 'rgb(28, 200, 138)',
                        borderWidth: 4,
                        fill: false,
                        tension: 0.3,
                        pointRadius: 6,
                        pointBackgroundColor: 'rgb(28, 200, 138)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: {
                            stacked: true,
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>