<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Tahunan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8fafc; }
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #1e3a8a, #1e40af); }
        .sidebar-link { transition: all 0.2s; color: white; padding: 1rem; display: flex; align-items: center; text-decoration: none; }
        .sidebar-link:hover { background-color: rgba(255, 255, 255, 0.1); border-left: 4px solid #fff; }
        .active-link { background-color: rgba(255, 255, 255, 0.2); border-left: 4px solid #fff; color: white; }

    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            @include('layouts.sidebar')
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Laporan Tahunan</li>
                        </ol>
                    </nav>
                    @include('layouts.user_dropdown')
                </div>

                <h2 class="h3 mb-4 text-center">Laporan Tahunan Penjualan & Biaya ({{ $selectedYear }})</h2>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="{{ route('rekap.tahunan') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Pilih Tahun</label>
                                <select class="form-select" name="year">
                                    @for ($i = date('Y') - 5; $i <= date('Y') + 1; $i++)
                                        <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> Filter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-success">Total Pemasukan Tahunan:</h5>
                                <p class="card-text fs-4 fw-bold">Rp {{ number_format($totalYearlyIncome, 0, ',', '.') }}</p>
                                <p class="text-muted">Dari {{ $totalYearlyTransactions }} transaksi.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-danger">Total Pengeluaran Tahunan:</h5>
                                <p class="card-text fs-4 fw-bold">Rp {{ number_format($totalYearlyExpense, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 border-left-primary" style="border-left: 0.25rem solid {{ $netProfitYearly >= 0 ? '#1cc88a' : '#e74a3b' }} !important;">
                            <div class="card-body">
                                <h5 class="card-title" style="color: {{ $netProfitYearly >= 0 ? '#1cc88a' : '#e74a3b' }}">Laba Bersih Tahunan:</h5>
                                <p class="card-text fs-4 fw-bold">Rp {{ number_format($netProfitYearly, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light"><h5>Ringkasan Bulanan Tahun {{ $selectedYear }}</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Bulan</th>
                                        <th>Pemasukan</th>
                                        <th>Pengeluaran</th>
                                        <th>Laba/Rugi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monthlySummaries as $summary)
                                    <tr>
                                        <td>{{ $summary->month_name }}</td>
                                        <td class="text-success">Rp {{ number_format($summary->income, 0, ',', '.') }}</td>
                                        <td class="text-danger">Rp {{ number_format($summary->expense, 0, ',', '.') }}</td>
                                        <td class="fw-bold" style="color: {{ $summary->profit >= 0 ? '#1cc88a' : '#e74a3b' }}">
                                            Rp {{ number_format($summary->profit, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Grafik Laba Rugi Tahunan</h5>
                        <a href="{{ route('rekap.tahunan.grafik', ['year' => $selectedYear]) }}" class="btn btn-info btn-sm text-white">
                            <i class="fas fa-chart-line me-1"></i> Lihat Grafik
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>