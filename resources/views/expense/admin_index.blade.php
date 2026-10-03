<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pengeluaran Harian - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #1e3a8a, #1e40af); transition: all 0.3s; }
        .sidebar-link { transition: all 0.2s; color: white; padding: 1rem; display: flex; align-items: center; text-decoration: none; }
        .sidebar-link:hover, .active-link { background-color: rgba(255, 255, 255, 0.15); border-left: 4px solid #fff; color: white; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
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
                            <li class="breadcrumb-item"><a href="{{ route('input.transaksi') }}">Input Transaksi</a></li>
                            <li class="breadcrumb-item active">Input Pengeluaran Harian</li>
                        </ol>
                    </nav>
                    @include('layouts.user_dropdown')
                </div>

                <h2 class="h3 mb-4 text-primary"><i class="fas fa-hand-holding-usd me-2"></i> Input Pengeluaran Harian</h2>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2 card-hover">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran Hari Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalExpenseToday, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-money-bill-wave fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Pengeluaran</h6>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#masterItemModal">
                                <i class="fas fa-list me-1"></i> Tambah Deskripsi
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
                                <i class="fas fa-plus me-1"></i> Tambah Pengeluaran
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.pengeluaran.index') }}" method="GET" class="row g-3 align-items-end mb-4">
                            <div class="col-md-3">
                                <label class="form-label small">Pilih Tanggal</label>
                                <input type="date" class="form-control" name="date" value="{{ $selectedDate }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter Data</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Deskripsi</th>
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenses as $expense)
                                        <tr>
                                            <td>{{ date('d M Y', strtotime($expense->expense_date)) }}</td>
                                            <td>{{ $expense->description }}</td>
                                            <td><span class="badge bg-danger">{{ $expense->category->name ?? 'N/A' }}</span></td>
                                            <td class="fw-bold text-danger">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" onclick="edit({{ $expense->id }}, '{{ $expense->expense_date }}', '{{ $expense->category_id }}', '{{ $expense->description }}', {{ $expense->amount }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('admin.pengeluaran.destroy', $expense) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada data.</td>
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

<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.pengeluaran.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5>Tambah Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Tanggal</label><input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi Barang</label>
                    <input list="barang_list" name="description" id="item_desc" class="form-control" placeholder="Pilih atau Ketik Nama Barang..." required autocomplete="off">
                    <datalist id="barang_list">
                        @foreach($masterItems as $item)
                            <option data-unit="{{ $item->default_unit }}" value="{{ $item->name }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jumlah/Berat</label>
                        <input type="number" name="quantity" class="form-control" placeholder="Ex: 5" required min="0.1" step="0.1">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Satuan</label>
                        <select name="unit" id="item_unit" class="form-select" required>
                            <option value="">Pilih Satuan</option>
                            <option value="KG">KG</option>
                            <option value="Liter">Liter</option>
                            <option value="Butir">Butir</option>
                            <option value="Tabung">Tabung</option>
                            <option value="Pcs">Pcs</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3"><label class="form-label">Total Harga (Rp)</label><input type="number" name="amount" class="form-control" required></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan Pengeluaran</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="masterItemModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.master-item.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5>Master Deskripsi Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Daftarkan nama barang agar muncul otomatis di pilihan pengeluaran.</p>
                <div class="mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Beras Ramos" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Satuan Standar</label>
                    <select name="default_unit" class="form-select" required>
                        <option value="KG">KG</option>
                        <option value="Liter">Liter</option>
                        <option value="Butir">Butir</option>
                        <option value="Tabung">Tabung</option>
                        <option value="Pcs">Pcs</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-success">Daftarkan Barang</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header bg-warning text-dark">
                <h5>Edit Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Tanggal</label><input type="date" id="e_date" name="expense_date" class="form-control" required></div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select id="e_category" name="category_id" class="form-select" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Deskripsi</label><input type="text" id="e_desc" name="description" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Jumlah (Rp)</label><input type="number" id="e_amount" name="amount" class="form-control" required></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-warning text-dark">Update</button></div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('item_desc').addEventListener('input', function(e) {
        const val = e.target.value;
        const list = document.getElementById('barang_list');
        const opts = list.getElementsByTagName('option');
        for (let i = 0; i < opts.length; i++) {
            if (opts[i].value === val) {
                document.getElementById('item_unit').value = opts[i].getAttribute('data-unit');
                break;
            }
        }
    });

    function edit(id, date, cat, desc, amount) {
        document.getElementById('editForm').action = "/admin/kelola-pengeluaran/" + id;
        document.getElementById('e_date').value = date;
        document.getElementById('e_category').value = cat;
        document.getElementById('e_desc').value = desc;
        document.getElementById('e_amount').value = amount;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>
</body>
</html>