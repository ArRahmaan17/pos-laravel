<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerRole;
use App\Models\User;
use App\Models\UserCustomerRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::user_manager();
        $where = [['userId', '=', session('userLogged')['company']['userId']]];
        $customer_roles = CustomerRole::where($where)->get();

        return view('man.customer-user', compact('users', 'customer_roles'));
    }

    public function generateRegistrationLink(Request $request)
    {
        $request->validate([
            'managerId' => 'required',
            'roleId' => 'required',
        ], [
            'managerId.required' => 'The customer user field is required',
            'roleId.required' => 'The customer user role field is required',
        ]);
        if (getRole() === 'Developer') {
            $id = $request->managerId;
        } else {
            $id = session('userLogged')['user']['id'];
        }
        $role = $request->roleId;
        $link = route('auth.registration') . '?action=' . base64_encode($id . '|' . now('Asia/Jakarta')->add($request->time_limit) . '|' . $role . '|' . env('APP_SECRET'));

        return response()->json(['message' => 'registration link created successfully', 'link' => $link]);
    }

    public function dataTable(Request $request)
    {
        $totalData = UserCustomerRole::join('customer_roles as cr', 'cr.id', '=', 'user_customer_roles.roleId')
            ->join('customer_companies as cc', 'user_customer_roles.companyId', '=', 'cc.id')
            ->join('users as u', 'user_customer_roles.userId', '=', 'u.id')
            ->select('u.name', 'u.phone_number', 'cr.name as role_name', 'u.username', 'u.id')
            ->orderBy('id', 'asc')
            ->where('cc.name', session('userLogged')['company']['name'])
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = UserCustomerRole::join('customer_roles as cr', 'cr.id', '=', 'user_customer_roles.roleId')
                ->join('customer_companies as cc', 'user_customer_roles.companyId', '=', 'cc.id')
                ->join('users as u', 'user_customer_roles.userId', '=', 'u.id')
                ->select('u.name', 'u.phone_number', 'cr.name as role_name', 'u.username', 'u.id');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->where('cc.name', session('userLogged')['company']['name'])->get();
        } else {
            $assets = UserCustomerRole::join('customer_roles as cr', 'cr.id', '=', 'user_customer_roles.roleId')
                ->join('customer_companies as cc', 'user_customer_roles.companyId', '=', 'cc.id')
                ->join('users as u', 'user_customer_roles.userId', '=', 'u.id')
                ->where('cc.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('cr.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('cc.phone_number', 'like', '%' . $request['search']['value'] . '%')
                ->select('u.name', 'u.phone_number', 'cr.name as role_name', 'u.username', 'u.id');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where('cc.name', session('userLogged')['company']['name'])->get();

            $totalFiltered = UserCustomerRole::join('customer_roles as cr', 'cr.id', '=', 'user_customer_roles.roleId')
                ->join('customer_companies as cc', 'user_customer_roles.companyId', '=', 'cc.id')
                ->join('users as u', 'user_customer_roles.userId', '=', 'u.id')
                ->select('u.name', 'u.phone_number', 'cr.name as role_name', 'u.username', 'u.id')
                ->where('cc.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('cr.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('cc.phone_number', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where('cc.name', session('userLogged')['company']['name'])->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['name'] = $item->name . '<br><small>(' . $item->username . ')</small>';
            $row['phone_number'] = formatIndonesianPhoneNumber($item->phone_number);
            $row['role'] = $item->role_name;
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-user-customer='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-user-customer='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>" . (in_array(getRole(), ['Developer', 'Manager']) ? '<button class="btn btn-icon btn-info login-as" data-user-customer="' . $item->id . '"><i class="bx bx-log-in"></i></button>' : '');
            $dataFiltered[] = $row;
        }
        $response = [
            'draw' => $request['draw'],
            'recordsFiltered' => $totalFiltered,
            'recordsTotal' => count($dataFiltered),
            'aaData' => $dataFiltered,
        ];

        return Response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users,name',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|unique:users,phone_number',
            'roleId' => 'required|exists:customer_roles,id',
        ]);
        DB::beginTransaction();
        try {
            $dataUser = $request->except('_token', 'id', 'managerId', 'roleId');
            $dataUser['password'] = defaultPassword();
            $user = User::create($dataUser);
            UserCustomerRole::create(['userId' => $user->id, 'roleId' => $request->roleId, 'companyId' => session('userLogged')['company']['id']]);
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

    public function profile()
    {
        return view('man.customer-user-profile');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = UserCustomerRole::with('user', 'role')->where('userId', $id)->get();
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
            'name' => 'required|unique:users,name,' . $id,
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|unique:users,email,' . $id,
            'phone_number' => 'required|unique:users,phone_number,' . $id,
            'roleId' => 'required|exists:user_customer_roles,id',
        ]);
        DB::beginTransaction();
        try {
            User::where('id', $id)->update($request->except('_token', 'id', 'managerId', 'roleId'));
            UserCustomerRole::where('userId', $id)->update($request->only('roleId'));
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
        $id = session('userLogged')['userId'];
        $request->validate([
            'name' => 'required|unique:users,name,' . $id,
            'phone_number' => 'required|unique:users,phone_number,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'username' => 'required|unique:users,username,' . $id,
        ]);
        DB::beginTransaction();
        try {
            User::where('id', $id)->update($request->except('_token'));
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
    public function generateAffiliateCode()
    {
        $status = 400;
        $response = ['message' => 'failed generating affiliate code', 'data' => []];
        DB::beginTransaction();
        try {
            $affiliate_code = generateAffiliateCode();
            $update_affiliate = User::where('id', session('userLogged')['userId'])->where('affiliate_code', null)->update(['affiliate_code' => $affiliate_code]);
            if ($update_affiliate) {
                $dataSession = session('userLogged');
                $dataSession['user']['affiliate_code'] = $affiliate_code;
                session(['userLogged' => $dataSession]);
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
            UserCustomerRole::where('userId', $id)->delete();
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
