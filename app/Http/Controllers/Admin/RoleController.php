<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {
        return view("admin.role.index");
    }

    public function store(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $role = new Role();
        $role->name = $request->name;
        $role->permission = json_encode($request->permission);
        $role->created_by = Auth::user()->id;
        if($role->save()){
        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Role Created Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        return response()->json(['status'=> 303,'message'=>'Server Error!!']);
    }

    public function edit($id)
    {
        $data = Role::where('id',$id)->first();
        return view("admin.role.edit",compact('data'));
    }

    public function update(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $role = Role::find($request->id);
        $role->name = $request->name;
        $role->permission = json_encode($request->permission);
        $role->updated_by = Auth::user()->id;
        if($role->save()){
        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Role Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        return response()->json(['status'=> 303,'message'=>'Server Error!!']);
    }

}
