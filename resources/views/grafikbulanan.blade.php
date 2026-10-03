<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafik Laba Rugi Bulanan - {{ $selectedYear }}</title>
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
        @media (max-width: 768px) {
            .sidebar { position: absolute; z-index: 100; width: 250px; transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
        }
        .rounded-circle-btn { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: #007bff; color: white; font-weight: bold; text-decoration: none; }
        .rounded-circle-btn:hover { background-color: #0056b3; color: white; }
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
                            <li class="breadcrumb-item"><a href="{{ route('rekap.bulanan') }}">Laporan Bulanan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Grafik Bulanan</li>
                        </ol>
                    </nav>
                    @include('layouts.user_dropdown')
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('rekap.bulanan', ['month' => date('n'), 'year' => $selectedYear]) }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Laporan Bulanan
                    </a>
                    <h2 class="h3 mb-0 text-center flex-grow-1">Grafik Laba Rugi Tahun {{ $selectedYear }}</h2>
                    <div style="width: 150px;"></div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="{{ route('rekap.bulanan.grafik') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="yearFilter" class="form-label">Pilih Tahun</label>
                                <select class="form-select" id="yearFilter" name="year">
                                    @for ($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                        <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Komparasi Laba Rugi Bulanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlyIncomeChart"></canvas>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mengambil data gabungan (Income, Expense, Profit) dari Controller
            const monthlyDataRaw = @json($monthlyData);
            const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

            const monthlyData = Object.values(monthlyDataRaw); 

            const incomeData = monthlyData.map(data => data.income);
            const expenseData = monthlyData.map(data => data.expense);
            const profitData = monthlyData.map(data => data.profit);

            const ctx = document.getElementById('monthlyIncomeChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Pemasukan (Rp)',
                        data: incomeData,
                        backgroundColor: 'rgba(0, 123, 255, 0.7)',
                        stack: 'Stack 0',
                        yAxisID: 'y'
                    }, {
                        label: 'Pengeluaran (Rp)',
                        data: expenseData,
                        backgroundColor: 'rgba(231, 74, 59, 0.7)',
                        stack: 'Stack 0',
                        yAxisID: 'y'
                    }, {
                        type: 'line',
                        label: 'Laba Bersih (Rp)',
                        data: profitData,
                        borderColor: 'rgb(28, 200, 138)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y',
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Jumlah (Rp)' },
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
                        legend: { display: true },
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