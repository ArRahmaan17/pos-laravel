<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerRoleAccessibility;
use App\Models\UserManagement\Permission;
use App\Models\UserManagement\Role;
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
        $menus = Permission::customer_menu();
        $menus = buildTree($menus);
        $roles = Role::with(['role_users'])->where('user_id', session('userLogged')['company']['user_id'])->get();

        return view('man.customer-role-accessibility', compact('menus', 'roles'));
    }

    public function dataTable(Request $request)
    {
        $where = [
            ['customer_roles.user_id', '=', session('userLogged')['user']['id']],
        ];
        if (getScope() === 'Developer') {
            $where = [
                ['customer_roles.user_id', '=', session('userLogged')['company']['user_id']],
            ];
        }
        $totalData = Role::with('role_menus')
            ->select('customer_roles.name', 'customer_roles.id')
            ->join('customer_role_accessibilities as cra', 'customer_roles.id', '=', 'cra.role_id')
            ->leftJoin('user_customer_roles as ucr', 'customer_roles.id', '=', 'ucr.role_id')
            ->leftJoin('companies as cc', 'ucr.company_id', '=', 'cc.id')
            ->where($where)
            ->orderBy('customer_roles.id', 'asc')
            ->groupBy('customer_roles.name', 'customer_roles.id')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = Role::with('role_menus')
                ->join('customer_role_accessibilities as cra', 'customer_roles.id', '=', 'cra.role_id')
                ->leftJoin('user_customer_roles as ucr', 'customer_roles.id', '=', 'ucr.role_id')
                ->leftJoin('companies as cc', 'ucr.company_id', '=', 'cc.id')
                ->select('customer_roles.name', 'customer_roles.id');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $assets = $assets->where($where)->groupBy('customer_roles.name', 'customer_roles.id')->get();
        } else {
            $assets = Role::with('role_menus')
                ->join('customer_role_accessibilities as cra', 'customer_roles.id', '=', 'cra.role_id')
                ->leftJoin('user_customer_roles as ucr', 'customer_roles.id', '=', 'ucr.role_id')
                ->leftJoin('companies as cc', 'ucr.company_id', '=', 'cc.id')
                ->select('customer_roles.name', 'customer_roles.id')
                ->where('customer_roles.name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('customer_roles.description', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where($where)->groupBy('customer_roles.name', 'customer_roles.id')->get();

            $totalFiltered = Role::select('customer_roles.name', 'customer_roles.id')
                ->join('customer_role_accessibilities as cra', 'customer_roles.id', '=', 'cra.role_id')
                ->leftJoin('user_customer_roles as ucr', 'customer_roles.id', '=', 'ucr.role_id')
                ->leftJoin('companies as cc', 'ucr.company_id', '=', 'cc.id')
                ->where('customer_roles.name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('customer_roles.description', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where($where)->groupBy('customer_roles.name', 'customer_roles.id')->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['name'] = $item->name;
            $row['menu'] = $item->role_menus;
            $row['action'] = "<button class='btn btn-icon btn-outline-warning edit' data-customer-role-accessibility='".$item->id."' ><i class='bx bx-pencil' ></i></button><button data-customer-role-accessibility='".$item->id."' class='btn btn-icon btn-outline-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
            'role_id' => 'required',
            'menuId' => 'required|array',
        ], [
            'role_id.required' => 'The role field is required',
            'menuId.required' => 'The menu field is required',
        ]);
        DB::beginTransaction();
        try {
            if (Role::where(['id' => $request->role_id, 'user_id' => session('userLogged')['company']['user_id']])->count() != 0) {
                $data_menu = [];
                foreach ($request->menuId as $index => $menu) {
                    $data_menu[] = [
                        'role_id' => $request->role_id,
                        'menuId' => $menu,
                        'created_at' => now('Asia/jakarta'),
                        'updated_at' => now('Asia/jakarta'),
                    ];
                }
                CustomerRoleAccessibility::insert($data_menu);
            } else {
                throw new Exception('Role mismatch to our record', 422);
            }
            DB::commit();
            $response = ['message' => 'Creating resources successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'Failed creating resources'];
            if ($th->getCode() === 422) {
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
        $role_menu = Role::select('*', 'id as role_id')->with(['role_menus' => function ($query) {
            $query->select(['*']);
        }])->where('id', $id)->first();
        $response = ['message' => 'Show resources successfully', 'data' => $role_menu];
        $code = 200;
        if (empty($role_menu->role_menus)) {
            $response = ['message' => 'Failed show resources', 'data' => $role_menu];
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
                'role_id' => 'required',
                'menuId' => 'required|array',
            ]
        );
        DB::beginTransaction();
        try {
            if (Role::where(['id' => $request->role_id, 'user_id' => session('userLogged')['company']['user_id']])->count() != 0) {
                CustomerRoleAccessibility::where('role_id', $id)->delete();
                $data_menu = [];
                foreach ($request->menuId as $index => $menu) {
                    $data_menu[] = [
                        'role_id' => $request->role_id,
                        'menuId' => $menu,
                        'created_at' => now('Asia/jakarta'),
                        'updated_at' => now('Asia/jakarta'),
                    ];
                }
                CustomerRoleAccessibility::insert($data_menu);
            } else {
                throw new Exception('Role mismatch to our record', 422);
            }
            DB::commit();
            $response = ['message' => 'Creating resources successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'Failed creating resources'];
            if ($th->getCode() === 422) {
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
            CustomerRoleAccessibility::where('role_id', $id)->delete();
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
