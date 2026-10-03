<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8fafc; }
        .card { border-radius: 0.75rem; }
        .table th, .table td { vertical-align: middle; }
        .table thead th { background-color: #e9ecef; }
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
                            <li class="breadcrumb-item active" aria-current="page">Laporan Bulanan</li>
                        </ol>
                    </nav>
                    @include('layouts.user_dropdown')
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h3 mb-0 text-center flex-grow-1">Laporan Bulanan Penjualan & Biaya</h2>
                    <div style="width: 150px;"></div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="{{ route('rekap.bulanan') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
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
                            <div class="col-md-3">
                                <label for="perPage" class="form-label">Tampilkan per halaman</label>
                                <select name="per_page" id="perPage" class="form-select">
                                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="{{ $dailySummaries->total() }}" {{ $perPage == $dailySummaries->total() ? 'selected' : '' }}>Semua</option>
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

                {{-- CARD RINGKASAN --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-success"><i class="fas fa-money-bill-wave me-2"></i> Total Pemasukan:</h5>
                                <p class="card-text fs-4 fw-bold">Rp {{ number_format($totalMonthlyIncome, 0, ',', '.') }}</p>
                                <p class="text-muted mb-0">Dari {{ $totalMonthlyTransactions }} transaksi.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-danger"><i class="fas fa-minus-circle me-2"></i> Total Pengeluaran:</h5>
                                <p class="card-text fs-4 fw-bold">Rp {{ number_format($totalMonthlyExpense, 0, ',', '.') }}</p>
                                <p class="text-muted mb-0">Total biaya operasional dan bahan baku.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 border-left-primary" style="border-left: 0.25rem solid {{ $netProfitMonthly >= 0 ? '#1cc88a' : '#e74a3b' }} !important;">
                            <div class="card-body">
                                <h5 class="card-title" style="color: {{ $netProfitMonthly >= 0 ? '#1cc88a' : '#e74a3b' }} !important;"><i class="fas fa-receipt me-2"></i> Laba Bersih:</h5>
                                <p class="card-text fs-4 fw-bold">Rp {{ number_format($netProfitMonthly, 0, ',', '.') }}</p>
                                <p class="text-muted mb-0">Laba Bersih Bulanan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Ringkasan Pemasukan & Pengeluaran Harian Bulan Ini</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Total Pemasukan</th>
                                        <th>Total Pengeluaran</th>
                                        <th>Laba Harian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($combinedSummaries as $summary)
                                        <tr>
                                            <td>{{ date('d F Y', strtotime($summary->transaction_date_only)) }}</td>
                                            <td class="text-success">Rp {{ number_format($summary->total_daily_income, 0, ',', '.') }}</td>
                                            <td class="text-danger">Rp {{ number_format($summary->total_daily_expense, 0, ',', '.') }}</td>
                                            <td class="fw-bold" style="color: {{ ($summary->total_daily_income - $summary->total_daily_expense) >= 0 ? '#1cc88a' : '#e74a3b' }} !important;">
                                                Rp {{ number_format($summary->total_daily_income - $summary->total_daily_expense, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada ringkasan transaksi untuk bulan ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                Menampilkan {{ $dailySummaries->firstItem() }} hingga {{ $dailySummaries->lastItem() }} dari {{ $dailySummaries->total() }} ringkasan harian
                            </small>
                            {{ $dailySummaries->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Menu Terlaris per Kategori (Bulan Ini)</h5>
                    </div>
                    <div class="card-body">
                        @forelse ($topSellingMenusPerCategory as $topMenu)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <span class="fw-bold">{{ $topMenu['category_name'] }}:</span> {{ $topMenu['menu_name'] }}
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $topMenu['total_quantity'] }} Terjual</span>
                            </div>
                        @empty
                            <p class="text-center text-muted">Belum ada data menu terlaris untuk bulan ini.</p>
                        @endforelse
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Grafik Pendapatan Bulanan (Tahun Ini)</h5>
                        <a href="{{ route('rekap.bulanan.grafik', ['year' => $selectedYear]) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-line me-1"></i> Show Grafik
                        </a>
                    </div>
                    <div class="card-body text-center text-muted">
                        <p>Klik "Show Grafik" untuk melihat grafik pendapatan bulanan secara detail.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>