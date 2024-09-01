<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomerRole;
use App\Models\User;
use App\Models\UserCustomerRole;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            if (empty($role) || empty($role->user) || empty($role->role)) {
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
    public function register(Request $request)
    {
        if ($request->action) {
            [$managerId, $lifetime, $customerRoleId, $secret] = explode('|', base64_decode($request->action));
            if ($secret != env('APP_SECRET') || empty(User::find($managerId)) || empty(CustomerRole::find($customerRoleId)) || now('Asia/Jakarta')->format('Y-m-d H:i:s') >  date('Y-m-d H:i:s', strtotime($lifetime))) {
                abort(401, "Token invalid");
            }
            return view('auth.registration.index', compact('managerId', 'lifetime', 'customerRoleId'));
        }
        return view('auth.registration.index',);
    }
    public function registration(Request $request)
    {
        $request->validate([
            'name' => ['required', 'min:5', 'max:30'],
            'username' => ['required', 'min:8', 'max:15'],
            'email' => ['required', 'email', 'unique:users,email,id'],
            'phone_number' => ['required', 'min:10', 'max:19', 'unique:users,phone_number,id'],
            'password' => ['required', 'min:8', 'max:15', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/'],
        ], ['password.regex' => 'The password field must mixed-case letters, numbers and symbols']);
        DB::beginTransaction();
        try {
            $data = $request->except('_token', 'customerRoleId', 'managerId');
            $data['phone_number'] = unFormattedPhoneNumber($data['phone_number']);
            if ($request->has('managerId')) {
                $dataUser = User::user_manager($request->managerId);
            }
            if ($request->has('customerRoleId')) {
                $dataCustomerRole = CustomerRole::customer_roles($request->managerId, $request->customerRoleId);
            }
            $user_register = User::create($data);
            if (empty($dataUser) || empty($dataCustomerRole)) {
                abort(401, 'Unauthorized');
            } else {
                UserCustomerRole::create([
                    'userId' => $user_register->id,
                    'customerRoleId' => $dataCustomerRole[0]->id
                ]);
            }
            DB::commit();
            return redirect()->route('home');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back();
            //throw $th;
        }
    }
}
