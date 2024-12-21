<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;

class UserController extends Controller
{
    public function userDetails()
    {
        $data = User::where('id', Auth::user()->id)->first();
        $success['data'] = $data;
        return response()->json(['success'=>true,'response'=> $success], 200);
    }

    public function userProfileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
            'phone' => 'required|regex:/^\d{11}$/',
            'nid' => 'required|string|max:255',
            'house_number' => 'required|string|max:255',
            'street_name' => 'required|string|max:255',
            'town' => 'required|string|max:255',
            'postcode' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ], [
            'phone.regex' => 'The phone number must be exactly 11 digits.',
            'email.unique' => 'The email has already been taken.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $user = User::find($request->user()->id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->nid = $request->nid;
        $user->house_number = $request->house_number;
        $user->street_name = $request->street_name;
        $user->town = $request->town;
        $user->postcode = $request->postcode;
        $user->address = $request->address;
        $user->save();

        return response()->json(['message' => 'Profile updated successfully.', 'user' => $user], 200);
    }

    public function getOrders()
    {
        $orders = Order::where('user_id', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->get();

        return response()->json($orders, 200);
    }

    public function orderDetails($orderId)
    {
        $order = Order::with(['user', 'orderDetails.product'])
            ->where('id', $orderId)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        return response()->json($order, 200);
    }
}
