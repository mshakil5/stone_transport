<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquityHolder;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class EquityHolderController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $equityHolders = EquityHolder::with('branch')->latest()->get();
            return DataTables::of($equityHolders)
                ->addColumn('branch_name', function ($equityHolder) {
                    return $equityHolder->branch->name;
                })
                ->make(true);
        }
        return view('admin.equity_holders.index');
    }

    public function store(Request $request)
    {
        if (empty($request->name)) {
            return response()->json(['status' => 303, 'message' => 'Name Field Is Required..!']);
        }
        if (empty($request->company_name)) {
            return response()->json(['status' => 303, 'message' => 'Company Field Is Required..!']);
        }
        if (empty($request->phone)) {
            return response()->json(['status' => 303, 'message' => 'Phone Field Is Required..!']);
        }
        if (empty($request->tax_number)) {
            return response()->json(['status' => 303, 'message' => 'Tax Field Is Required..!']);
        }
        if (empty($request->tin)) {
            return response()->json(['status' => 303, 'message' => 'Tin Field Is Required..!']);
        }
        if (empty($request->address)) {
            return response()->json(['status' => 303, 'message' => 'Address Field Is Required..!']);
        }

        $equityHolder = new EquityHolder();
        $equityHolder->name = $request->name;
        $equityHolder->company_name = $request->company_name;
        $equityHolder->phone = $request->phone;
        $equityHolder->tax_number = $request->tax_number;
        $equityHolder->tin = $request->tin;
        $equityHolder->address = $request->address;
        $equityHolder->created_by = Auth::user()->id;
        $equityHolder->save();

        return response()->json(['status' => 200, 'message' => 'Created Successfully']);
    }

    public function edit($id)
    {
        $chartDtl = EquityHolder::where('id', '=', $id)->first();
        if(empty($chartDtl)){
            return response()->json(['status'=> 303,'message'=>"No data found"]);
        }else{
            return response()->json(['status'=> 300,
            'id'=>$chartDtl->id,
            'name'=>$chartDtl->name,'company_name'=>$chartDtl->company_name,
            'phone'=>$chartDtl->phone,
            'tax_number'=>$chartDtl->tax_number,
            'address'=>$chartDtl->address,
            'tin'=>$chartDtl->tin]);
        }
    }

    public function update(Request $request, $id)
    {
        if (empty($request->name)) {
            return response()->json(['status' => 303, 'message' => 'Name Field Is Required..!']);
        }
        if (empty($request->company_name)) {
            return response()->json(['status' => 303, 'message' => 'Company Field Is Required..!']);
        }
        if (empty($request->phone)) {
            return response()->json(['status' => 303, 'message' => 'Phone Field Is Required..!']);
        }
        if (empty($request->tax_number)) {
            return response()->json(['status' => 303, 'message' => 'Tax Field Is Required..!']);
        }
        if (empty($request->tin)) {
            return response()->json(['status' => 303, 'message' => 'Tin Field Is Required..!']);
        }
        if (empty($request->address)) {
            return response()->json(['status' => 303, 'message' => 'Address Field Is Required..!']);
        }

        $equityHolder = EquityHolder::find($id);
        $equityHolder->name = $request->name;
        $equityHolder->company_name = $request->company_name;
        $equityHolder->phone = $request->phone;
        $equityHolder->tax_number = $request->tax_number;
        $equityHolder->tin = $request->tin;
        $equityHolder->address = $request->address;
        $equityHolder->updated_by = Auth::user()->id;
        $equityHolder->save();

        return response()->json(['status' => 200, 'message' => 'Updated Successfully']);
    }

    public function shareHolderLedger($id, Request $request)
    {
        $ledgers = Transaction::where('share_holder_id', $id)->get();
        $totalDrAmount = Transaction::where('share_holder_id', $id)->where('transaction_type', ['Payment'])->sum('at_amount');
        $totalCrAmount = Transaction::where('share_holder_id', $id)->where('transaction_type', ['Received'])->sum('at_amount');
        $totalAmount = $totalDrAmount - $totalCrAmount;
        return view('admin.ledger.share_holder', compact('ledgers', 'totalAmount'));
    }
}