<?php

namespace App\Http\Controllers;

use App\Mail\SupplierEmail;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Str;
use App\Models\SupplierTransaction;
use Illuminate\Support\Facades\Hash;
use App\Models\SupplierStock;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;

class SupplierController extends Controller
{
    public function getSupplier()
    {
        $data = Supplier::getAllsuppliersWithBalance();
        return view('admin.supplier.index', compact('data'));
    }

    public function supplierStore(Request $request)
    {
        if(empty($request->id_number)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Supplier code \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $chkid = Supplier::where('id_number',$request->id_number)->first();
        if($chkid){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This supplier code already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Supplier name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(isset($request->password) && ($request->password != $request->confirm_password)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new Supplier;
        $data->id_number = $request->id_number;
        $data->name = $request->name;
        $data->slug = Str::slug($request->name);
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->vat_reg = $request->vat_reg;
        $data->address = $request->address;
        $data->company = $request->company;
        $data->contract_date = $request->contract_date;
        if(isset($request->password)){
            $data->password = Hash::make($request->password);
        }
        $data->created_by = auth()->id(); 

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/supplier/');
            $path = $uploadedFile->move($destinationPath, $randomName); 
            $data->image = $randomName;
        }
        
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Create Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function supplierEdit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Supplier::where($where)->get()->first();
        return response()->json($info);
    }

    public function supplierUpdate(Request $request)
    {
        if(empty($request->id_number)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Supplier code \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Supplier name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $duplicatid = Supplier::where('id_number',$request->id_number)->where('id','!=', $request->codeid)->first();
        if($duplicatid){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This supplier code already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $duplicatename = Supplier::where('name',$request->name)->where('id','!=', $request->codeid)->first();
        if($duplicatename){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This supplier name added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(isset($request->password) && ($request->password != $request->confirm_password)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

            $data = Supplier::find($request->codeid);
            $data->id_number = $request->id_number;
            $data->name = $request->name;
            $data->slug = Str::slug($request->name);
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->vat_reg = $request->vat_reg;
            $data->address = $request->address;
            $data->company = $request->company;
            $data->contract_date = $request->contract_date;
            if(isset($request->password)){
                $data->password = Hash::make($request->password);
            }
            $data->updated_by = auth()->id(); 

            if ($request->hasFile('image')) {
                $uploadedFile = $request->file('image');

                if ($data->image && file_exists(public_path('images/supplier/'. $data->image))) {
                    unlink(public_path('images/supplier/'. $data->image));
                }

                $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
                $destinationPath = public_path('images/supplier/');
                $path = $uploadedFile->move($destinationPath, $randomName); 
                $data->image = $randomName;
                $data->save();
           }

          if ($data->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Failed to update data. Please try again.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

    }

    public function supplierDelete($id)
    {
        $brand = Supplier::find($id);
        
        if (!$brand) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($brand->image && file_exists(public_path('images/supplier/' . $brand->image))) {
            unlink(public_path('images/supplier/' . $brand->image));
        }

        if ($brand->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }

    public function supplierTransactions($supplierId)
    {
        $supplier = Supplier::whereId($supplierId)->select('id', 'name')->first();
        $transactions = Transaction::where('supplier_id', $supplierId)
                                ->orderBy('id', 'desc')
                                ->select('id', 'date', 'note', 'payment_type', 'table_type', 'at_amount', 'document')
                                ->get();

    
                                
        $totalDrAmount = Transaction::where('supplier_id', $supplierId)->whereIn('table_type', ['Purchase'])->whereIn('payment_type', ['Credit'])->sum('at_amount');

        $totalCrAmount = Transaction::where('supplier_id', $supplierId)->whereIn('table_type', ['Purchase'])->whereIn('payment_type', ['Cash','Bank','Return'])->sum('at_amount');

        $totalBalance = $totalDrAmount - $totalCrAmount;
        return view('admin.supplier.transactions', compact('transactions','supplier','totalBalance'));
    }

    public function showStocks($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return redirect()->back()->with('error', 'Supplier not found.');
        }
        $data = SupplierStock::where('supplier_id', $id)->orderBy('id', 'desc')->get();
        return view('admin.supplier.stocks', compact('supplier', 'data'));
    }

    public function approveItem(Request $request)
    {
        $item = SupplierStock::find($request->id);
        if ($item) {
            $item->is_approved = $request->is_approved;
            $item->save();

            return response()->json(['message' => 'Updated successfully.']);
        }

        return response()->json(['message' => 'Item not found.'], 404);
    }

    public function showOrders($supplierId)
    {
        $supplier = Supplier::with(['orderDetails' => function($query) {
            $query->orderBy('created_at', 'DESC');
        }])->findOrFail($supplierId);
        return view('admin.supplier.orders', compact('supplier'));
    }

    public function showPurchase($supplierId)
    {
       
        $purchases = Purchase::with('purchaseHistory.product','supplier')->where('supplier_id', $supplierId)->orderby('id','DESC')->get();
        return view('admin.stock.purchase_history', compact('purchases'));

    }

    public function toggleStatus(Request $request)
    {
        $supplier = Supplier::findOrFail($request->id);
        $supplier->status = $request->status;
        $supplier->save();

        return response()->json(['message' => 'Supplier status updated successfully']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_number' => 'required|unique:suppliers',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|numeric',
            'password' => 'nullable|string|min:6',
        ]);

        $supplier = new Supplier();
        $supplier->id_number = $request->id_number;
        $supplier->name = $request->name;
        $supplier->slug = Str::slug($request->name);
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->password = bcrypt($request->password);
        $supplier->vat_reg = $request->vat_reg;
        $supplier->contract_date = $request->contract_date;
        $supplier->address = $request->address;
        $supplier->company = $request->company;

        if ($supplier->save()) {
            return response()->json([
                'success' => true,
                'data' => $supplier,
            ]);
        } else {
            return response()->json(['success' => false], 500);
        }
    }

    public function updateTransaction(Request $request)
    {
        $request->validate([
            'transactionId' => 'required|integer|exists:transactions,id',
            'at_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);
    
        $transaction = Transaction::findOrFail($request->transactionId);
    
        $transaction->at_amount = $request->at_amount;
        $transaction->note = $request->note;
    
        if ($request->hasFile('document')) {
            if ($transaction->document && file_exists(public_path($transaction->document))) {
                unlink(public_path($transaction->document));
            }
    
            $uploadedFile = $request->file('document');
            $randomName = mt_rand(10000000, 99999999) . '.' . $uploadedFile->getClientOriginalExtension();
            $destinationPath = 'images/supplier/document/';
            $uploadedFile->move(public_path($destinationPath), $randomName);
            
            $transaction->document = '/' . $destinationPath . $randomName;
        }

        $transaction->updated_by = auth()->user()->id;
    
        $transaction->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully!',
        ]);
    }

    public function supplierEmail($id)
    {
        $supplier = Supplier::whereId($id)->select('id', 'name','email')->first();
        return view('admin.supplier.email', compact('supplier'));
    }

    public function sendSupplierEmail(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['status' => 'error', 'message' => 'supplier not found.'], 404);
        }

        Mail::to($supplier->email)->send(new SupplierEmail($request->subject, $request->body));
        return response()->json(['status' => 'success', 'message' => 'Email sent successfully.']);
    }

}
