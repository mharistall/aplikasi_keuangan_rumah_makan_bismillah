<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #1e3a8a, #1e40af); transition: all 0.3s; }
        .sidebar-link { transition: all 0.2s; color: white; padding: 1rem; display: flex; align-items: center; text-decoration: none; }
        .active-link { background-color: rgba(255, 255, 255, 0.2); border-left: 4px solid #fff; color: white; }
        .rounded-circle-btn { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: #007bff; color: white; font-weight: bold; text-decoration: none; }
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
                            <li class="breadcrumb-item active" aria-current="page">Master Kategori</li>
                        </ol>
                    </nav>
                    @include('layouts.user_dropdown')
                </div>

                <h2 class="h3 mb-4 text-primary"><i class="fas fa-list me-2"></i> Master Kategori</h2>

                {{-- ALERT MESSAGES --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- CARD UTAMA --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Kategori</h6>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahKategoriModal">
                            <i class="fas fa-plus me-1"></i> Tambah Kategori
                        </button>
                    </div>
                    <div class="card-body">

                        {{-- NAVIGASI TAB --}}
                        <ul class="nav nav-tabs mb-4" id="categoryTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="income-tab" data-bs-toggle="tab" data-bs-target="#income-categories" type="button" role="tab" aria-controls="income-categories" aria-selected="true">
                                    Kategori Pemasukan (Menu) <span class="badge bg-success">{{ $incomeCategories->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="expense-tab" data-bs-toggle="tab" data-bs-target="#expense-categories" type="button" role="tab" aria-controls="expense-categories" aria-selected="false">
                                    Kategori Pengeluaran (Biaya) <span class="badge bg-danger">{{ $expenseCategories->count() }}</span>
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="categoryTabsContent">

                            {{-- TAB KATEGORI PEMASUKAN --}}
                            <div class="tab-pane fade show active" id="income-categories" role="tabpanel" aria-labelledby="income-tab">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Kategori</th>
                                                <th>Tipe</th>
                                                <th>Jumlah Menu Terkait</th>
                                                <th style="width: 150px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($incomeCategories as $category)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="fw-bold">{{ $category->name }}</td>
                                                    <td><span class="badge bg-success">{{ ucfirst($category->type) }}</span></td>
                                                    <td>{{ $category->menus->count() }}</td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm" onclick="showEditModal({{ $category->id }}, '{{ $category->name }}', '{{ $category->type }}')">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <form action="{{ route('master.kategori.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $category->name }}? Pastikan tidak ada menu/pengeluaran yang terhubung!')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada kategori pemasukan (menu) yang tercatat.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- TAB KATEGORI PENGELUARAN --}}
                            <div class="tab-pane fade" id="expense-categories" role="tabpanel" aria-labelledby="expense-tab">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Kategori</th>
                                                <th>Tipe</th>
                                                <th>Jumlah Pengeluaran Terkait</th>
                                                <th style="width: 150px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($expenseCategories as $category)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="fw-bold">{{ $category->name }}</td>
                                                    <td><span class="badge bg-danger">{{ ucfirst($category->type) }}</span></td>
                                                    <td>{{ $category->expenses->count() }}</td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm" onclick="showEditModal({{ $category->id }}, '{{ $category->name }}', '{{ $category->type }}')">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <form action="{{ route('master.kategori.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $category->name }}? Pastikan tidak ada menu/pengeluaran yang terhubung!')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada kategori pengeluaran (biaya) yang tercatat.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    {{-- MODAL TAMBAH KATEGORI --}}
    <div class="modal fade" id="tambahKategoriModal" tabindex="-1" aria-labelledby="tambahKategoriModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('master.kategori.store') }}">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="tambahKategoriModalLabel">Tambah Kategori Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Tipe Kategori</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Pilih Tipe</option>
                                <option value="pemasukan" {{ old('type') == 'pemasukan' ? 'selected' : '' }}>Pemasukan (Menu)</option>
                                <option value="pengeluaran" {{ old('type') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran (Biaya)</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT KATEGORI --}}
    <div class="modal fade" id="editKategoriModal" tabindex="-1" aria-labelledby="editKategoriModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="editKategoriModalLabel">Edit Kategori</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_type" class="form-label">Tipe Kategori</label>
                            <select class="form-select" id="edit_type" name="type" required>
                                <option value="pemasukan">Pemasukan (Menu)</option>
                                <option value="pengeluaran">Pengeluaran (Biaya)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk menampilkan modal edit
        window.showEditModal = function(id, name, type) {
            const modal = new bootstrap.Modal(document.getElementById('editKategoriModal'));

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_type').value = type;

            // Set action form untuk PUT request
            const routeUrl = "{{ route('master.kategori.update', ':id') }}";
            document.getElementById('editForm').action = routeUrl.replace(':id', id);

            modal.show();
        }

        // Tampilkan modal tambah jika ada error validasi saat submit form tambah
        @if ($errors->any() && !old('_method'))
            document.addEventListener('DOMContentLoaded', function() {
                const tambahModal = new bootstrap.Modal(document.getElementById('tambahKategoriModal'));
                tambahModal.show();
            });
        @endif

        // Tampilkan modal edit jika ada error validasi saat submit form edit
        @if ($errors->any() && old('_method') === 'PUT')
            document.addEventListener('DOMContentLoaded', function() {
                const editModal = new bootstrap.Modal(document.getElementById('editKategoriModal'));
                editModal.show();
            });
        @endif
    </script>
</body>
</html>