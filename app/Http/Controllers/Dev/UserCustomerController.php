<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::user_manager();
        return view('dev.user-customer', compact('users'));
    }
    public function generateRegistrationLink(Request $request)
    {
        $id = session('userLogged')->user->id;
        if (getRole() === "Developer") {
            $id = $request->userId;
        }
        $role = $request->customerRoleId;
        $link = route('auth.registration') . '?action=' . $id . '|' . now('Asia/Jakarta')->add($request->time_limit) . '|' . $role . '|' . env('APP_SECRET');
        return response()->json(['message' => 'registration link created successfully', 'link' => $link]);
    }

    public function dataTable(Request $request)
    {
        $totalData = User::join('customer_roles as cr', 'cr.userId', '=', 'users.id')
            ->join('user_customer_roles as ucr', 'ucr.customerRoleId', '=', 'cr.id')->orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = User::join('customer_roles as cr', 'cr.userId', '=', 'users.id')
                ->join('user_customer_roles as ucr', 'ucr.customerRoleId', '=', 'cr.id')->select('users.name', 'users.phone_number', 'cr.name as role_name', 'users.username');

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
                ->join('user_customer_roles as ucr', 'ucr.customerRoleId', '=', 'cr.id')->select('users.name', 'users.phone_number', 'cr.name as role_name', 'users.username')
                ->where('users.name', 'ilike', '%' . $request['search']['value'] . '%')
                ->where('users.phone_number', 'ilike', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->get();

            $totalFiltered = User::join('customer_roles as cr', 'cr.userId', '=', 'users.id')
                ->join('user_customer_roles as ucr', 'ucr.customerRoleId', '=', 'cr.id')->select('users.name', 'users.phone_number', 'cr.name as role_name', 'users.username')
                ->where('users.name', 'ilike', '%' . $request['search']['value'] . '%')
                ->where('users.phone_number', 'ilike', '%' . $request['search']['value'] . '%');

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
            $row['phone_number'] = formatIndonesianPhoneNumber('+62' . $item->phone_number);
            $row['role'] = $item->role_name;
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-customer-role='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-customer-role='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
        $data = CustomerRole::find($id);
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
            'userId' => 'required',
            'name' => 'required|unique:app_roles,name,' . $id,
            'description' => 'required|min:6|max:100',
        ]);
        DB::beginTransaction();
        try {
            CustomerRole::find($id)->update($request->except('_token', 'id'));
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
            if (empty(collect(CustomerRole::with('role_users')->find($id)->role_users)->toArray())) {
                CustomerRole::destroy($id);
                DB::commit();
                $response = ['message' => 'deleting resource successfully'];
                $code = 200;
            } else {
                $response = ['message' => "Failed deleting resource. This data is still being used in other data. You can't delete it until it's removed from those data"];
                $code = 422;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed deleting resource'];
            $code = 422;
        }
        return response()->json($response, $code);
    }
}
