<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\PurchaseHistory;
use App\Models\PurchaseHistoryLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class IncomeStatementController extends Controller
{

    public function incomeStatement()
    {
        return view('admin.accounts.income_statement.search');
    }


    public function incomeStatementSearch(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $purchaseSum = Transaction::where('table_type', 'Purchase')
            ->where('status', 0)
            ->where('transaction_type', 'Due')
            ->where('description', 'Purchase')
            ->whereNull('chart_of_account_id')
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('at_amount');

        $salesSum = Transaction::where('table_type', 'Sales')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->where('transaction_type', 'Current')
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('amount');

        $operatingIncomes = Transaction::where('table_type', 'Income')
            ->with('chartOfAccount')
            ->where('status', 0)
            ->whereNotNull('chart_of_account_id')
            ->whereIn('transaction_type', ['Current','Advance'])
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->selectRaw('chart_of_account_id, SUM(amount) as total_amount')
            ->groupBy('chart_of_account_id')
            ->get();
        
        $operatingIncomeSums = $operatingIncomes->sum('total_amount');

        $operatingIncomeRefundSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNotNull('chart_of_account_id')
            ->where('transaction_type', 'Refund')
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('amount');
         
         //Operating Expense   
        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');
        $operatingExpenses = Transaction::select('chart_of_account_id', DB::raw('SUM(amount) as total_amount'))
            ->with('chartOfAccount')
            ->whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->groupBy('chart_of_account_id')
            ->get();
        $operatingExpenseSum = $operatingExpenses->sum('total_amount');
        // dd($operatingExpenseSum);

        //Overhead expense
        $overHeadExpenseId = ChartOfAccount::where('sub_account_head', 'Overhead Expense')->pluck('id');
        $overHeadExpenses = Transaction::select('chart_of_account_id', DB::raw('SUM(amount) as total_amount'))
            ->with('chartOfAccount')
            ->whereIn('chart_of_account_id', $overHeadExpenseId)
            ->where('status', 0)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->groupBy('chart_of_account_id')
            ->get();

        $overHeadExpenseSum = $overHeadExpenses->sum('total_amount');

        //Administrative Expense
        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');
        $administrativeExpenses = Transaction::select('chart_of_account_id', DB::raw('SUM(amount) as total_amount'))
            ->with('chartOfAccount')
            ->whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->groupBy('chart_of_account_id')
            ->get();
        $administrativeExpenseSum = $administrativeExpenses->sum('total_amount');

        //Fixed Asset Depreciation Expense
        $fixedAssetId = ChartOfAccount::where('sub_account_head', 'Fixed Asset')->where('account_head', 'Assets')->pluck('id');

        $fixedAssetDepriciation = Transaction::whereIn('chart_of_account_id', $fixedAssetId)
            ->where('status', 0)
            ->where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Depreciation'])
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('amount');

        $salesReturn = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('transaction_type', 'Return')
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('amount');

        $purchaseReturn = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('transaction_type', 'Return')
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('amount');

        $salesDiscount = Order::when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('created_at', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('discount_amount');

        $updatedStartDate = Carbon::parse($startDate)->format('Y-m-d');
        $updatedEndDate = Carbon::parse($endDate)->subDay()->format('Y-m-d');

        $totalOpeningStock = PurchaseHistoryLog::when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
            $query->whereBetween('log_date', [$request->input('start_date'), $request->input('end_date')]);
        })
        ->sum('total_amount');

        $closingBalances = PurchaseHistory::where('available_stock', '>', 0)
        ->get()
        ->groupBy('product_id')
        ->map(function ($purchaseHistories) {
            return $purchaseHistories->sum(function ($purchaseHistory) {
                return $purchaseHistory->available_stock * $purchaseHistory->purchase_price;
            });
        });

        $totalClosingStock = $closingBalances->sum();

        $purchaseVatSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->whereNull('chart_of_account_id')
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('vat_amount');

        $salesVatSum = Transaction::where('table_type', 'Sales')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('vat_amount');

        $operatingIncomeVatSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNotNull('chart_of_account_id')
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('vat_amount');

        $operatingExpenseVatSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('vat_amount');

        $administrativeExpenseVatSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            })
            ->sum('vat_amount');

        $taxAndVat =  $purchaseVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum - $salesVatSum - $operatingIncomeVatSum;

        return view('admin.accounts.income_statement.index', compact(
            'purchaseSum', 
            'salesSum', 
            'operatingExpenses', 
            'administrativeExpenses', 
            'salesReturn', 
            'salesDiscount', 
            'operatingExpenseSum', 
            'administrativeExpenseSum', 
            'totalOpeningStock', 
            'totalClosingStock', 
            'taxAndVat',
            'operatingIncomes',
            'operatingIncomeSums',
            'operatingIncomeRefundSum',
            'purchaseReturn',
            'overHeadExpenses',
            'overHeadExpenseSum',
            'fixedAssetDepriciation'
        ))->with('start_date', $startDate)->with('end_date', $endDate);
    }
}
