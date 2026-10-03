<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .food-card { transition: all 0.3s ease; cursor: pointer; }
        .food-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .receipt { background: linear-gradient(to bottom, #ffffff, #ffffff); border-left: 4px solid #4CAF50; }
        .quantity-btn { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
        .sticky-top { position: sticky; top: 1rem; align-self: flex-start; }
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #1e3a8a, #1e40af); transition: all 0.3s; }
        .sidebar-link { transition: all 0.2s; color: rgb(255, 255, 255); padding: 1rem; display: flex; align-items: center; text-decoration: none; }
        .active-link { background-color: rgba(255, 255, 255, 0.2); border-left: 4px solid #fff; color: white; }
    </style>
</head>
<body class="bg-gray-50">
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')

        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active" aria-current="page">Input Transaksi</li>
                    </ol>
                </nav>
                @include('layouts.user_dropdown')
            </div>

            <h1 class="h3 mb-4 text-primary"><i class="fas fa-cash-register me-2"></i> Input Transaksi</h1>

            <div class="row g-4 mt-1">
                {{-- Bagian Kiri: Pilih Menu --}}
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <h5 class="fw-bold text-gray-700 mb-3"><i class="fas fa-user-circle me-2 text-primary"></i> Informasi Pelanggan</h5>
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small text-uppercase">Nomor Pelanggan</label>
                                        <input type="text" class="form-control bg-light" id="customerNumber" value="{{ $nomor_pelanggan ?? '' }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small text-uppercase">Nomor Meja (Opsional)</label>
                                        <select class="form-select" id="tableNumber">
                                            <option value="">-- Tanpa Meja (Bungkus) --</option>
                                            @for ($i = 1; $i <= 6; $i++)
                                                <option value="Meja {{ $i }}">Meja {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            @foreach($categories as $category)
                                <div class="mb-4 menu-category-section" data-category-id="{{ $category->id }}">
                                    <h5 class="fw-bold text-gray-700 mb-3"><i class="fas fa-utensils me-2 text-primary"></i> {{ $category->name }}</h5>
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                        @php $currentCategoryMenus = $groupedMenus->get($category->id) ?? collect(); @endphp
                                        @foreach($currentCategoryMenus as $menu)
                                            <div class="col menu-item">
                                                <div class="card h-100 shadow-sm border-0 food-card">
                                                    <img src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('images/default-menu.png') }}" class="card-img-top" style="height: 120px; object-fit: cover;">
                                                    <div class="card-body d-flex flex-column">
                                                        <h6 class="card-title fw-bold mb-1" style="font-size: 0.9rem;">{{ $menu->name }}</h6>
                                                        <p class="card-text fw-bold text-success mt-auto mb-2 small">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                                        <button class="btn btn-primary btn-sm add-to-cart-btn"
                                                            data-menu-id="{{ $menu->id }}"
                                                            data-name="{{ $menu->name }}"
                                                            data-price="{{ $menu->price }}">
                                                            <i class="fas fa-plus me-1"></i> Tambah
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Bagian Kanan: Ringkasan Pesanan --}}
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 sticky-top">
                        <div class="card-body p-4 receipt">
                            <h5 class="fw-bold text-white-700 mb-3"><i class="fas fa-receipt me-2 text-primary"></i> Ringkasan Pesanan</h5>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Pilih Kategori Menu</label>
                                <select class="form-select form-select-sm" id="filterKategori">
                                    <option value="">Tampilkan Semua Menu</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="small mb-3">
                                <div class="d-flex justify-content-between"><span>No. Pelanggan:</span><span class="fw-bold">{{ $nomor_pelanggan ?? '-' }}</span></div>
                                <div class="d-flex justify-content-between"><span>Nomor Meja:</span><span id="displayTable" class="fw-bold text-primary">-</span></div>
                                <div class="d-flex justify-content-between"><span>Waktu:</span><span class="fw-bold">{{ date('d M Y, H:i') }}</span></div>
                            </div>

                            <hr>
                            <ul id="cartItems" class="list-group list-group-flush mb-3">
                                <p class="text-muted text-center my-4 small">Belum ada pesanan</p>
                            </ul>
                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="h6 fw-bold mb-0">Total Tagihan:</span>
                                <span id="totalAmount" class="h5 fw-bold text-primary mb-0">Rp 0</span>
                            </div>

                            <form id="orderForm" method="POST" action="{{ route('transactions.store') }}">
                                @csrf
                                <input type="hidden" name="nomor_pelanggan" value="{{ $nomor_pelanggan }}">
                                <input type="hidden" name="nomor_meja" id="formTableNumber">
                                <input type="hidden" name="order_data" id="orderDataInput">
                                <button id="submitOrder" class="btn btn-primary w-100 py-2 shadow-sm" type="submit">
                                    <i class="fas fa-check-circle me-2"></i> Simpan Transaksi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="modal-title mb-3 fw-bold">Transaksi Berhasil!</h5>
                <p class="text-muted mb-4">Pesanan telah berhasil disimpan ke sistem.</p>
                <button type="button" class="btn btn-primary px-5" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pemicu Modal Sukses
        @if(session('success'))
            const sModal = new bootstrap.Modal(document.getElementById('successModal'));
            sModal.show();
        @endif

        const tableSelect = document.getElementById('tableNumber');
        const displayTable = document.getElementById('displayTable');
        const formTableInput = document.getElementById('formTableNumber');
        const orderItemsContainer = document.getElementById('cartItems');
        const totalAmountSpan = document.getElementById('totalAmount');
        const orderDataInput = document.getElementById('orderDataInput');
        const filterKategori = document.getElementById('filterKategori');

        let cart = [];

        // Logika Dropdown Meja
        tableSelect.addEventListener('change', function() {
            displayTable.textContent = this.value || '-';
            formTableInput.value = this.value;
        });

        // Logika Filter Kategori (KEMBALI ADA)
        filterKategori.addEventListener('change', function() {
            const catId = this.value;
            document.querySelectorAll('.menu-category-section').forEach(section => {
                section.style.display = (catId === '' || section.dataset.categoryId == catId) ? 'block' : 'none';
            });
        });

        // Tambah ke Keranjang
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.menuId;
                const name = this.dataset.name;
                const price = parseFloat(this.dataset.price);

                const exist = cart.find(i => i.menu_id == id);
                if (exist) {
                    exist.quantity++;
                    exist.subtotal = exist.quantity * exist.price;
                } else {
                    cart.push({ menu_id: parseInt(id), name, price, quantity: 1, subtotal: price });
                }
                renderCart();
            });
        });

        // Render Keranjang dengan Tombol +/- (KEMBALI ADA)
        function renderCart() {
            orderItemsContainer.innerHTML = '';
            let total = 0;
            if (cart.length === 0) {
                orderItemsContainer.innerHTML = '<p class="text-muted text-center my-4 small">Belum ada pesanan</p>';
            } else {
                cart.forEach((item, idx) => {
                    total += item.subtotal;
                    orderItemsContainer.innerHTML += `
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0 border-bottom">
                            <div class="small">
                                <div class="fw-bold">${item.name}</div>
                                <div class="text-muted">Rp ${item.price.toLocaleString('id-ID')} x ${item.quantity}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold small mb-1">Rp ${item.subtotal.toLocaleString('id-ID')}</div>
                                <div class="btn-group">
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-2" onclick="updateQty(${idx}, -1)">-</button>
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-2" onclick="updateQty(${idx}, 1)">+</button>
                                </div>
                            </div>
                        </li>`;
                });
            }
            totalAmountSpan.textContent = `Rp ${total.toLocaleString('id-ID')}`;
            orderDataInput.value = JSON.stringify(cart);
        }

        // Fungsi Update Kuantitas
        window.updateQty = (idx, val) => {
            cart[idx].quantity += val;
            if (cart[idx].quantity <= 0) {
                cart.splice(idx, 1);
            } else {
                cart[idx].subtotal = cart[idx].quantity * cart[idx].price;
            }
            renderCart();
        };

        document.getElementById('orderForm').addEventListener('submit', function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('Pilih menu terlebih dahulu!');
            }
        });
    });
</script>
</body>
</html>