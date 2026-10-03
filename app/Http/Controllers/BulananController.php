<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;

class BulananController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->input('month', date('n'));
        $selectedYear = $request->input('year', date('Y'));
        $perPage = $request->input('per_page', 5);

        $startOfMonth = date('Y-m-01 00:00:00', strtotime("{$selectedYear}-{$selectedMonth}-01"));
        $endOfMonth = date('Y-m-t 23:59:59', strtotime("{$selectedYear}-{$selectedMonth}-01"));

        $dailySummaries = Transaction::selectRaw('DATE(transaction_date) as transaction_date_only, COUNT(DISTINCT customer_number) as total_customers_today, SUM(total_amount) as total_daily_income')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->groupBy('transaction_date_only')
            ->orderBy('transaction_date_only', 'desc')
            ->paginate($perPage);

        $totalMonthlyIncome = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');
        $totalMonthlyTransactions = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->count();

        $dailyExpenseSummaries = Expense::selectRaw('DATE(expense_date) as expense_date_only, SUM(amount) as total_daily_expense')
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->groupBy('expense_date_only')
            ->orderBy('expense_date_only', 'desc')
            ->get();

        $totalMonthlyExpense = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $netProfitMonthly = $totalMonthlyIncome - $totalMonthlyExpense;

        $topSellingMenusPerCategory = [];
        $categories = Category::income()->get();

        foreach ($categories as $category) {
            $topMenu = TransactionDetail::selectRaw('menu.name, SUM(detailtransaksi.quantity) as total_quantity')
                ->join('menu', 'detailtransaksi.menu_id', '=', 'menu.id')
                ->join('transaksi', 'detailtransaksi.transaction_id', '=', 'transaksi.id')
                ->whereBetween('transaksi.transaction_date', [$startOfMonth, $endOfMonth])
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

        $combinedSummaries = collect($dailySummaries->items())->keyBy(function ($item) {
            return $item->transaction_date_only;
        });

        foreach ($dailyExpenseSummaries as $expenseSummary) {
            $dateKey = $expenseSummary->expense_date_only;
            if ($combinedSummaries->has($dateKey)) {
                $combinedSummaries[$dateKey]->total_daily_expense = $expenseSummary->total_daily_expense;
            } else {
                $combinedSummaries[$dateKey] = (object) [
                    'transaction_date_only' => $dateKey,
                    'total_customers_today' => 0,
                    'total_daily_income' => 0,
                    'total_daily_expense' => $expenseSummary->total_daily_expense,
                ];
            }
        }

        $combinedSummaries = $combinedSummaries->sortByDesc('transaction_date_only');

        return view('bulanan', [
            'dailySummaries' => $dailySummaries,
            'combinedSummaries' => $combinedSummaries,
            'totalMonthlyIncome' => $totalMonthlyIncome,
            'totalMonthlyExpense' => $totalMonthlyExpense,
            'netProfitMonthly' => $netProfitMonthly,
            'totalMonthlyTransactions' => $totalMonthlyTransactions,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'perPage' => $perPage,
            'topSellingMenusPerCategory' => $topSellingMenusPerCategory,
        ]);
    }

    public function showGraph(Request $request, $year = null)
    {
        $selectedYear = $request->input('year', $year ?? date('Y'));

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStart = date('Y-m-01 00:00:00', strtotime("{$selectedYear}-{$m}-01"));
            $monthEnd = date('Y-m-t 23:59:59', strtotime("{$selectedYear}-{$m}-01"));

            $income = Transaction::whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('total_amount');
            $expense = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');

            $monthlyData[date('M', strtotime("{$selectedYear}-{$m}-01"))] = [
                'income' => $income,
                'expense' => $expense,
                'profit' => $income - $expense,
            ];
        }

        return view('grafikbulanan', [
            'selectedYear' => $selectedYear,
            'monthlyData' => $monthlyData,
        ]);
    }
}
