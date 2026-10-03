<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Grafik Pendapatan Harian - Tahun {{ $year ?? date('Y') }}</title>

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
                            <li class="breadcrumb-item"><a href="{{ route('rekap.harian') }}">Laporan Harian</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $month ? 'Grafik Harian' : 'Grafik Bulanan' }}</li>
                        </ol>
                    </nav>
                    @include('layouts.user_dropdown')
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('rekap.harian', ['date' => date('Y-m-d', mktime(0, 0, 0, 1, 1, $year ?? date('Y')))]) }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Laporan Harian
                    </a>
                    <h2 class="h3 mb-0 text-center flex-grow-1">{{ $chartTitle }}</h2>
                    <div style="width: 150px;"></div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="{{ route('rekap.harian.grafik') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="monthFilter" class="form-label">Pilih Bulan</label>
                                <select class="form-select" id="monthFilter" name="month">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ ($month ?? date('m')) == $m ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="yearFilter" class="form-label">Pilih Tahun</label>
                                <select class="form-select" id="yearFilter" name="year">
                                    @php
                                        $currentYear = date('Y');
                                    @endphp
                                    @for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++)
                                        <option value="{{ $i }}" {{ ($year ?? $currentYear) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i> Tampilkan Grafik
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Perbandingan Pemasukan, Pengeluaran, dan Laba Bersih per Bulan</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlyLabaRugiChart"></canvas>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = @json($labels);
            const incomeData = @json($incomeData);
            const expenseData = @json($expenseData);
            const profitData = @json($profitData);

            const ctx = document.getElementById('monthlyLabaRugiChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pemasukan (Rp)',
                            data: incomeData,
                            backgroundColor: 'rgba(40, 167, 69, 0.6)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1,
                            stack: 'stack1'
                        },
                        {
                            label: 'Pengeluaran (Rp)',
                            data: expenseData,
                            backgroundColor: 'rgba(220, 53, 69, 0.6)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1,
                            stack: 'stack1'
                        },
                        {
                            label: 'Laba Bersih (Rp)',
                            data: profitData,
                            type: 'line',
                            backgroundColor: 'rgba(0, 123, 255, 0.8)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 3,
                            fill: false,
                            yAxisID: 'y'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Bulan'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
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
