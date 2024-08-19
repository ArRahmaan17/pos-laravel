<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AppRole;
use App\Models\User;
use App\Models\UserCustomerRole;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'min:8', 'max:15'],
            'password' => ['required', 'min:8', 'max:15']
        ]);
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->orWhere('phone_number', $request->username)
            ->first();
        if (!empty($user) && Hash::check($request->password, $user->password)) {
            $role = UserRole::with('user', 'role')->where('userId', $user->id)->first();
            if (empty($role)) {
                $role = UserCustomerRole::with('user', 'role')->where('userId', $user->id)->first();
            }
            session()->flush();
            session(['userLogged' => $role]);
            return redirect()->route('home');
        }
        return redirect()
            ->back()
            ->with('error', "Your provide <i><b>Username/Email/Phone Number</b></i> or <i><b>Password</b></i> dons't match to our record")
            ->withInput();
    }
    public function register()
    {
        return view('auth.registration.index');
    }
    public function registration(Request $request)
    {
        $request->validate([
            'name' => ['required', 'min:5', 'max:30'],
            'username' => ['required', 'min:8', 'max:15'],
            'email' => ['required', 'email', 'unique:users,email,id'],
            'phone_number' => ['required', 'min:10', 'max:13', 'unique:users,phone_number,id'],
            'password' => ['required', 'min:8', 'max:15', 'regex:/^\*[^\w\s]\S{8,16}$/']
        ], ['password.regex' => 'The password field must mixed-case letters, numbers and symbols']);
    }
}
