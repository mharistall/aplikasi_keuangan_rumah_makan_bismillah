<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseMasterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    protected function validateExpense(Request $request)
    {
        return $request->validate([
            'expense_date' => 'required|date',
            'category_id' => [
                'required',
                Rule::exists('kategori', 'id')->where(function ($query) {
                    return $query->where('type', 'pengeluaran');
                }),
            ],
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'quantity' => 'nullable|numeric',
            'unit' => 'nullable|string',
        ]);
    }

    public function adminIndex(Request $request)
    {
        $selectedDate = $request->input('date', date('Y-m-d'));
        $categories = Category::expense()->get();
        $masterItems = ExpenseMasterItem::orderBy('name', 'asc')->get();
        $expenses = Expense::with(['user', 'category'])
            ->whereDate('expense_date', $selectedDate)
            ->orderBy('expense_date', 'desc')
            ->paginate($request->input('per_page', 10));
        $totalExpenseToday = Expense::whereDate('expense_date', $selectedDate)->sum('amount');

        return view('expense.admin_index', compact('expenses', 'categories', 'totalExpenseToday', 'selectedDate', 'masterItems'));
    }

    public function ownerIndex(Request $request)
    {
        $selectedDate = $request->input('date', date('Y-m-d'));
        
        $expenses = Expense::with(['user', 'category'])
            ->whereDate('expense_date', $selectedDate)
            ->orderBy('expense_date', 'desc')
            ->paginate($request->input('per_page', 10));
        
        $totalExpenseToday = Expense::whereDate('expense_date', $selectedDate)->sum('amount');

        // REVISI: Logika Grouping berdasarkan Nama Barang Master agar item yang sama menyatu
        // Kita mencocokkan deskripsi pengeluaran yang mengandung nama dari master barang
        $chartData = DB::table('expenses')
            ->join('masterpengeluaranbarang', function($join) {
                // Mencocokkan jika deskripsi di tabel expenses mengandung nama barang dari master
                $join->on('expenses.description', 'LIKE', DB::raw("CONCAT(masterpengeluaranbarang.name, '%')"));
            })
            ->whereDate('expenses.expense_date', $selectedDate)
            ->select('masterpengeluaranbarang.name as item_name', DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('masterpengeluaranbarang.name')
            ->orderBy('total', 'desc')
            ->get();

        // Jika ada pengeluaran manual yang tidak ada di master, kita ambil sebagai "Lainnya"
        $labels = $chartData->pluck('item_name');
        $totals = $chartData->pluck('total');

        return view('expense.owner_index', compact('expenses', 'totalExpenseToday', 'selectedDate', 'labels', 'totals'));
    }

    public function storeMasterItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:masterpengeluaranbarang,name',
            'default_unit' => 'required|string',
        ]);
        ExpenseMasterItem::create($request->all());

        return redirect()->back()->with('success', 'Deskripsi barang baru berhasil ditambahkan!');
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateExpense($request);
        $fullDescription = $request->description;
        if ($request->quantity && $request->unit) {
            $fullDescription .= ' ('.$request->quantity.' '.$request->unit.')';
        }
        Expense::create([
            'expense_date' => $validatedData['expense_date'],
            'category_id' => $validatedData['category_id'],
            'description' => $fullDescription,
            'amount' => $validatedData['amount'],
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.pengeluaran.index')->with('success', 'Pengeluaran berhasil dicatat!');
    }

    public function update(Request $request, Expense $expense)
    {
        $validatedData = $this->validateExpense($request);
        $expense->update($validatedData);

        return redirect()->route('admin.pengeluaran.index')->with('success', 'Pengeluaran berhasil diperbarui!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('admin.pengeluaran.index')->with('success', 'Pengeluaran berhasil dihapus!');
    }
}