<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            transition: all 0.3s;
        }

        .sidebar-link {
            transition: all 0.2s;
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .active-link {
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 4px solid #fff;
            color: white;
        }

        .rounded-circle-btn {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-decoration: none;
        }
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
                            <li class="breadcrumb-item">
                                <a
                                    href="{{ Auth::user()->role === 'admin' ? route('input.transaksi') : route('dashboard') }}">{{ Auth::user()->role === 'admin' ? 'Input Transaksi' : 'Dashboard' }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Menu</li>
                        </ol>
                    </nav>
                    @include('layouts.user_dropdown')
                </div>

                <h2 class="h3 mb-4 text-primary"><i class="fas fa-utensils me-2"></i> Edit Menu</h2>

                {{-- ALERT MESSAGES --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- CARD UTAMA --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Menu</h6>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#tambahMenuModal">
                            <i class="fas fa-plus me-1"></i> Tambah Menu
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Gambar</th>
                                        <th>
                                        Nama Menu 
                                            <a href="{{ route('menu.index', ['sort' => 'name', 'direction' => 'asc']) }}"><i class="fas fa-caret-up"></i></a>
                                            <a href="{{ route('menu.index', ['sort' => 'name', 'direction' => 'desc']) }}"><i class="fas fa-caret-down"></i></a>
                                        </th>
                                        <th>
                                        Kategori 
                                            <a href="{{ route('menu.index', ['sort' => 'kategori.name', 'direction' => 'asc']) }}"><i class="fas fa-caret-up"></i></a>
                                            <a href="{{ route('menu.index', ['sort' => 'kategori.name', 'direction' => 'desc']) }}"><i class="fas fa-caret-down"></i></a>
                                        </th>
                                        <th>
                                        Harga 
                                            <a href="{{ route('menu.index', ['sort' => 'price', 'direction' => 'asc']) }}"><i class="fas fa-caret-up"></i></a>
                                            <a href="{{ route('menu.index', ['sort' => 'price', 'direction' => 'desc']) }}"><i class="fas fa-caret-down"></i></a>
                                        </th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($menus as $menu)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($menu->image)
                                                    <img src="{{ asset('storage/' . $menu->image) }}"
                                                        alt="{{ $menu->name }}"
                                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                @else
                                                    <i class="fas fa-image text-muted" style="font-size: 24px;"></i>
                                                @endif
                                            </td>
                                            <td class="fw-bold">{{ $menu->name }}</td>
                                            <td><span
                                                    class="badge bg-success">{{ $menu->category->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm"
                                                    onclick="showEditModal({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->category_id }}, {{ $menu->price }}, '{{ $menu->image }}')">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form action="{{ route('menu.destroy', $menu) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus menu {{ $menu->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada menu yang tercatat.</td>
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

    {{-- MODAL TAMBAH MENU --}}
    <div class="modal fade" id="tambahMenuModal" tabindex="-1" aria-labelledby="tambahMenuModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('menu.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="tambahMenuModalLabel">Tambah Menu Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Menu</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Harga (Rp)</label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                id="price" name="price" value="{{ old('price') }}" required min="100">
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Gambar (Opsional)</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                id="image" name="image">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT MENU (Hidden) --}}
    <div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="editMenuModalLabel">Edit Menu</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Menu</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="edit_category_id" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Harga (Rp)</label>
                            <input type="number" class="form-control" id="edit_price" name="price" required
                                min="100">
                        </div>
                        <div class="mb-3">
                            <label for="edit_image" class="form-label">Ganti Gambar (Opsional)</label>
                            <input type="file" class="form-control" id="edit_image" name="image">
                            <small class="text-muted mt-1" id="current_image_info">Gambar saat ini: -</small>
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
        window.showEditModal = function(id, name, categoryId, price, imagePath) {
            const modal = new bootstrap.Modal(document.getElementById('editMenuModal'));

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_category_id').value = categoryId;
            document.getElementById('edit_price').value = price;

            // Update informasi gambar saat ini
            const imageInfo = document.getElementById('current_image_info');
            if (imagePath) {
                imageInfo.innerHTML =
                    `Gambar saat ini: <a href="{{ asset('storage') }}/${imagePath}" target="_blank">Lihat Gambar</a>`;
            } else {
                imageInfo.textContent = 'Gambar saat ini: Tidak ada';
            }

            // Set action form untuk PUT request
            const routeUrl = "{{ route('menu.update', ':id') }}";
            document.getElementById('editForm').action = routeUrl.replace(':id', id);

            modal.show();
        }

        // Logika untuk menampilkan modal Tambah jika ada error validasi
        @if ($errors->any() && !old('_method'))
            document.addEventListener('DOMContentLoaded', function() {
                const tambahModal = new bootstrap.Modal(document.getElementById('tambahMenuModal'));
                tambahModal.show();
            });
        @endif
    </script>
</body>

</html>
