<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function create()
    {
        $categories = Category::where('type', 'pemasukan')->get();

        $menus = Menu::with('category')
            ->whereHas('category', function ($query) {
                $query->where('type', 'pemasukan');
            })
            ->get();

        $groupedMenus = $menus->groupBy('category_id');

        $today = date('Y-m-d');
        $countToday = Transaction::whereDate('transaction_date', $today)->count();
        $nextNumber = $countToday + 1;
        $nomor_pelanggan = 'CST'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('inputpesan', compact('categories', 'groupedMenus', 'nomor_pelanggan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_pelanggan' => 'required|string|max:50',
            'nomor_meja' => 'nullable|string|max:20',
            'order_data' => 'required|json',
        ]);

        $items = json_decode($request->order_data, true);

        if (empty($items)) {
            return redirect()->back()->with('error', 'Keranjang belanja kosong!');
        }

        $totalTransactionAmount = 0;
        foreach ($items as $item) {
            $totalTransactionAmount += ($item['price'] * $item['quantity']);
        }

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'transaction_date' => Carbon::now(),
                'customer_number' => $request->nomor_pelanggan,
                'nomor_meja' => $request->nomor_meja,
                'total_amount' => $totalTransactionAmount,
                'user_id' => Auth::id(),
            ]);

            foreach ($items as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'subtotal' => ($item['price'] * $item['quantity']),
                ]);
            }

            DB::commit();

            return redirect()->route('input.transaksi')->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: '.$e->getMessage());
        }
    }
}
