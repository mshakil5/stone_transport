<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CashFlowController extends Controller
{
    public function cashflow(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $branchId = auth()->user()->branch_id;

        $incomes = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->get();

        $assetSold = Transaction::where('table_type', 'Assets')
            ->where('transaction_type', 'Sold')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $liabilityReceived = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $equityReceived = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $expenses = Transaction::where('table_type', 'Expenses')
            ->whereIn('transaction_type', ['Current', 'Prepaid'])
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->get();

        $assetPurchase = Transaction::where('table_type', 'Assets')
            ->where('transaction_type', 'Purchase')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $liabilityPayment = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $equityPayment = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('branch_id', $branchId)
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        return view('admin.accounts.cashflow.index', compact('incomes', 'assetSold', 'liabilityReceived', 'equityReceived','expenses','assetPurchase', 'liabilityPayment', 'equityPayment'));
    }
}
