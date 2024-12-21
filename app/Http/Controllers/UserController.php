<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function userProfile()
    {
        $user = auth()->user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {

        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->email)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Email \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->phone) ||!preg_match('/^\d{11}$/', $request->phone)){
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill a valid Phone field with exactly 11 digits.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if ($request->password !== $request->confirm_password) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Passwords don't match.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        if ($request->password !== $request->confirm_password) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Passwords don't match.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        $duplicateEmail = User::where('email', $request->email)->where('id', '!=', auth()->id())->first();
        if ($duplicateEmail) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This email is already in use.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        $user = User::find(auth()->id());
        if (!$user) {
            return response()->json(['status' => 303, 'message' => 'User not found.']);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->nid = $request->nid;
        $user->house_number = $request->house_number;
        $user->street_name = $request->street_name;
        $user->town = $request->town;
        $user->postcode = $request->postcode;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        
        if ($user->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Updated Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            return response()->json(['status' => 303, 'message' => 'Server Error!!']);
        }
    }
}
