<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meninjau Pengeluaran - Pemilik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #1e3a8a, #1e40af); transition: all 0.3s; }
        .sidebar-link { transition: all 0.2s; color: white; padding: 1rem; display: flex; align-items: center; text-decoration: none; }
        .sidebar-link:hover, .active-link { background-color: rgba(255, 255, 255, 0.15); border-left: 4px solid #fff; color: white; }
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
                            <li class="breadcrumb-item active">Meninjau Pengeluaran</li>
                        </ol>
                    </nav>
                    @include('layouts.user_dropdown')
                </div>

                <h2 class="h3 mb-4 text-primary"><i class="fas fa-search-dollar me-2"></i>Meninjau Pengeluaran</h2>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form action="{{ route('pemilik.pengeluaran.index') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small">Pilih Tanggal</label>
                                <input type="date" name="date" class="form-control" value="{{ $selectedDate }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Cari Data</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-7">
                        <div class="card shadow">
                            <div class="card-header bg-white"><h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-2"></i>Persentase Pengeluaran Terbesar</h6></div>
                            <div class="card-body">
                                <div style="max-height: 300px;">
                                    <canvas id="expensePieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card shadow h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <p class="text-muted mb-1">Total Pengeluaran Hari Ini</p>
                                <h2 class="text-danger fw-bold">Rp {{ number_format($totalExpenseToday, 0, ',', '.') }}</h2>
                                <hr class="w-75">
                                <p class="small text-center text-muted fst-italic">Item barang yang sama akan otomatis tergabung dalam satu kategori grafik untuk memudahkan peninjauan volume belanja harian.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header bg-white"><h6 class="m-0 font-weight-bold text-primary">Data Pengeluaran Terkini</h6></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Deskripsi</th>
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenses as $expense)
                                        <tr>
                                            <td>{{ date('d/m/Y', strtotime($expense->expense_date)) }}</td>
                                            <td>{{ $expense->description }}</td>
                                            <td><span class="badge bg-secondary">{{ $expense->category->name ?? 'N/A' }}</span></td>
                                            <td class="text-danger fw-bold">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                            <td><span class="badge bg-success">Tercatat</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data pengeluaran.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $expenses->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('expensePieChart').getContext('2d');
            const labels = {!! json_encode($labels) !!};
            const dataValues = {!! json_encode($totals) !!};

            if(labels.length > 0) {
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataValues,
                            backgroundColor: [
                                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
                                '#fd7e14', '#6f42c1', '#20c997', '#858796', '#5a5c69'
                            ],
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.parsed;
                                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        let percentage = ((value / total) * 100).toFixed(1) + "%";
                                        return `${label}: Rp ${value.toLocaleString('id-ID')} (${percentage})`;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                ctx.font = "14px Arial";
                ctx.textAlign = "center";
                ctx.fillText("Tidak ada data untuk grafik", 150, 150);
            }
        });
    </script>
</body>
</html>