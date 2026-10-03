<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;

class HarianController extends Controller
{
    protected function calculateMonthlyData($year)
    {
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStart = date('Y-m-01 00:00:00', strtotime("{$year}-{$m}-01"));
            $monthEnd = date('Y-m-t 23:59:59', strtotime("{$year}-{$m}-01"));

            $income = Transaction::whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('total_amount');
            $expense = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');

            $monthlyData[date('M', strtotime("{$year}-{$m}-01"))] = [
                'income' => $income,
                'expense' => $expense,
                'profit' => $income - $expense,
            ];
        }

        return $monthlyData;
    }

    public function index(Request $request)
    {
        $selectedDate = $request->input('date', date('Y-m-d'));
        $perPage = $request->input('per_page', 5);

        // Ambil data transaksi (termasuk nomor_meja otomatis terambil karena ada di model)
        $transactions = Transaction::whereDate('transaction_date', $selectedDate)
            ->orderBy('transaction_date', 'desc')
            ->with('details.menu')
            ->paginate($perPage);

        // REVISI: Total income diambil dari total harian di DB, bukan dari limit per halaman
        $totalIncome = Transaction::whereDate('transaction_date', $selectedDate)->sum('total_amount');
        $transactionCount = Transaction::whereDate('transaction_date', $selectedDate)->count();

        $expenses = Expense::whereDate('expense_date', $selectedDate)
            ->with('category', 'user')
            ->orderBy('expense_date', 'desc')
            ->get();
        $totalExpense = $expenses->sum('amount');

        $netProfit = $totalIncome - $totalExpense;

        $topSellingMenusPerCategory = [];
        $categories = Category::income()->get();

        foreach ($categories as $category) {
            $topMenu = TransactionDetail::selectRaw('menu.name, SUM(detailtransaksi.quantity) as total_quantity')
                ->join('menu', 'detailtransaksi.menu_id', '=', 'menu.id')
                ->join('transaksi', 'detailtransaksi.transaction_id', '=', 'transaksi.id')
                ->whereDate('transaksi.transaction_date', $selectedDate)
                ->where('menu.category_id', $category->id)
                ->groupBy('menu.name')
                ->orderByDesc('total_quantity')
                ->first();

            if ($topMenu) {
                $topSellingMenusPerCategory[] = [
                    'category_name' => $category->name,
                    'menu_name' => $topMenu->name,
                    'total_quantity' => $topMenu->total_quantity,
                ];
            }
        }

        $selectedYearForGraph = date('Y', strtotime($selectedDate));
        $monthlyData = $this->calculateMonthlyData($selectedYearForGraph);

        return view('harian', [
            'transactions' => $transactions,
            'totalIncome' => $totalIncome,
            'transactionCount' => $transactionCount,
            'expenses' => $expenses,
            'totalExpense' => $totalExpense,
            'netProfit' => $netProfit,
            'selectedDate' => $selectedDate,
            'topSellingMenusPerCategory' => $topSellingMenusPerCategory,
            'perPage' => $perPage,
            'monthlyData' => $monthlyData,
            'selectedYearForGraph' => $selectedYearForGraph,
        ]);
    }

    public function showGraph(Request $request, $year = null, $month = null)
    {
        $year = $request->input('year', $year ?? date('Y'));
        $month = $request->input('month', $month ?? date('m'));

        $labels = [];
        $incomeData = [];
        $expenseData = [];
        $profitData = [];

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateString = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $labels[] = $d;

            $income = Transaction::whereDate('transaction_date', $dateString)->sum('total_amount');
            $expense = Expense::whereDate('expense_date', $dateString)->sum('amount');

            $incomeData[] = $income;
            $expenseData[] = $expense;
            $profitData[] = $income - $expense;
        }

        $chartTitle = 'Grafik Harian - '.date('F', mktime(0, 0, 0, $month, 1))." $year";

        return view('grafikharian', compact(
            'labels',
            'incomeData',
            'expenseData',
            'profitData',
            'year',
            'month',
            'chartTitle'
        ));
    }
}
