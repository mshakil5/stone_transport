<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function login(Request $request)
    {
        $input = $request->all();
        $loginField = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where($loginField, $input['email'])->first();

        if ($user) {
            if ($user->status == 1) {
                if (auth()->attempt([$loginField => $input['email'], 'password' => $input['password']])) {
                    // session(['session_clear' => true]);
                    if (auth()->user()->is_type == '1') {
                        return redirect()->route('admin.dashboard');
                    } elseif (auth()->user()->is_type == '2') {
                        return redirect()->route('manager.dashboard');
                    } elseif (auth()->user()->is_type == '0') {
                        return redirect()->route('user.dashboard');
                    }
                } else {
                    return redirect()->back()->withInput($request->only('email'))->with('message', 'Wrong Password.');
                }
            } else {
                return redirect()->back()->withInput($request->only('email'))->with('message', 'Your ID is Deactivated.');
            }
        } else {
            return redirect()->back()->withInput($request->only('email'))->with('message', 'No account found with the provided credentials.');
        }
    }
}