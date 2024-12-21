<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ChartOfAccount;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $transactions = Transaction::with('chartOfAccount')
                ->whereIn('table_type', ['Expenses', 'Cogs'])
                ->where('branch_id', auth()->user()->branch_id)
                ->where('status', 0);

        if ($request->filled('start_date')) {
                $endDate = $request->filled('end_date') ? $request->input('end_date') : now()->endOfDay();
                $transactions->whereBetween('date', [
                    $request->input('start_date'),
                    $endDate
                ]);
            }

            if ($request->filled('account_name')) {
                $transactions->whereHas('chartOfAccount', function ($query) use ($request) {
                    $query->where('account_name', $request->input('account_name'));
                });
            }

            $transactions = $transactions->latest()->get();
               
                
            return DataTables::of($transactions)
                ->addColumn('chart_of_account', function ($transaction) {
                    return $transaction->chartOfAccount ? $transaction->chartOfAccount->account_name : $transaction->description;
                })
                ->make(true);
        }
        $accounts = ChartOfAccount::where('account_head', 'Expenses')->get();
        return view('admin.transactions.expense', compact('accounts'));
    }

    public function store(Request $request)
    {

        if (empty($request->date)) {
            return response()->json(['status' => 303, 'message' => 'Date Field Is Required..!']);
        }

        if (empty($request->chart_of_account_id)) {
            return response()->json(['status' => 303, 'message' => 'Chart of Account ID Field Is Required..!']);
        }

        if (empty($request->table_type)) {
            return response()->json(['status' => 303, 'message' => 'Table Type Field Is Required..!']);
        }

        if (empty($request->amount)) {
            return response()->json(['status' => 303, 'message' => 'Amount Field Is Required..!']);
        }

        if (empty($request->transaction_type)) {
            return response()->json(['status' => 303, 'message' => 'Transaction Type Field Is Required..!']);
        }

        if ($request->transaction_type !== 'Prepaid Adjust' && empty($request->payment_type)) {
            return response()->json(['status' => 303, 'message' => 'Payment Type Field Is Required..!']);
        }

        $transaction = new Transaction();
        $transaction->tran_id = strtoupper(Str::random(2)) . date('Y') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $transaction->date = $request->input('date');
        $transaction->chart_of_account_id = $request->input('chart_of_account_id');
        $transaction->table_type = $request->input('table_type');
        $transaction->ref = $request->input('ref');
        $transaction->description = $request->input('description');
        $transaction->amount = $request->input('amount');
        $transaction->tax_rate = $request->input('tax_rate');
        $transaction->tax_amount = $request->input('tax_amount');
        $transaction->vat_rate = $request->input('vat_rate');
        $transaction->vat_amount = $request->input('vat_amount');
        $transaction->at_amount = $request->input('at_amount');
        $transaction->transaction_type = $request->input('transaction_type');
        $transaction->liability_id = $request->input('payable_holder_id');
        $transaction->payment_type = $request->input('payment_type');
        $transaction->expense_id = $request->input('chart_of_account_id');
        $transaction->branch_id = Auth::user()->branch_id;
        $transaction->created_by = Auth()->user()->id;
        $transaction->created_ip = request()->ip();

        $transaction->save();
        $transaction->tran_id = 'EX' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        return response()->json(['status' => 200, 'message' => 'Created Successfully']);

    }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);

        $responseData = [
            'id' => $transaction->id,
            'date' => $transaction->date,
            'chart_of_account_id' => $transaction->chart_of_account_id,
            'ref' => $transaction->ref,
            'transaction_type' => $transaction->transaction_type,
            'amount' => $transaction->amount,
            'tax_rate' => $transaction->tax_rate,
            'tax_amount' => $transaction->tax_amount,
            'at_amount' => $transaction->at_amount,
            'payment_type' => $transaction->payment_type,
            'description' => $transaction->description,
            'payable_holder_id' => $transaction->liability_id,
        ];
        return response()->json($responseData);
    }

    public function update(Request $request, $id)
    {

        if (empty($request->date)) {
            return response()->json(['status' => 303, 'message' => 'Date Field Is Required..!']);
        }

        if (empty($request->chart_of_account_id)) {
            return response()->json(['status' => 303, 'message' => 'Chart of Account ID Field Is Required..!']);
        }

        if (empty($request->amount)) {
            return response()->json(['status' => 303, 'message' => 'Amount Field Is Required..!']);
        }

        if (empty($request->transaction_type)) {
            return response()->json(['status' => 303, 'message' => 'Transaction Type Field Is Required..!']);
        }

        if ($request->transaction_type !== 'Prepaid Adjust' && empty($request->payment_type)) {
            return response()->json(['status' => 303, 'message' => 'Payment Type Field Is Required..!']);
        }

        $transaction = Transaction::find($id);

        $transaction->date = $request->input('date');
        $transaction->chart_of_account_id = $request->input('chart_of_account_id');
        $transaction->ref = $request->input('ref');
        $transaction->description = $request->input('description');
        $transaction->amount = $request->input('amount');
        // $transaction->tax_rate = $request->input('tax_rate');
        // $transaction->tax_amount = $request->input('tax_amount');
        $transaction->vat_rate = $request->input('vat_rate');
        $transaction->vat_amount = $request->input('vat_amount');
        $transaction->at_amount = $request->input('at_amount');
        $transaction->transaction_type = $request->input('transaction_type');

        if ($request->input('transaction_type') !== 'Due') {
        $transaction->liability_id = null;
        } else {
            $transaction->liability_id = $request->input('payable_holder_id');
        }

        // $transaction->liability_id = $request->input('payable_holder_id');
        // $transaction->payment_type = $request->input('payment_type');
        $transaction->expense_id = $request->input('chart_of_account_id');
        $transaction->updated_by = Auth()->user()->id;
        $transaction->updated_ip = request()->ip();

        if ($request->input('transaction_type') === 'Prepaid Adjust') {
            $transaction->tax_rate = null;
            $transaction->tax_amount = null;
            $transaction->payment_type = null;
            $transaction->at_amount = $request->input('amount');
        } else {
            $transaction->tax_rate = $request->input('tax_rate');
            $transaction->tax_amount = $request->input('tax_amount');
            $transaction->payment_type = $request->input('payment_type');
        }

        $transaction->save();

        return response()->json(['status' => 200, 'message' => 'Updated Successfully']);

    }
    
}
