<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;

class DashboardController extends Controller
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
        $today = date('Y-m-d');

        $selectedYear = $request->input('year', date('Y'));

        $transactionsToday = Transaction::whereDate('transaction_date', $today)->get();
        $totalIncomeToday = $transactionsToday->sum('total_amount');
        $transactionCountToday = $transactionsToday->count();

        $startOfMonth = date('Y-m-01 00:00:00');
        $endOfMonth = date('Y-m-t 23:59:59');
        $totalIncomeMonth = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->sum('total_amount');

        $totalExpenseToday = Expense::whereDate('expense_date', $today)->sum('amount');
        $totalExpenseMonth = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])->sum('amount');

        $netProfitToday = $totalIncomeToday - $totalExpenseToday;
        $netProfitMonth = $totalIncomeMonth - $totalExpenseMonth;

        $topSellingMenuToday = TransactionDetail::selectRaw('menu_id, SUM(quantity) as total_quantity')
            ->whereHas('transaction', function ($query) use ($today) {
                $query->whereDate('transaction_date', $today);
            })
            ->groupBy('menu_id')
            ->orderByDesc('total_quantity')
            ->with('menu')
            ->first();

        $monthlyData = $this->calculateMonthlyData($selectedYear);

        $latestTransactions = Transaction::orderBy('transaction_date', 'desc')
            ->with('user')
            ->take(5)
            ->get();

        return view('dashbord', compact(
            'totalIncomeToday',
            'transactionCountToday',
            'topSellingMenuToday',
            'totalIncomeMonth',
            'totalExpenseToday',
            'totalExpenseMonth',
            'netProfitToday',
            'netProfitMonth',
            'monthlyData',
            'latestTransactions',
            'selectedYear'
        ));
    }
}
