<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use App\Models\CompanyAddress;
use App\Models\CustomerCompany;
use App\Models\CustomerRole;
use App\Models\User;
use App\Models\UserCustomerRole;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
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
            $role = UserRole::with('user', 'role')->where('userId', $user->id)->first();
            if (empty($role) || empty($role->user) || empty($role->role)) {
                $role = UserCustomerRole::with('user', 'role')->where('userId', $user->id)->first();
            }

            if (! $role) {
                throw ValidationException::withMessages([
                    'username' => ['User has no assigned role.'],
                ]);
            }

            $userData = [
                'user' => $role->user->toArray(),
                'role' => $role->role->toArray(),
                'userId' => $user->id,
            ];

            if (! in_array($role->role->name, ['Developer', 'Manager'])) {
                $company = UserCustomerRole::employeeCompany($role->userId);
                if ($company) {
                    $company['address'] = CompanyAddress::where('companyId', $company['id'])->first()?->toArray();
                    $userData['company'] = $company;
                }
            }

            // Create Sanctum token
            $token = $user->createToken('auth-token', [strtolower($role->role->name)], now()->addWeek())->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $userData,
                'token_type' => 'Bearer',
            ], 200);
        }

        throw ValidationException::withMessages([
            'username' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ], 200);
    }

    public function logoutAll(Request $request)
    {
        // Revoke all tokens for the current user
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out from all devices',
        ], 200);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $role = UserRole::with('user', 'role')->where('userId', $user->id)->first();

        if (empty($role) || empty($role->user) || empty($role->role)) {
            $role = UserCustomerRole::with('user', 'role')->where('userId', $user->id)->first();
        }

        if (! $role) {
            return response()->json([
                'message' => 'User has no assigned role',
            ], 404);
        }

        $userData = [
            'user' => $role->user->toArray(),
            'role' => $role->role->toArray(),
            'userId' => $user->id,
        ];

        if (! in_array($role->role->name, ['Developer', 'Manager'])) {
            $company = UserCustomerRole::employeeCompany($role->userId);
            if ($company) {
                $company['address'] = CompanyAddress::where('companyId', $company['id'])->first()?->toArray();
                $userData['company'] = $company;
            }
        }

        return response()->json([
            'message' => 'User data retrieved successfully',
            'data' => $userData,
        ], 200);
    }

    public function selectCompany(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:customer_companies,id',
        ]);

        $user = $request->user();
        $role = UserRole::with('user', 'role')->where('userId', $user->id)->first();

        if (empty($role) || empty($role->user) || empty($role->role)) {
            $role = UserCustomerRole::with('user', 'role')->where('userId', $user->id)->first();
        }

        if (! $role) {
            return response()->json([
                'message' => 'User has no assigned role',
            ], 404);
        }

        $where = [['id', '=', $request->company_id]];
        if (in_array($role->role->name, ['Manager'])) {
            $where = [['id', '=', $request->company_id], ['userId', '=', $user->id]];
        }

        $company = CustomerCompany::with('address')->where($where)->first();

        if (! $company) {
            return response()->json([
                'message' => 'Company not found or access denied',
            ], 404);
        }

        return response()->json([
            'message' => 'Company selected successfully',
            'data' => [
                'user' => $role->user->toArray(),
                'role' => $role->role->toArray(),
                'userId' => $user->id,
                'company' => $company->toArray(),
            ],
        ], 200);
    }

    public function loginAs(Request $request, $id)
    {
        $request->validate([
            'company_id' => 'required|exists:customer_companies,id',
        ]);

        $user = $request->user();
        $where = [
            'userId' => $id,
            'companyId' => $request->company_id,
        ];

        $targetUser = UserCustomerRole::with('user', 'role')
            ->where($where)
            ->first();

        if (empty($targetUser)) {
            return response()->json([
                'message' => 'User not found in this company',
            ], 404);
        }

        $hasPrivileges = true;
        $targetUserData = $targetUser->toArray();
        $targetUserData['company'] = UserCustomerRole::employeeCompany($targetUser->userId);

        if (UserCustomerRole::employeeMenu($targetUser->userId) == 0) {
            $hasPrivileges = false;
        }

        if ($hasPrivileges) {
            // Create a new token for the target user
            $targetUserModel = User::find($id);
            $token = $targetUserModel->createToken('login-as-token')->plainTextToken;

            return response()->json([
                'message' => 'Successfully logged in as ' . $targetUser->user->username,
                'token' => $token,
                'user' => $targetUserData,
                'token_type' => 'Bearer',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to login as ' . $targetUser->user->username . ', please set role for the user',
            ], 403);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'user.name' => ['required', 'min:5', 'max:30'],
            'user.username' => ['required', 'min:8', 'max:15', 'unique:users,username'],
            'user.email' => ['required', 'email', 'unique:users,email'],
            'user.phone_number' => ['required', 'min:10', 'max:19', 'unique:users,phone_number', 'regex:/8\d{10,11}$/'],
            'user.password' => ['required', 'min:8', 'max:15', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,15}$/'],
            'user.confirm_password' => ['same:user.password', 'required', 'min:8', 'max:15', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,15}$/'],
            'company.name' => ['required', 'min:5', 'max:30'],
            'company.email' => ['required', 'email', 'unique:customer_companies,email'],
            'company.phone_number' => ['required', 'min:10', 'max:19', 'unique:customer_companies,phone_number', 'regex:/8\d{10,11}$/'],
            'address.place' => ['required', 'min:4', 'max:30'],
            'address.address' => ['required', 'min:4', 'max:30'],
            'address.city' => ['required', 'min:4', 'max:30'],
            'address.province' => ['required', 'min:4', 'max:30'],
            'address.zipCode' => ['required', 'min:4', 'max:30'],
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

                    return response()->json([
                        'message' => 'Unauthorized registration attempt',
                    ], 401);
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

            return response()->json([
                'message' => 'Registration successful',
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Registration failed',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'min:8', 'max:15', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/'],
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Password changed successfully',
        ], 200);
    }

    public function activateAccessPin(Request $request)
    {
        $request->validate([
            'current_access_pin' => 'required|string|size:6',
            'access_pin' => ['required', 'string', 'size:6'],
            'confirm_access_pin' => 'required|string|size:6|same:access_pin',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_access_pin, $user->pin)) {
            throw ValidationException::withMessages([
                'current_access_pin' => ['The current access pin is incorrect.'],
            ]);
        }

        $user->update([
            'pin' => Hash::make($request->access_pin),
        ]);

        return response()->json([
            'message' => 'Access pin updated successfully',
        ], 200);
    }

    public function unlockScreen(Request $request)
    {
        $request->validate([
            'access_pin' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if (Hash::check($request->access_pin, $user->pin)) {
            return response()->json([
                'message' => 'Screen unlocked successfully',
            ], 200);
        }

        throw ValidationException::withMessages([
            'access_pin' => ['The access pin is incorrect.'],
        ]);
    }

    public function customerCompany(Request $request)
    {
        $user = $request->user();
        $where = [['userId', '=', $user->id]];

        $role = UserRole::with('user', 'role')->where('userId', $user->id)->first();
        if (empty($role) || empty($role->user) || empty($role->role)) {
            $role = UserCustomerRole::with('user', 'role')->where('userId', $user->id)->first();
        }

        if ($role && $role->role->name === 'Developer') {
            $where = [['userId', '<>', null]];
        }

        $data = CustomerCompany::with('address', 'type')->where($where)->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'No companies found',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => 'Companies retrieved successfully',
            'data' => $data,
        ], 200);
    }

    public function checkAvailableUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:5| max:30',
            'username' => 'required|string|max:25|min:5|unique:users,name',
            'email' => 'required|string|unique:users,email|email',
            'phone_number' => 'required|string|unique:users,phone_number|regex:/8\d{10,11}$/',
        ]);

        $checkUser = User::where([
            'username' => $request->username,
        ])->orWhere(function (Builder $query) use ($request) {
            $query->where('email', $request->email)->where('phone_number', $request->phone_number);
        })->count();

        if ($checkUser >= 1) {
            return response()->json([
                'message' => 'User already exists',
            ], 422);
        }

        return response()->json([
            'message' => 'User still available',
        ], 200);
    }

    public function companyTypes()
    {
        $allCompanyTypes = BusinessType::orderBy('name')->get();

        if ($allCompanyTypes->isEmpty()) {
            return response()->json([
                'message' => 'No company types found',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => 'Company types retrieved successfully',
            'data' => $allCompanyTypes,
        ], 200);
    }

    public function checkAvailableCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:customer_companies,name',
            'email' => 'required|string|unique:customer_companies,email|email',
            'phone_number' => 'required|string|unique:customer_companies,phone_number|regex:/8\d{10,11}$/',
            'businessId' => 'required|exists:business_types,id',
        ]);

        $checkCompany = CustomerCompany::where([
            'name' => $request->name,
        ])->orWhere(function (Builder $query) use ($request) {
            $query->where('email', $request->email)->where('phone_number', $request->phone_number);
        })->count();

        if ($checkCompany >= 1) {
            return response()->json([
                'message' => 'Company already exists',
            ], 422);
        }

        return response()->json([
            'message' => 'Company still available',
        ], 200);
    }
}
