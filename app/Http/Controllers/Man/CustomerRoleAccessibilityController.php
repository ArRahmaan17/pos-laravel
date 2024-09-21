<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\AppMenu;
use App\Models\CustomerRole;
use App\Models\CustomerRoleAccessibility;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerRoleAccessibilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::user_manager();
        $menus = AppMenu::customer_menu();
        $roles = CustomerRole::with(['role_users'])->get();
        return view('man.customer-role-accessibility', compact('users', 'menus', 'roles'));
    }

    public function dataTable(Request $request)
    {
        $where = [['customer_roles.userId', '=', session('userLogged')['user']['id']]];
        if (getRole() === "Developer") {
            $where = [['customer_roles.userId', '<>', 0]];
        }
        $totalData = CustomerRole::select('customer_roles.*')
            ->where($where)
            ->orderBy('customer_roles.id', 'asc')
            ->groupBy('customer_roles.id')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerRole::with('role_menus')->join('customer_role_accessibilities as cra', 'customer_roles.id', '=', 'cra.roleId')
                ->select('customer_roles.*');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->where($where)->groupBy('customer_roles.id')->get();
        } else {
            $assets = CustomerRole::with('role_menus')->join('customer_role_accessibilities as cra', 'customer_roles.id', '=', 'cra.roleId')
                ->select('customer_roles.*')
                ->where('customer_roles.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('customer_roles.description', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where($where)->groupBy('customer_roles.id')->get();

            $totalFiltered = CustomerRole::select('customer_roles.*')
                ->where('customer_roles.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('customer_roles.description', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where($where)->groupBy('customer_roles.id')->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['name'] = $item->name;
            $row['menu'] = $item->role_menus;
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-customer-role-accessibility='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-customer-role-accessibility='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
            'roleId' => 'required',
            'userId' => 'required',
            'menuId' => 'required|array',
        ], [
            'roleId.required' => 'The role field is required',
            'userId.required' => 'The manager field is required',
            'menuId.required' => 'The menu field is required',
        ]);
        DB::beginTransaction();
        try {
            if (CustomerRole::where(['id' => $request->roleId, 'userId' => $request->userId])->count() != 0) {
                $data_menu = [];
                foreach ($request->menuId as $index => $menu) {
                    $data_menu[] = [
                        'roleId' => $request->roleId,
                        'menuId' => $menu,
                        'created_at' => now('Asia/jakarta'),
                        'updated_at' => now('Asia/jakarta'),
                    ];
                }
                CustomerRoleAccessibility::insert($data_menu);
            } else {
                throw new Exception("Role mismatch to our record", 422);
            }
            DB::commit();
            $response = ['message' => 'Creating resources successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'Failed creating resources'];
            if ($th->getCode() == 422) {
                $response = ['message' => $th->getMessage()];
            }
            $code = 422;
        }
        return response()->json($response, $code);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role_menu = CustomerRole::select('*', 'id as roleId')->with(['role_menus' => function ($query) {
            $query->select(['*']);
        }])->where('id', $id)->first();
        $response = ['message' => "Show resources successfully", 'data' => $role_menu];
        $code = 200;
        if (empty($role_menu->role_menus)) {
            $response = ['message' => "Failed show resources", 'data' => $role_menu];
            $code = 404;
        }
        return response()->json($response, $code);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(
            [
                'roleId' => 'required',
                'userId' => 'required',
                'menuId' => 'required|array',
            ]
        );
        DB::beginTransaction();
        try {
            if (CustomerRole::where(['id' => $request->roleId, 'userId' => $request->userId])->count() != 0) {
                CustomerRoleAccessibility::where('roleId', $id)->delete();
                $data_menu = [];
                foreach ($request->menuId as $index => $menu) {
                    $data_menu[] = [
                        'roleId' => $request->roleId,
                        'menuId' => $menu,
                        'created_at' => now('Asia/jakarta'),
                        'updated_at' => now('Asia/jakarta'),
                    ];
                }
                CustomerRoleAccessibility::insert($data_menu);
            } else {
                throw new Exception("Role mismatch to our record", 422);
            }
            DB::commit();
            $response = ['message' => 'Creating resources successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'Failed creating resources'];
            if ($th->getCode() == 422) {
                $response = ['message' => $th->getMessage()];
            }
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
            CustomerRoleAccessibility::where('roleId', $id)->delete();
            DB::commit();
            $response = ['message' => 'Successfully destroy resource'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'Failed destroy resource'];
            $code = 422;
        }
        return response()->json($response, $code);
    }
}
