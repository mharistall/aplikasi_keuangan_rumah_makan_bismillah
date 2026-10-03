<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard RM Bismillah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #1e3a8a, #1e40af); transition: all 0.3s; }
        .sidebar-link { transition: all 0.2s; color: white; padding: 1rem; display: flex; align-items: center; text-decoration: none; }
        .sidebar-link:hover { background-color: rgba(255, 255, 255, 0.1); border-left: 4px solid #fff; color: white; }
        .active-link { background-color: rgba(255, 255, 255, 0.2); border-left: 4px solid #fff; color: white; }
        @media (max-width: 768px) {
            .sidebar { position: absolute; z-index: 100; width: 250px; transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
        }

        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .rounded-circle-btn { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: #007bff; color: white; font-weight: bold; text-decoration: none; }
        .rounded-circle-btn:hover { background-color: #0056b3; color: white; }
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
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                    @include('layouts.user_dropdown')
                </div>

                {{-- METRIK LABA RUGI --}}
                <div class="row mb-4">
                    {{-- Pendapatan Hari Ini --}}
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2 card-hover">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pendapatan Hari Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalIncomeToday, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-calendar fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pengeluaran Hari Ini --}}
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2 card-hover">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pengeluaran Hari Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalExpenseToday, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-minus-circle fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Laba Bersih Hari Ini --}}
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2 card-hover" style="border-left: 0.25rem solid {{ $netProfitToday >= 0 ? '#1cc88a' : '#e74a3b' }} !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: {{ $netProfitToday >= 0 ? '#1cc88a' : '#e74a3b' }} !important;">Laba Bersih Hari Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($netProfitToday, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-receipt fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Transaksi Hari Ini --}}
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2 card-hover">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Transaksi Hari Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $transactionCountToday }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Metrik Bulanan --}}
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2 card-hover">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pemasukan Bulan Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalIncomeMonth, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2 card-hover">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran Bulan Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalExpenseMonth, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-money-bill-wave fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2 card-hover" style="border-left: 0.25rem solid {{ $netProfitMonth >= 0 ? '#1cc88a' : '#e74a3b' }} !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: {{ $netProfitMonth >= 0 ? '#1cc88a' : '#e74a3b' }} !important;">Laba Bersih Bulan Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($netProfitMonth, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-coins fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Laba Rugi Per Bulan ({{ $selectedYear }})</h6>

                        <form action="{{ route('dashboard') }}" method="GET" class="d-flex align-items-center">
                            <label for="yearFilter" class="form-label mb-0 me-2 small">Tahun</label>
                            <select class="form-select form-select-sm" id="yearFilter" name="year" onchange="this.form.submit()">
                                @php
                                    $currentYear = date('Y');
                                    $startYear = $currentYear - 2; // Mulai dari 2 tahun lalu
                                    $endYear = $currentYear + 1; // Sampai 1 tahun ke depan (termasuk 2026)
                                @endphp

                                @for ($i = $startYear; $i <= $endYear; $i++)
                                    <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </form>
                    </div>

                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="incomeChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">5 Transaksi Pemasukan Terakhir</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Pelanggan Ke</th>
                                        <th>Total</th>
                                        <th>Dicatat Oleh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latestTransactions as $transaction)
                                        <tr>
                                            <td>{{ date('d M Y, H:i', strtotime($transaction->transaction_date)) }}</td>
                                            <td>{{ $transaction->customer_number ?? '-' }}</td>
                                            <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                            <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Belum ada transaksi terakhir.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
            const monthlyData = @json($monthlyData);
            const labels = Object.keys(monthlyData);
            const incomeData = labels.map(key => monthlyData[key].income);
            const expenseData = labels.map(key => monthlyData[key].expense);
            const profitData = labels.map(key => monthlyData[key].profit);

            const ctx = document.getElementById('incomeChart').getContext('2d');

            // Grafik Laba Rugi Komparatif
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pemasukan (Rp)',
                        data: incomeData,
                        backgroundColor: 'rgba(78, 115, 223, 0.7)',
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
                        borderColor: 'rgb(28, 200, 138)', // Hijau terang
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y',
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
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