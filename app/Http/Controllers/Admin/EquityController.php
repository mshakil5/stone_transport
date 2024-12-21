<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\EquityHolder;
use Illuminate\Support\Str;
use App\Models\ChartOfAccount;

class EquityController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $transactions = Transaction::with('chartOfAccount', 'equityHolder')
                ->where('table_type', 'Equity')
                ->where('branch_id', auth()->user()->branch_id);

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
                    return $transaction->chartOfAccount->account_name;
                })
                ->addColumn('share_holder_name', function ($transaction) {
                    return $transaction->equityHolder ? $transaction->equityHolder->name : 'N/A';
                })
                ->make(true);
        }
        $accounts = ChartOfAccount::where('account_head', 'Equity')->get();
        return view('admin.transactions.equity', compact('accounts'));
    }

    public function store(Request $request)
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

        if (empty($request->payment_type)) {
            return response()->json(['status' => 303, 'message' => 'Payment Field Is Required..!']);
        }

        $transaction = new Transaction();
        $transaction->date = $request->input('date');
        $transaction->chart_of_account_id = $request->input('chart_of_account_id');
        $transaction->table_type = 'Equity';
        $transaction->ref = $request->input('ref');
        $transaction->description = $request->input('description');
        $transaction->amount = $request->input('amount');
        $transaction->at_amount = $request->input('amount');
        $transaction->transaction_type = $request->input('transaction_type');
        $transaction->payment_type = $request->input('payment_type');
        $transaction->share_holder_id = $request->input('share_holder_id');
        $transaction->branch_id = Auth::user()->branch_id;
        $transaction->created_by = Auth()->user()->id;
        $transaction->created_ip = request()->ip();

        $transaction->save();
        $transaction->tran_id = 'EQ' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        $shareHolder = EquityHolder::find($request->input('share_holder_id'));

        if ($request->input('transaction_type') == 'Received') {
            $shareHolder->balance += $request->input('amount');
        } elseif ($request->input('transaction_type') == 'Payment') {
            $shareHolder->balance -= $request->input('amount');
        }

        $shareHolder->save();

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
            'share_holder_id' => $transaction->share_holder_id,
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

        if (empty($request->payment_type)) {
            return response()->json(['status' => 303, 'message' => 'Payment Field Is Required..!']);
        }

        $transaction = Transaction::find($id);

        $oldAmount = $transaction->amount;
        $oldTransactionType = $transaction->transaction_type;


        $transaction = Transaction::find($id);

        $transaction->date = $request->input('date');
        $transaction->chart_of_account_id = $request->input('chart_of_account_id');
        $transaction->ref = $request->input('ref');
        $transaction->description = $request->input('description');
        $transaction->amount = $request->input('amount');
        $transaction->at_amount = $request->input('amount');
        $transaction->transaction_type = $request->input('transaction_type');
        $transaction->payment_type = $request->input('payment_type');
        $transaction->share_holder_id = $request->input('share_holder_id');
        $transaction->updated_by = Auth()->user()->id;
        $transaction->updated_ip = request()->ip();

        $transaction->save();

        if ($oldAmount != $transaction->amount || $oldTransactionType != $transaction->transaction_type) 
        
        {
             $shareHolder = EquityHolder::find($request->input('share_holder_id'));

            if ($oldTransactionType == 'Received') {
                $shareHolder->balance -= $oldAmount;
            } elseif ($oldTransactionType == 'Payment') {
                $shareHolder->balance += $oldAmount;
            }

            if ($transaction->transaction_type == 'Received') {
                $shareHolder->balance += $transaction->amount;
            } elseif ($transaction->transaction_type == 'Payment') {
                $shareHolder->balance -= $transaction->amount;
            }

            $shareHolder->save();
        }

        return response()->json(['status' => 200, 'message' => 'Updated Successfully']);

    }
}
