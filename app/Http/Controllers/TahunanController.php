<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;

class TahunanController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->input('year', date('Y'));

        $monthlySummaries = [];
        $totalYearlyIncome = 0;
        $totalYearlyExpense = 0;
        $totalYearlyTransactions = 0;

        for ($m = 1; $m <= 12; $m++) {
            $monthStart = date('Y-m-01 00:00:00', strtotime("{$selectedYear}-{$m}-01"));
            $monthEnd = date('Y-m-t 23:59:59', strtotime("{$selectedYear}-{$m}-01"));

            $income = Transaction::whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('total_amount');
            $expense = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');
            $transCount = Transaction::whereBetween('transaction_date', [$monthStart, $monthEnd])->count();

            $monthlySummaries[] = (object) [
                'month_name' => date('F', mktime(0, 0, 0, $m, 10)),
                'month_number' => $m,
                'income' => $income,
                'expense' => $expense,
                'profit' => $income - $expense,
                'transactions' => $transCount
            ];

            $totalYearlyIncome += $income;
            $totalYearlyExpense += $expense;
            $totalYearlyTransactions += $transCount;
        }

        $topSellingMenusYearly = [];
        $categories = Category::income()->get();

        foreach ($categories as $category) {
            $topMenu = TransactionDetail::selectRaw('menu.name, SUM(detailtransaksi.quantity) as total_quantity')
                ->join('menu', 'detailtransaksi.menu_id', '=', 'menu.id')
                ->join('transaksi', 'detailtransaksi.transaction_id', '=', 'transaksi.id')
                ->whereYear('transaksi.transaction_date', $selectedYear)
                ->where('menu.category_id', $category->id)
                ->groupBy('menu.name')
                ->orderByDesc('total_quantity')
                ->first();

            if ($topMenu) {
                $topSellingMenusYearly[] = [
                    'category_name' => $category->name,
                    'menu_name' => $topMenu->name,
                    'total_quantity' => $topMenu->total_quantity,
                ];
            }
        }

        return view('tahunan', [
            'monthlySummaries' => $monthlySummaries,
            'totalYearlyIncome' => $totalYearlyIncome,
            'totalYearlyExpense' => $totalYearlyExpense,
            'netProfitYearly' => $totalYearlyIncome - $totalYearlyExpense,
            'totalYearlyTransactions' => $totalYearlyTransactions,
            'selectedYear' => $selectedYear,
            'topSellingMenusYearly' => $topSellingMenusYearly,
        ]);
    }

    public function showGraph(Request $request)
    {
        $years = [2024, 2025, 2026, 2027, 2028];
        $yearlyComparisonData = [];

        foreach ($years as $year) {
            $income = Transaction::whereYear('transaction_date', $year)->sum('total_amount');
            
            $expense = Expense::whereYear('expense_date', $year)->sum('amount');

            $yearlyComparisonData[$year] = [
                'income' => $income,
                'expense' => $expense,
                'profit' => $income - $expense,
            ];
        }

        return view('grafiktahunan', [
            'years' => $years,
            'yearlyData' => $yearlyComparisonData,
        ]);
    }
}