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
        $where = [['userId', '=', session('userLogged')->user->id]];
        if (getRole() === "Developer") {
            $where = [['userId', '<>', 0]];
        }
        $customer_roles = CustomerRole::exists_role($where);
        return view('man.user-customer', compact('users', 'customer_roles'));
    }
    public function generateRegistrationLink(Request $request)
    {

        $request->validate([
            'managerId' => 'required',
            'customerRoleId' => 'required',
        ], [
            'managerId.required' => 'The customer user field is required',
            'customerRoleId.required' => 'The customer user role field is required'
        ]);
        if (getRole() === "Developer") {
            $id = $request->managerId;
        } else {
            $id = session('userLogged')->user->id;
        }
        $role = $request->customerRoleId;
        $link = route('auth.registration') . '?action=' . base64_encode($id . '|' . now('Asia/Jakarta')->add($request->time_limit) . '|' . $role . '|' . env('APP_SECRET'));
        return response()->json(['message' => 'registration link created successfully', 'link' => $link]);
    }

    public function dataTable(Request $request)
    {
        $totalData = User::join('customer_roles as cr', 'cr.userId', '=', 'users.id')
            ->join('user_customer_roles as ucr', 'ucr.customerRoleId', '=', 'cr.id')
            ->join('users as uc', 'ucr.userId', '=', 'uc.id')
            ->orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = User::join('customer_roles as cr', 'cr.userId', '=', 'users.id')
                ->join('user_customer_roles as ucr', 'ucr.customerRoleId', '=', 'cr.id')
                ->join('users as uc', 'ucr.userId', '=', 'uc.id')
                ->select('uc.name', 'uc.phone_number', 'cr.name as role_name', 'uc.username', 'uc.id');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->get();
        } else {
            $assets = User::join('customer_roles as cr', 'cr.userId', '=', 'users.id')
                ->join('user_customer_roles as ucr', 'ucr.customerRoleId', '=', 'cr.id')
                ->join('users as uc', 'ucr.userId', '=', 'uc.id')
                ->select('uc.name', 'uc.phone_number', 'cr.name as role_name', 'uc.username', 'uc.id')
                ->where('uc.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('cr.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('uc.phone_number', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->get();

            $totalFiltered = User::join('customer_roles as cr', 'cr.userId', '=', 'users.id')
                ->join('user_customer_roles as ucr', 'ucr.customerRoleId', '=', 'cr.id')
                ->join('users as uc', 'ucr.userId', '=', 'uc.id')
                ->select('uc.name', 'uc.phone_number', 'cr.name as role_name', 'uc.username', 'uc.id')
                ->where('uc.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('cr.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('uc.phone_number', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['name'] = $item->name . '<br><small>(' . $item->username . ')</small>';
            $row['phone_number'] = formatIndonesianPhoneNumber($item->phone_number);
            $row['role'] = $item->role_name;
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-user-customer='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-user-customer='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
            'userId' => 'required',
            'name' => 'required|min:2|max:10|unique:customer_roles,name',
            'description' => 'required|min:6|max:100',
        ], ['userId.required' => 'The customer user field is required']);
        DB::beginTransaction();
        try {
            CustomerRole::create($request->except('_token'));
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
            'customerRoleId' => 'required',
        ]);
        DB::beginTransaction();
        try {
            User::where('id', $id)->update($request->except('_token', 'id', 'managerId', 'customerRoleId'));
            UserCustomerRole::where('userId', $id)->update($request->only('customerRoleId'));
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
