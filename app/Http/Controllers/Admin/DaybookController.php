<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DaybookController extends Controller
{
    public function cashbook(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $cashbooks = Transaction::where('payment_type', 'Cash')
        ->select('id', 'date', 'description', 'ref', 'chart_of_account_id', 'transaction_type', 'at_amount')
        ->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance', 'Purchase', 'Payment', 'Prepaid'])
        ->when($startDate, function($query, $startDate) {
            return $query->whereDate('date', '>=', $startDate);
        })
        ->when($endDate, function($query, $endDate) {
            return $query->whereDate('date', '<=', $endDate);
        })
        ->orderBy('id', 'desc')
        ->get();
        
        $totalDrAmount = Transaction::where('payment_type', 'Cash')
        ->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance'])
        ->when($startDate, function($query, $startDate) {
            return $query->whereDate('date', '>=', $startDate);
        })
        ->when($endDate, function($query, $endDate) {
            return $query->whereDate('date', '<=', $endDate);
        })
        ->sum('at_amount');

        $totalCrAmount = Transaction::where('payment_type', 'Cash')
        ->whereIn('transaction_type', ['Purchase', 'Payment', 'Prepaid'])
        ->when($startDate, function($query, $startDate) {
            return $query->whereDate('date', '>=', $startDate);
        })
        ->when($endDate, function($query, $endDate) {
            return $query->whereDate('date', '<=', $endDate);
        })
        ->sum('at_amount');

        $totalAmount = $totalDrAmount - $totalCrAmount;
        return view('admin.accounts.daybook.cashbook', compact('cashbooks', 'totalAmount'));
    }

    public function bankbook(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $bankbooks = Transaction::where('payment_type', 'Bank')
            ->select('id', 'date', 'description', 'ref', 'chart_of_account_id', 'transaction_type', 'at_amount')
            ->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance', 'Purchase', 'Payment', 'Prepaid'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->orderBy('id', 'desc')
            ->get();

        $totalDrAmount = Transaction::where('payment_type', 'Bank')
            ->whereIn('transaction_type', ['Current', 'Received', 'Sold', 'Advance'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $totalCrAmount = Transaction::where('payment_type', 'Bank')
            ->whereIn('transaction_type', ['Purchase', 'Payment', 'Prepaid'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('at_amount');

        $totalAmount = $totalDrAmount - $totalCrAmount;

        return view('admin.accounts.daybook.bankbook', compact('bankbooks', 'totalAmount'));
    }
}
