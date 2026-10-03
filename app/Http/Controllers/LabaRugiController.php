<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LabaRugiController extends Controller
{
    protected function calculateProfitLoss($start_date, $end_date)
    {
        $totalIncome = Transaction::whereBetween('transaction_date', [$start_date, $end_date])
            ->sum('total_amount');

        $totalExpense = Expense::whereBetween('expense_date', [$start_date, $end_date])
            ->sum('amount');

        $netProfit = $totalIncome - $totalExpense;

        return [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netProfit' => $netProfit,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
    }

    public function index(Request $request)
    {
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', date('n'));
        $currentMonth = Carbon::create($selectedYear, $selectedMonth, 1);
        $startOfMonth = $currentMonth->startOfMonth()->toDateTimeString();
        $endOfMonth = $currentMonth->endOfMonth()->toDateTimeString();
        $summary = $this->calculateProfitLoss($startOfMonth, $endOfMonth);
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStart = Carbon::create($selectedYear, $m, 1)->startOfMonth()->toDateTimeString();
            $monthEnd = Carbon::create($selectedYear, $m, 1)->endOfMonth()->toDateTimeString();

            $result = $this->calculateProfitLoss($monthStart, $monthEnd);
            $monthName = date('M', strtotime("{$selectedYear}-{$m}-01"));

            $monthlyData[$monthName] = [
                'income' => $result['totalIncome'],
                'expense' => $result['totalExpense'],
                'profit' => $result['netProfit'],
            ];
        }

        return view('laporan.labarugi.index', compact(
            'summary',
            'monthlyData',
            'selectedYear',
            'selectedMonth'
        ));
    }

    public function exportPdf(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $month = $request->query('month', date('n'));

        $currentMonth = Carbon::create($year, $month, 1);
        $startOfMonth = $currentMonth->startOfMonth()->toDateTimeString();
        $endOfMonth = $currentMonth->endOfMonth()->toDateTimeString();

        $summary = $this->calculateProfitLoss($startOfMonth, $endOfMonth);

        $detailedExpenses = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->with('category')
            ->orderBy('expense_date', 'asc')
            ->get();

        $detailedIncomes = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->with('user')
            ->orderBy('transaction_date', 'asc')
            ->get();

        $data = [
            'summary' => $summary,
            'monthName' => $currentMonth->translatedFormat('F Y'),
            'detailedExpenses' => $detailedExpenses,
            'detailedIncomes' => $detailedIncomes,
        ];

        $pdf = Pdf::loadView('laporan.labarugi.pdf', $data);

        $filename = 'Laporan_LabaRugi_'.$currentMonth->translatedFormat('F_Y').'.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $month = $request->query('month', date('n'));
        $currentMonth = Carbon::create($year, $month, 1);
        $startOfMonth = $currentMonth->startOfMonth()->toDateTimeString();
        $endOfMonth = $currentMonth->endOfMonth()->toDateTimeString();
        $summary = $this->calculateProfitLoss($startOfMonth, $endOfMonth);
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laba Rugi '.$currentMonth->translatedFormat('F Y'));
        $sheet->setCellValue('A1', 'LAPORAN LABA RUGI RUMAH MAKAN BISMILLAH');
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('A2', 'Periode: '.$currentMonth->translatedFormat('F Y'));
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('A4', 'URAIAN');
        $sheet->setCellValue('B4', 'JUMLAH (Rp)');
        $sheet->setCellValue('A5', 'TOTAL PEMASUKAN');
        $sheet->setCellValue('B5', $summary['totalIncome']);
        $sheet->getStyle('A5:B5')->getFont()->setBold(true);
        $sheet->getStyle('B5')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->setCellValue('A6', 'TOTAL PENGELUARAN');
        $sheet->setCellValue('B6', $summary['totalExpense']);
        $sheet->getStyle('A6:B6')->getFont()->setBold(true);
        $sheet->getStyle('B6')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->setCellValue('A8', 'LABA BERSIH');
        $sheet->setCellValue('B8', $summary['netProfit']);
        $sheet->getStyle('A8:B8')->getFont()->setBold(true);
        $sheet->getStyle('A8:B8')->getFill()->getStartColor()->setARGB('FFFFE082');
        $sheet->getStyle('B8')->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'C') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_LabaRugi_'.$currentMonth->translatedFormat('F_Y').'.xlsx';
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
