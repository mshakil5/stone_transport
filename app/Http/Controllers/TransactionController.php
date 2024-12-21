<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Models\Transaction;
use App\Models\Purchase;

class TransactionController extends Controller
{
    public function pay(Request $request)
    {
        $request->validate([
            'supplierId' => 'required',
            'paymentAmount' => 'required',
            'paymentNote' => 'nullable',
        ]);

        $transaction = new Transaction();
        $transaction->supplier_id = $request->supplierId;

        if ($request->hasFile('document')) {
            $uploadedFile = $request->file('document');
            $randomName = mt_rand(10000000, 99999999).'.'.$uploadedFile->getClientOriginalExtension();
            $destinationPath = 'images/supplier/document/';
            $uploadedFile->move(public_path($destinationPath), $randomName);
            $transaction->document = '/' . $destinationPath . $randomName;
        }

        $transaction->amount = $request->paymentAmount;
        $transaction->at_amount = $request->paymentAmount;
        $transaction->payment_type = $request->payment_type;
        $transaction->note = $request->paymentNote;
        $transaction->table_type = "Purchase";
        $transaction->transaction_type = "Current";
        $transaction->date = date('Y-m-d');
        $transaction->save();
        $transaction->tran_id = 'AP' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Payment processed successfully!',
        ]);
    }

    public function duePay(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required',
            'supplier_id' => 'required',
            'payment_amount' => 'required',
            'payment_type' => 'required',
            'payment_note' => 'nullable',
        ]);

        $transaction = new Transaction();
        $transaction->supplier_id = $request->supplier_id;
        $transaction->purchase_id = $request->purchase_id;

        if ($request->hasFile('document')) {
            $uploadedFile = $request->file('document');
            $randomName = mt_rand(10000000, 99999999).'.'.$uploadedFile->getClientOriginalExtension();
            $destinationPath = 'images/supplier/document/';
            $uploadedFile->move(public_path($destinationPath), $randomName);
            $transaction->document = '/' . $destinationPath . $randomName;
        }

        $transaction->amount = $request->payment_amount;
        $transaction->at_amount = $request->payment_amount;
        $transaction->payment_type = $request->payment_type;
        $transaction->description = $request->payment_note;
        $transaction->table_type = "Purchase";
        $transaction->transaction_type = "Current";
        $transaction->date = date('Y-m-d');

        if($transaction->save()){
            $transaction->tran_id = 'AP' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);

            $purchase = Purchase::find($request->purchase_id);
            $purchase->due_amount = $purchase->due_amount - $request->payment_amount;
            $purchase->paid_amount = $purchase->paid_amount + $request->payment_amount;
            $purchase->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment processed successfully!',
            ]);
            
        }


    }
}
