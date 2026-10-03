<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian</title>
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
                        <li class="breadcrumb-item active" aria-current="page">Laporan Harian</li>
                    </ol>
                </nav>
                @include('layouts.user_dropdown')
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0 text-center flex-grow-1">Laporan Harian Penjualan & Biaya</h2>
                <div style="width: 150px;"></div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('rekap.harian') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="dateFilter" class="form-label">Pilih Tanggal</label>
                            <input type="date" class="form-control" id="dateFilter" name="date" value="{{ $selectedDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="perPage" class="form-label">Tampilkan per halaman</label>
                            <select name="per_page" id="perPage" class="form-select">
                                <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                        <div class="col-md-5 d-flex justify-content-end align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Filter Laporan
                            </button>
                            <a href="{{ route('rekap.harian.grafik', ['year' => date('Y', strtotime($selectedDate)), 'month' => date('n', strtotime($selectedDate))]) }}" class="btn btn-info">
                                <i class="fas fa-chart-line me-1"></i> Show Grafik
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Ringkasan Metrics --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title text-success"><i class="fas fa-money-bill-wave me-2"></i> Total Pemasukan:</h5>
                            <p class="card-text fs-4 fw-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                            <p class="text-muted mb-0">Dari {{ $transactionCount }} transaksi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title text-danger"><i class="fas fa-minus-circle me-2"></i> Total Pengeluaran:</h5>
                            <p class="card-text fs-4 fw-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                            <p class="text-muted mb-0">Dari {{ $expenses->count() }} catatan biaya.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm h-100" style="border-left: 0.25rem solid {{ $netProfit >= 0 ? '#1cc88a' : '#e74a3b' }} !important;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: {{ $netProfit >= 0 ? '#1cc88a' : '#e74a3b' }} !important;"><i class="fas fa-receipt me-2"></i> Laba Bersih:</h5>
                            <p class="card-text fs-4 fw-bold">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
                            <p class="text-muted mb-0">Laba Bersih Harian</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><i class="fas fa-calendar-alt me-2"></i> Tanggal:</h5>
                            <p class="card-text fs-4 fw-bold">{{ date('d F Y', strtotime($selectedDate)) }}</p>
                            <p class="text-muted mb-0">Total Transaksi: {{ $transactionCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Detail Pengeluaran Harian</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Deskripsi</th>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                    <th>Dicatat Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expenses as $expense)
                                    <tr>
                                        <td>{{ date('H:i', strtotime($expense->expense_date)) }}</td>
                                        <td>{{ $expense->description }}</td>
                                        <td><span class="badge bg-danger">{{ $expense->category->name ?? 'N/A' }}</span></td>
                                        <td class="text-danger">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                        <td>{{ $expense->user->name ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Tidak ada catatan pengeluaran.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Detail Transaksi Harian</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No. Pelanggan</th>
                                    <th>No. Meja</th> 
                                    <th>Waktu</th>
                                    <th>Total</th>
                                    <th>Detail Item</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->customer_number ?? '-' }}</td>
                                        <td class="fw-bold text-primary">{{ $transaction->nomor_meja ?? '-' }}</td>
                                        <td>{{ date('H:i', strtotime($transaction->transaction_date)) }}</td>
                                        <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                        <td>
                                            <ul class="list-unstyled mb-0 small">
                                                @foreach ($transaction->details as $detail)
                                                    <li>{{ $detail->quantity }}x {{ $detail->menu->name ?? 'Menu tidak dikenal' }} (Rp {{ number_format($detail->subtotal, 0, ',', '.') }})</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Tidak ada transaksi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">
                            Menampilkan {{ $transactions->firstItem() }} hingga {{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi
                        </small>
                        {{ $transactions->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Menu Terlaris per Kategori (Hari Ini)</h5>
                </div>
                <div class="card-body">
                    @forelse ($topSellingMenusPerCategory as $topMenu)
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div><span class="fw-bold">{{ $topMenu['category_name'] }}:</span> {{ $topMenu['menu_name'] }}</div>
                            <span class="badge bg-primary rounded-pill">{{ $topMenu['total_quantity'] }} Terjual</span>
                        </div>
                    @empty
                        <p class="text-center text-muted">Belum ada data menu terlaris.</p>
                    @endforelse
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>