<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use function App\CPU\translate;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    public function login()
    {
        return view('admin-views.auth.login');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        $remember_me = $request->has('remember') ? true : false;
        if (auth('admin')->attempt(['email' => $request->email, 'password' => $request->password], $remember_me)) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors([translate('Credentials does not match')]);
    }
    public function logout(Request $request)
    {
        auth()->guard('admin')->logout();
        return redirect()->route('admin.auth.login');
    }
}
