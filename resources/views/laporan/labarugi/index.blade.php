<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #1e3a8a, #1e40af); transition: all 0.3s; }
        .sidebar-link { transition: all 0.2s; color: white; padding: 1rem; display: flex; align-items: center; text-decoration: none; }
        .sidebar-link:hover, .active-link { background-color: rgba(255, 255, 255, 0.15); border-left: 4px solid #fff; color: white; }
        .rounded-circle-btn { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: #007bff; color: white; font-weight: bold; text-decoration: none; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .chart-container { position: relative; height: 50vh; width: 100%; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container-fluid">
        <div class="row">
            {{-- SIDEBAR --}}
            @include('layouts.sidebar')

            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Laporan Laba Rugi</li>
                        </ol>
                    </nav>
                    @include('layouts.user_dropdown')
                </div>

                <h2 class="h3 mb-4 text-primary"><i class="fas fa-chart-line me-2"></i> Laporan Laba Rugi Bulanan</h2>

                {{-- FILTER TAHUN DAN BULAN --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="{{ route('laba-rugi.index') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="monthFilter" class="form-label">Pilih Bulan</label>
                                <select class="form-select" id="monthFilter" name="month">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
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
                            <div class="col-md-4 d-flex justify-content-end align-items-end">
                                <a href="{{ route('laba-rugi.export.pdf', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" class="btn btn-danger me-2">
                                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                                </a>
                                <a href="{{ route('laba-rugi.export.excel', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" class="btn btn-success">
                                    <i class="fas fa-file-excel me-1"></i> Export Excel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- RINGKASAN METRIK BULAN INI --}}
                <div class="row mb-4">
                    <div class="col-md-4 mb-4">
                        <div class="card border-left-success shadow h-100 py-2 card-hover">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pemasukan ({{ date('F Y', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear)) }})</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($summary['totalIncome'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2 card-hover">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran ({{ date('F Y', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear)) }})</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($summary['totalExpense'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2 card-hover" style="border-left: 0.25rem solid {{ $summary['netProfit'] >= 0 ? '#1cc88a' : '#e74a3b' }} !important;">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="color: {{ $summary['netProfit'] >= 0 ? '#1cc88a' : '#e74a3b' }} !important;">
                                    Laba Bersih ({{ date('F Y', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear)) }})
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($summary['netProfit'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- GRAFIK LABA RUGI TAHUNAN --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Komparasi Pemasukan, Pengeluaran, dan Laba Bersih ({{ $selectedYear }})</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="profitLossChart"></canvas>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthlyData = @json($monthlyData);
            const labels = Object.keys(monthlyData);
            const incomeData = labels.map(key => monthlyData[key].income);
            const expenseData = labels.map(key => monthlyData[key].expense);
            const profitData = labels.map(key => monthlyData[key].profit);

            const ctx = document.getElementById('profitLossChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pemasukan',
                        data: incomeData,
                        backgroundColor: 'rgba(28, 200, 138, 0.7)', // (Success)
                        stack: 'Stack 0',
                        yAxisID: 'y'
                    }, {
                        label: 'Pengeluaran',
                        data: expenseData,
                        backgroundColor: 'rgba(231, 74, 59, 0.7)', // (Danger)
                        stack: 'Stack 0',
                        yAxisID: 'y'
                    }, {
                        type: 'line',
                        label: 'Laba Bersih',
                        data: profitData,
                        borderColor: 'rgb(78, 115, 223)', // Primary
                        borderWidth: 3,
                        fill: false,
                        tension: 0.3,
                        yAxisID: 'y',
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah (Rp)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>