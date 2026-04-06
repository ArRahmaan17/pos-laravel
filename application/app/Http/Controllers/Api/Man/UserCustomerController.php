<?php

namespace App\Http\Controllers\Api\Man;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\User;
use App\Models\UserRole;
use App\Traits\ImageHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use ImageHandler;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $company_id = $user->current_company_id ?? $user->customerCompanies()->first()?->id;

        if (! $company_id) {
            return response()->json(['message' => 'No company selected'], 400);
        }

        $users = User::user_manager();
        $where = [['user_id', '=', $user->id]];
        $customer_roles = Role::where($where)->get();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => [
                'users' => $users,
                'customer_roles' => $customer_roles,
            ],
        ]);
    }

    public function generateRegistrationLink(Request $request)
    {
        $request->validate([
            'managerId' => 'required',
            'role_id' => 'required',
        ], [
            'managerId.required' => 'The customer user field is required',
            'role_id.required' => 'The customer user role field is required',
        ]);

        $user = $request->user();

        if (getScope() === 'global') {
            $id = $request->managerId;
        } else {
            $id = $user->id;
        }

        $role = $request->role_id;
        $link = url('/api/auth/register') . '?action=' . base64_encode($id . '|' . now()->add($request->time_limit) . '|' . $role . '|' . env('APP_SECRET'));

        return response()->json(['message' => 'registration link created successfully', 'link' => $link]);
    }

    public function dataTable(Request $request)
    {
        $user = $request->user();
        $company_id = $user->current_company_id ?? $user->customerCompanies()->first()?->id;

        if (! $company_id) {
            return response()->json(['message' => 'No company selected'], 400);
        }

        $totalData = UserRole::join('customer_roles as cr', 'cr.id', '=', 'user_roles.role_id')
            ->join('companies as cc', 'user_roles.company_id', '=', 'cc.id')
            ->join('users as u', 'user_roles.user_id', '=', 'u.id')
            ->select('u.name', 'u.phone_number', 'cr.name as role_name', 'u.username', 'u.id')
            ->where('cc.id', $company_id)
            ->orderBy('id', 'asc')
            ->count();

        $totalFiltered = $totalData;

        if (empty($request['search']['value'])) {
            $assets = UserRole::join('customer_roles as cr', 'cr.id', '=', 'user_roles.role_id')
                ->join('companies as cc', 'user_roles.company_id', '=', 'cc.id')
                ->join('users as u', 'user_roles.user_id', '=', 'u.id')
                ->select('u.name', 'u.phone_number', 'cr.name as role_name', 'u.username', 'u.id')
                ->where('cc.id', $company_id);

            if ($request['length'] != '-1') {
                $assets->limit($request['length']);
                if ($request['start'] > 0) {
                    $assets->where('u.id', '>', $request['start']);
                }
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->get();
        } else {
            $assets = UserRole::join('customer_roles as cr', 'cr.id', '=', 'user_roles.role_id')
                ->join('companies as cc', 'user_roles.company_id', '=', 'cc.id')
                ->join('users as u', 'user_roles.user_id', '=', 'u.id')
                ->where('cc.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('cr.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('cc.phone_number', 'like', '%' . $request['search']['value'] . '%')
                ->select('u.name', 'u.phone_number', 'cr.name as role_name', 'u.username', 'u.id')
                ->where('cc.id', $company_id);

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length']);
                if ($request['start'] > 0) {
                    $assets->where('u.id', '>', $request['start']);
                }
            }
            $assets = $assets->get();

            $totalFiltered = UserRole::join('customer_roles as cr', 'cr.id', '=', 'user_roles.role_id')
                ->join('companies as cc', 'user_roles.company_id', '=', 'cc.id')
                ->join('users as u', 'user_roles.user_id', '=', 'u.id')
                ->select('u.name', 'u.phone_number', 'cr.name as role_name', 'u.username', 'u.id')
                ->where('cc.id', $company_id)
                ->where('cc.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('cr.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('cc.phone_number', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->count();
        }

        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['name'] = $item->name;
            $row['username'] = $item->username;
            $row['phone_number'] = formatIndonesianPhoneNumber($item->phone_number);
            $row['role'] = $item->role_name;
            $row['id'] = $item->id;
            $dataFiltered[] = $row;
        }

        $response = [
            'draw' => $request['draw'],
            'recordsFiltered' => $totalFiltered,
            'recordsTotal' => count($dataFiltered),
            'data' => $dataFiltered,
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|unique:users,phone_number',
            'role_id' => 'required|exists:customer_roles,id',
        ]);

        $user = $request->user();
        $company_id = $user->current_company_id ?? $user->customerCompanies()->first()?->id;

        if (! $company_id) {
            return response()->json(['message' => 'No company selected'], 400);
        }

        DB::beginTransaction();
        try {
            $dataUser = $request->except('_token', 'id', 'managerId', 'role_id');
            $dataUser['password'] = defaultPassword();
            $user = User::create($dataUser);
            UserRole::create(['user_id' => $user->id, 'role_id' => $request->role_id, 'company_id' => $company_id]);
            $response = ['message' => 'Creating resources successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'Failed creating resources'];
            $code = 422;
        }

        return response()->json($response, $code);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Profile retrieved successfully',
            'data' => $user,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $data = UserRole::with('user', 'role')->where('user_id', $id)->get();
        $response = ['message' => 'Showing resource successfully', 'data' => $data];
        $code = 200;
        if (empty($data)) {
            $response = ['message' => 'Failed showing resource', 'data' => $data];
            $code = 404;
        }

        return response()->json($response, $code);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|unique:users,email,' . $id,
            'phone_number' => 'required|unique:users,phone_number,' . $id,
            'role_id' => 'required|exists:user_roles,id',
        ]);

        DB::beginTransaction();
        try {
            User::where('id', $id)->update($request->except('_token', 'id', 'managerId', 'role_id'));
            UserRole::where('user_id', $id)->update($request->only('role_id'));
            $response = ['message' => 'Updating resource successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'Failed updating resource'];
            $code = 422;
        }

        return response()->json($response, $code);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $id = $user->id;

        $request->validate([
            'name' => 'required|unique:users,name,' . $id,
            'phone_number' => 'required|unique:users,phone_number,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'username' => 'required|unique:users,username,' . $id,
            'profile_picture' => 'image|between:1,800|dimensions:ratio=1/1|mimes:png,jpg',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token');
            if ($request->profile_picture) {
                $filename = md5($request->name . now()->format('Y-m-d h:i:s')) . '.' . $request->file('profile_picture')->clientExtension();
                $data['profile_picture'] = $filename;
                $this->uploadAndWatermark($request->file('profile_picture'), '', 'customer-profile-picture', $filename);
            }
            $response = ['message' => 'Failed updating resource'];
            $code = 422;
            if (User::where('id', $id)->update($data)) {
                $response = ['message' => 'Updating resource successfully'];
                $code = 200;
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'Failed updating resource'];
            $code = 422;
        }

        return response()->json($response, $code);
    }

    public function generateAffiliateCode(Request $request)
    {
        $user = $request->user();
        $status = 400;
        $response = ['message' => 'failed generating affiliate code', 'data' => []];

        DB::beginTransaction();
        try {
            $affiliate_code = generateAffiliateCode();
            $update_affiliate = User::where('id', $user->id)->where('affiliate_code', null)->update(['affiliate_code' => $affiliate_code]);
            if ($update_affiliate) {
                $status = 200;
                $response = ['message' => 'successfully generating affiliate code', 'data' => ['affiliate_code' => $affiliate_code]];
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return response()->json($response, $status);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            UserRole::where('user_id', $id)->delete();
            User::find($id)->delete();
            DB::commit();
            $response = ['message' => 'deleting resource successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed deleting resource'];
            $code = 422;
        }

        return response()->json($response, $code);
    }
}
