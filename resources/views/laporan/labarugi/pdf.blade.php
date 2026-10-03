<!DOCTYPE html>
<html>
<head>
    <title>Laporan Laba Rugi {{ $monthName }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16pt; }
        .summary-box { border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; background-color: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #e9ecef; }
        .total-row td { font-weight: bold; background-color: #ffe0b2; } 
        .profit-row td { background-color: #ccffcc; font-weight: bold; }
        .loss-row td { background-color: #ffcccc; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN LABA RUGI</h1>
        <h2>Rumah Makan Bismillah</h2>
        <p>Periode: **{{ $monthName }}**</p>
    </div>

    <div class="summary-box">
        <h3>Ringkasan Keuangan</h3>
        <table>
            <tr>
                <th>Uraian</th>
                <th style="text-align: right;">Jumlah (Rp)</th>
            </tr>
            <tr>
                <td>Total Pemasukan (Penjualan)</td>
                <td style="text-align: right;">{{ number_format($summary['totalIncome'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran (Biaya)</td>
                <td style="text-align: right;">({{ number_format($summary['totalExpense'], 0, ',', '.') }})</td>
            </tr>
            <tr class="{{ $summary['netProfit'] >= 0 ? 'profit-row' : 'loss-row' }}">
                <td>**LABA BERSIH**</td>
                <td style="text-align: right;">**{{ number_format($summary['netProfit'], 0, ',', '.') }}**</td>
            </tr>
        </table>
    </div>

    {{-- Detail Pengeluaran --}}
    <h3>Detail Pengeluaran</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th style="text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($detailedExpenses as $expense)
                <tr>
                    <td>{{ date('d M Y', strtotime($expense->expense_date)) }}</td>
                    <td>{{ $expense->category->name ?? 'N/A' }}</td>
                    <td>{{ $expense->description }}</td>
                    <td style="text-align: right;">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada catatan pengeluaran rinci.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">Total Pengeluaran</td>
                <td style="text-align: right;">Rp {{ number_format($summary['totalExpense'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Detail Pemasukan --}}
    <h3>Detail Pemasukan</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No. Pelanggan</th>
                <th>Dicatat Oleh</th>
                <th style="text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($detailedIncomes as $income)
                <tr>
                    <td>{{ date('d M Y H:i', strtotime($income->transaction_date)) }}</td>
                    <td>{{ $income->customer_number ?? '-' }}</td>
                    <td>{{ $income->user->name ?? 'N/A' }}</td>
                    <td style="text-align: right;">Rp {{ number_format($income->total_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada catatan pemasukan rinci.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">Total Pemasukan</td>
                <td style="text-align: right;">Rp {{ number_format($summary['totalIncome'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>