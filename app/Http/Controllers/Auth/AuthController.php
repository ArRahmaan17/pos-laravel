<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use App\Models\CompanyAddress;
use App\Models\CustomerCompany;
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
            'password' => ['required', 'min:8', 'max:15'],
        ]);
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->orWhere('phone_number', $request->username)
            ->first();
        if (! empty($user) && Hash::check($request->password, $user->password)) {
            $role = UserRole::with('user', 'role')->where('userId', $user->id)->first()->toArray();
            if (empty($role) || empty($role['user']) || empty($role['role'])) {
                $role = UserCustomerRole::with('user', 'role')->where('userId', $user->id)->first()->toArray();
            }
            if (! in_array($role['role']['name'], ['Developer', 'Manager'])) {
                $role['company'] = UserCustomerRole::employeeCompany($role['userId']);
            }
            session()->flush();
            session(['userLogged' => $role]);

            return redirect()->route('select-customer-company');
        }

        return redirect()
            ->back()
            ->with('error', "Your provide <i><b>Username/Email/Phone Number</b></i> or <i><b>Password</b></i> dons't match to our record")
            ->withInput();
    }

    public function selectCompany(Request $request)
    {
        $data = session('userLogged');
        $where = [['id', '=', $request->id]];
        if (in_array($data['role']['name'], ['Manager'])) {
            $where = [['id', '=', $request->id], ['userId', '=', $data['userId']]];
        }
        $data['company'] = CustomerCompany::with('address')->where($where)->first()->toArray();
        session()->flush();
        session(['userLogged' => $data]);

        return redirect()->route('home');
    }

    public function register(Request $request)
    {
        $types = BusinessType::all();
        if ($request->action) {
            [$managerId, $lifetime, $roleId, $secret] = explode('|', base64_decode($request->action));
            if ($secret != env('APP_SECRET') || empty(User::find($managerId)) || empty(CustomerRole::find($roleId)) || now('Asia/Jakarta')->format('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($lifetime))) {
                abort(401, 'Token invalid');
            }

            return view('auth.registration.index', compact('managerId', 'lifetime', 'roleId', 'types'));
        }

        return view('auth.registration.index', compact('types'));
    }

    public function registration(Request $request)
    {
        $request->validate([
            'user.name' => ['required', 'min:5', 'max:30'],
            'user.username' => ['required', 'min:8', 'max:15', 'unique:users,username'],
            'user.email' => ['required', 'email', 'unique:users,email'],
            'user.phone_number' => ['required', 'min:10', 'max:19', 'unique:users,phone_number'],
            'user.password' => ['required', 'min:8', 'max:15', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/'],
            'company.name' => ['required', 'min:5', 'max:30'],
            'company.email' => ['required', 'email', 'unique:customer_companies,email'],
            'company.phone_number' => ['required', 'min:10', 'max:19', 'unique:customer_companies,phone_number'],
            'address.place' => ['required', 'min:4', 'max:30'],
            'address.address' => ['required', 'min:4', 'max:30'],
            'address.city' => ['required', 'min:4', 'max:30'],
            'address.province' => ['required', 'min:4', 'max:30'],
            'address.zipCode' => ['required', 'min:4', 'max:30'],
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
            'businessId.required' => 'The business type is required.',
            'address.address.required' => 'The address is required.',
            'address.place.required' => 'The building is required.',
            'address.city.required' => 'The address city is required.',
            'address.province.required' => 'The address province is required.',
            'address.zipCode.required' => 'The address zip code is required.',
            'address.address.min' => 'The address must be at least 4 characters.',
            'address.place.min' => 'The building must be at least 4 characters.',
            'address.city.min' => 'The city must be at least 4 characters.',
            'address.province.min' => 'The province must be at least 4 characters.',
            'address.zipCode.min' => 'The zip code must be at least 4 characters.',
            'address.address.max' => 'The address must not be greater than 30 characters.',
            'address.place.max' => 'The building must not be greater than 30 characters.',
            'address.city.max' => 'The address city must not be greater than 30 characters.',
            'address.province.max' => 'The address province must not be greater than 30 characters.',
            'address.zipCode.max' => 'The address zip code must not be greater than 30 characters.',
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
            if ($request->has('roleId')) {
                $dataCustomerRole = CustomerRole::customer_roles($request->managerId, $request->roleId);
            }
            if ($request->has('managerId')) {
                $user_register = User::create($user);
                if (empty($dataUser) || empty($dataCustomerRole)) {
                    DB::rollBack();
                    abort(401, 'Unauthorized');
                } else {
                    UserCustomerRole::create([
                        'userId' => $user_register->id,
                        'roleId' => $dataCustomerRole[0]->id,
                    ]);
                }
            } else {
                $user['hr'] = 1;
                $user_register = User::create($user);
                UserRole::create([
                    'userId' => $user_register->id,
                    'roleId' => 2,
                ]);
                $company['affiliate_code'] = generateAffiliateCode();
                $company['userId'] = $user_register->id;
                $company['picture'] = 'default-picture.png';
                $data_company = CustomerCompany::create($company);
                $address['companyId'] = $data_company->id;
                CompanyAddress::create($address);
            }
            DB::commit();
            return redirect()->route('home');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back();
        }
    }

    public function logout()
    {
        session()->flush();

        return redirect()->route('home');
    }

    public function customerCompany()
    {
        $where = [['userId', '=', session('userLogged')['user']['id']]];
        if (getRole() === 'Developer') {
            $where = [['userId', '<>', null]];
        }
        $data = CustomerCompany::with('address', 'type')->where($where)->get();
        $code = 200;
        $response = ['message' => 'Showing resource successfully', 'data' => $data];
        if (empty($data)) {
            $code = 404;
            $response = ['message' => 'Failed showing resource', 'data' => $data];
        }

        return response()->json($response, $code);
    }

    public function changeCompany()
    {
        $data = session('userLogged');
        unset($data['company']);
        session(['userLogged' => $data]);

        return redirect()->route('home');
    }
}
