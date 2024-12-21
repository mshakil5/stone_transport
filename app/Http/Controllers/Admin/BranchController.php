<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    public function get_all_branch()
    {
      $branch = Branch::all();
  
  
          return Response::json($branch);
  
    }
  
  
    public function save_branch(Request $request)
    {
      $branch = new Branch();
      $branch->name = $request->branch;
      $branch->created_by = Auth::user()->id;
      $branch->save();
      return;
    }
  
    public function view_branch(Request $request)
    {
        
        if($request->ajax()){
            $branch = Branch::all();
            return Datatables::of($branch)->make(true);
        }
        return view("admin.branch.index");
    }
  
    public function published_branch($ID) {
  
      Branch::where('id', $ID)
      ->update(['status' => 1]);
  
      return ;
    }
  
  
    public function unpublished_branch($ID) {
      Branch::where('id', $ID)
        ->update(['status' => 0]);
  
        return ;
    }
  
    public function edit_branch(Request $request, $id)
    {
              $branch = Branch::where('id',$id)
                      ->update(['name' =>$request['data']['branchName']]);
  
              return;
    }
}
