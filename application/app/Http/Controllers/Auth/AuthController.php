<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company\BusinessType;
use App\Models\Company\Company;
use App\Models\Company\CompanyAddress;
use App\Models\UserCustomerRole;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\User;
use App\Models\UserManagement\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();
        if (! empty($user) && Hash::check($request->password, $user->password)) {
            $roleUser = UserRole::with('role', 'user', 'role.company', 'role.scope', 'role.company.address')->where('user_id', $user->id)->first();
            $access = collect($roleUser)->toArray();
            if ($roleUser->role->scope->code !== 'user_created') {
                unset($access['company']);
            }
            session()->flush();
            session(['userLogged' => $access, 'lifetime' => now()->addMinutes((int) env('SESSION_LIFETIME', 120))]);

            return redirect()->route('select-company');
        }

        return redirect()
            ->back()
            ->with('error', "Your provide <i><b>Username/Email/Phone Number</b></i> or <i><b>Password</b></i> dons't match to our record")
            ->withInput();
    }

    public function selectCompany(Request $request)
    {
        $roleUser = session('userLogged');
        $where = [['id', '=', $request->id]];
        if (! $roleUser['role']['scope']['code'] === 'user_created') {
            $where = [['id', '=', $request->id], ['user_id', '=', $roleUser['user_id']]];
        }
        $roleUser['company'] = Company::with('address')->where($where)->first()->toArray();
        $roleUser = collect($roleUser)->toArray();
        session(['userLogged' => $roleUser, 'lifetime' => now()->addMinutes((int) env('SESSION_LIFETIME', 120))]);

        return redirect()->route('dashboard.index');
    }

    public function loginAs($id)
    {
        $where = [
            'user_id' => $id,
            'company_id' => session('userLogged')['company']['id'],
        ];
        $user = UserCustomerRole::with('user', 'role')
            ->where($where)
            ->first()->toArray();
        if (! empty($user)) {
            $hasPrivileges = true;
            $user['company'] = UserCustomerRole::employeeCompany($user['user_id']);
            if (UserCustomerRole::employeeMenu($user['user_id']) === 0) {
                $hasPrivileges = false;
            }
            if ($hasPrivileges) {
                session()->flush();
                session(['userLogged' => collect($user)->toArray(), 'lifetime' => now()->addMinutes(env('SESSION_LIFETIME', 120))]);
                $response = ['message' => 'successfully login as '.$user['user']['username']];
                $status = 200;
            } else {
                $response = ['message' => 'failed login as '.$user['user']['username'].', please set menu for the role'];
                $status = 404;
            }
        } else {
            $response = ['message' => 'failed login as '.$user['user']['username'].', unexpected error on process login as'];
            $status = 404;
        }

        return response()->json($response, $status);
    }

    public function register(Request $request)
    {
        $types = BusinessType::all();
        if ($request->action) {
            [$managerId, $lifetime, $role_id, $secret] = explode('|', base64_decode($request->action));
            if ($secret != env('APP_SECRET') || empty(User::find($managerId)) || empty(Role::find($role_id)) || now()->format('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($lifetime))) {
                abort(401, 'Token invalid');
            }

            return view('auth.registration', compact('managerId', 'lifetime', 'role_id', 'types'));
        }

        return view('auth.registration', compact('types'));
    }

    public function registration(Request $request)
    {
        $request->validate([
            'user.name' => ['required', 'min:5', 'max:30'],
            'user.username' => ['required', 'min:8', 'max:15', 'unique:users,username'],
            'user.email' => ['required', 'email', 'unique:users,email'],
            'user.phone_number' => ['required', 'min:10', 'max:19', 'unique:users,phone_number', 'regex:/^\+628(-\d{3,4}){3,4}/s'],
            'user.password' => ['required', 'min:8', 'max:15', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/'],
            'company.name' => ['required', 'min:5', 'max:30'],
            'company.email' => ['required', 'email', 'unique:companies,email'],
            'company.phone_number' => ['required', 'min:10', 'max:19', 'unique:companies,phone_number', 'regex:/^\+628(-\d{3,4}){3,4}/s'],
            'address.place' => ['required', 'min:4', 'max:30'],
            'address.address' => ['required', 'min:4', 'max:30'],
            'address.city' => ['required', 'min:4', 'max:30'],
            'address.province' => ['required', 'min:4', 'max:30'],
            'address.zip_code' => ['required', 'min:4', 'max:30'],
        ], [
            'user.name.required' => 'The name field is required.',
            'user.name.min' => 'The name field must be at least 5 characters.',
            'user.name.max' => 'The name field must not be greater than 30 characters.',
            'user.username.required' => 'The username field is required.',
            'user.username.min' => 'The username field must be at least 8 characters.',
            'user.username.max' => 'The username field must not be greater than 15 characters.',
            'user.username.unique' => 'The username has already been taken.',
            'user.email.required' => 'The email field is required.',
            'user.email.unique' => 'The email has already been taken.',
            'user.phone_number.required' => 'The phone number field is required.',
            'user.phone_number.min' => 'The phone number field must be at least 10 characters.',
            'user.phone_number.max' => 'The phone number field must not be greater than 19 characters.',
            'user.phone_number.unique' => 'The phone number has already been taken.',
            'user.password.required' => 'The password field is required.',
            'user.password.min' => 'The password field must be at least 8 characters.',
            'user.password.max' => 'The password field must not be greater than 15 characters.',
            'user.password.regex' => 'The password field must mixed-case letters, numbers and symbols.',
            'company.name.required' => 'The company name field is required.',
            'company.name.min' => 'The company name field must be at least 4 characters.',
            'company.name.max' => 'The company name field must not be greater than 30 characters.',
            'company.email.required' => 'The company email field is required.',
            'company.email.min' => 'The company email field must be at least 4 characters.',
            'company.email.max' => 'The company email field must not be greater than 30 characters.',
            'company.email.unique' => 'The company email has already been taken.',
            'company.phone_number.required' => 'The company phone number field is required.',
            'company.phone_number.min' => 'The company phone number field must be at least 4 characters.',
            'company.phone_number.max' => 'The company phone number field must not be greater than 30 characters.',
            'company.phone_number.unique' => 'The company phone number has already been taken.',
            'business_id.required' => 'The business type is required.',
            'address.address.required' => 'The address is required.',
            'address.place.required' => 'The building is required.',
            'address.city.required' => 'The address city is required.',
            'address.province.required' => 'The address province is required.',
            'address.zip_code.required' => 'The address zip code is required.',
            'address.address.min' => 'The address must be at least 4 characters.',
            'address.place.min' => 'The building must be at least 4 characters.',
            'address.city.min' => 'The city must be at least 4 characters.',
            'address.province.min' => 'The province must be at least 4 characters.',
            'address.zip_code.min' => 'The zip code must be at least 4 characters.',
            'address.address.max' => 'The address must not be greater than 30 characters.',
            'address.place.max' => 'The building must not be greater than 30 characters.',
            'address.city.max' => 'The address city must not be greater than 30 characters.',
            'address.province.max' => 'The address province must not be greater than 30 characters.',
            'address.zip_code.max' => 'The address zip code must not be greater than 30 characters.',
        ]);
        DB::beginTransaction();
        try {
            $user = $request->only('user')['user'];
            $user['phone_number'] = unFormattedPhoneNumber($user['phone_number']);
            $company = $request->only('company')['company'];
            $company['phone_number'] = unFormattedPhoneNumber($company['phone_number']);
            $address = $request->only('address')['address'];
            if ($request->has('managerId')) {
                $dataUser = User::user_manager($request->managerId);
            }
            if ($request->has('role_id')) {
                $dataCustomerRole = Role::customer_roles($request->managerId, $request->role_id);
            }
            if ($request->has('managerId')) {
                $user_register = User::create($user);
                if (empty($dataUser) || empty($dataCustomerRole)) {
                    DB::rollBack();
                    abort(401, 'Unauthorized');
                } else {
                    UserCustomerRole::create([
                        'user_id' => $user_register->id,
                        'role_id' => $dataCustomerRole[0]->id,
                    ]);
                }
            } else {
                $user_register = User::create($user);
                $company['user_id'] = $user_register->id;
                $company['created_by'] = $user_register->id;
                $company['picture'] = str_replace(public_path('/'), '', getFilePathDisk('company/default-company.png', 'default'));
                $data_company = Company::create($company);
                $address['company_id'] = $data_company->id;
                $address['created_by'] = $user_register->id;
                CompanyAddress::create($address);
                $data_role = Role::where('code', 'root')->first()->toArray();
                unset($data_role['id']);
                $data_role['name'] = 'Your Default Manager Role';
                $data_role['code'] = 'root-'.str(buatSingkatan($data_company->name))->lower();
                $data_role['company_id'] = $data_company->id;
                $data_role['created_by'] = $user_register->id;
                $create_role = Role::create($data_role);
                UserRole::create([
                    'user_id' => $user_register->id,
                    'role_id' => $create_role->id,
                ]);
            }
            DB::commit();

            return redirect()->route('dashboard.index');
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withInput();
        }
    }

    public function logout()
    {
        session()->flush();

        return redirect()->route('dashboard.index');
    }

    public function customerCompany()
    {
        $where = [['user_id', '=', session('userLogged')['user']['id']]];
        if (getScope() === 'Developer') {
            $where = [['user_id', '<>', null]];
        }
        $data = Company::with('address', 'type')->where($where)->get();
        $code = 200;
        $response = ['message' => 'Showing resource successfully', 'data' => $data];
        if (empty($data)) {
            $code = 404;
            $response = ['message' => 'Failed showing resource', 'data' => $data];
        }

        return response()->json($response, $code);
    }

    public function requestChangePassword()
    {
        return view('auth.change-password');
    }

    public function requestActivateAccessPin()
    {
        return view('auth.activate-access-pin');
    }

    public function activateAccessPin(Request $request)
    {
        $currentPin = null;
        $where = ['id' => session('userLogged')['user']['id']];
        if ($request->has('current_access_pin')) {
            $where = ['id' => session('userLogged')['user']['id'], 'pin' => $currentPin];
        }
        $request->validate([
            'current_access_pin' => ['array', function ($attribute, $value, $fail) use ($currentPin) {
                $currentPin = implode($value);
                if (! User::where(['id' => session('userLogged')['user']['id'], 'pin' => Hash::make($currentPin)])->makeVisible(['pin'])->exists()) {
                    $fail("The {$attribute} not match to our records");
                }
            }],
            'current_access_pin.*' => 'nullable|numeric',
            'access_pin' => ['array', function ($attribute, $value, $fail) {
                if (count(array_filter($value, function ($val) {
                    return $val === null;
                })) === 6) {
                    $fail("The {$attribute} must be 6 digits.");
                }
            }],
            'access_pin.*' => 'required|numeric',
            'confirm_access_pin' => ['array', function ($attribute, $value, $fail) {
                if (count(array_filter($value, function ($val) {
                    return $val === null;
                })) === 6) {
                    $fail("The {$attribute} must be 6 digits.");
                }
            }],
            'confirm_access_pin.*' => 'required_with:access_pin|numeric|same:access_pin.*',
        ]);
        $message = ['success', 'Access Pin updated successfully'];
        DB::beginTransaction();
        try {
            if (! User::where($where)->update(['pin' => Hash::make(implode('', $request->access_pin))])) {
                $message = ['error', 'Unexpected error in our record, try again later.'];
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $message = ['error', 'Unexpected error in our system, try again later.'];
        }
        $roleUser = UserRole::with('role', 'user', 'role.company', 'role.scope', 'role.company.address')->where('user_id', session('userLogged')['user']['id'])->first();
        $roleUser['company'] = $roleUser->role->company;
        $roleUser['company']['address'] = $roleUser->role->company->address;
        $access = collect($roleUser)->toArray();
        session(['userLogged' => $access, 'lifetime' => now()->addMinutes((int) env('SESSION_LIFETIME', 120))]);

        return redirect()->route('dashboard.index')->with($message);
    }

    public function changeCompany()
    {
        $data = session('userLogged');
        unset($data['company']);
        session(['userLogged' => $data, 'lifetime' => now()->addMinutes((int) env('SESSION_LIFETIME', 120))]);

        return redirect()->route('dashboard.index');
    }

    public function unlockScreen(Request $request)
    {
        $request->validate([
            'access_pin' => ['array', function ($attribute, $value, $fail) {
                if (count(array_filter($value, function ($val) {
                    return $val === null;
                })) === 6) {
                    $fail("The {$attribute} must be 6 digits.");
                }
            }],
            'access_pin.*' => 'required|numeric',
        ]);
        if (Hash::check(implode('', $request->access_pin), session('userLogged')['user']['pin'])) {
            $dataSession = session()->all();
            $dataSession['lifetime'] = now()->addMinutes((int) env('SESSION_LIFETIME', 120));
            session($dataSession);
            $status = 200;
            $message = ['message' => 'lifetime extended successfully'];
        } else {
            $status = 422;
            $message = ['message' => 'failed extending lifetime'];
        }

        return response()->json($message, $status);
    }

    public function lockscreen(Request $request)
    {
        $dataSession = session()->all();
        $dataSession['lifetime'] = null;
        session($dataSession);
    }
}
