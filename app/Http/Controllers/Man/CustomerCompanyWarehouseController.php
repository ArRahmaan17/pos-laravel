<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompanyWarehouse;
use App\Models\CustomerGoodWarehouse;
use App\Models\CustomerWarehouseRack;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerCompanyWarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::user_manager();
        return view('man.customer-company-warehouse', compact('users'));
    }

    public function dataTable(Request $request)
    {
        $where = [['companyId', '=', session('userLogged')->company->id ?? 0]];
        if (getRole() === "Developer") {
            $where = [['companyId', '<>', 0]];
        }
        $totalData = CustomerCompanyWarehouse::with(['racks'])->where($where)->orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerCompanyWarehouse::with(['racks'])->select('*');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->where($where)->get();
        } else {
            $assets = CustomerCompanyWarehouse::with(['racks'])->select('*')
                ->where('name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('description', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where($where)->get();

            $totalFiltered = CustomerCompanyWarehouse::with(['racks'])->select('*')
                ->where('name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('description', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where($where)->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['name'] = $item->name;
            $row['description'] = $item->description;
            $row['racks'] = $item->racks;
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-customer-company-warehouse='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-customer-company-warehouse='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
            'companyId' => 'required',
            'name' => 'required|min:2|max:30',
            'description' => 'required|min:2|max:100',
            'racks' => 'required|array',
            'racks.*.name' => 'min:2|max:30',
            'racks.*.description' => 'min:2|max:100',
        ], [
            'userId.required' => 'The user field is required.',
            'companyId.required' => 'The company field is required.',
            'racks.required' => 'The racks field must be at least 1 rack item.',
            'racks.array' => 'The racks field does not match the data required by the server',
            'racks.*.name.min' => 'The rack name field must be at least 2 characters.',
            'racks.*.name.max' => 'The rack name field must not be greater than 30 characters.',
            'racks.*.description.min' => 'The rack name field must be at least 2 characters.',
            'racks.*.description.max' => 'The rack description field must not be greater than 100 characters.',
        ]);
        DB::beginTransaction();
        try {
            $warehouse = CustomerCompanyWarehouse::create($request->except('_token', 'userId', 'racks'));
            $racks = [];
            foreach ($request->only('racks')['racks'] as $index => $rack) {
                $racks[] = array_merge($rack, ['warehouseId' => $warehouse->id]);
            }
            CustomerWarehouseRack::create($racks);
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
        $where = [
            ['id', $id],
            ['companyId', '<>', 0]
        ];
        if (getRole() != 'Developer') {
            $where = [
                ['id', $id],
                ['companyId', '=', session('userLogged')->company->id]
            ];
        }
        $warehouse = CustomerCompanyWarehouse::with(['racks', 'company'])->where($where)->first();
        $response = ['message' => 'Showing resources successfully', 'data' => $warehouse];
        $code = 200;
        if (empty($warehouse)) {
            $response = ['message' => 'Failed showing resources', 'data' => $warehouse];
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
            'userId' => 'required',
            'companyId' => 'required',
            'name' => 'required|min:2|max:30',
            'description' => 'required|min:2|max:100',
            'racks' => 'required|array',
            'racks.*.name' => 'min:2|max:30',
            'racks.*.description' => 'min:2|max:100',
        ], [
            'userId.required' => 'The user field is required.',
            'companyId.required' => 'The company field is required.',
            'racks.required' => 'The racks field must be at least 1 rack item.',
            'racks.array' => 'The racks field does not match the data required by the server',
            'racks.*.name.min' => 'The rack name field must be at least 2 characters.',
            'racks.*.name.max' => 'The rack name field must not be greater than 30 characters.',
            'racks.*.description.min' => 'The rack name field must be at least 2 characters.',
            'racks.*.description.max' => 'The rack description field must not be greater than 100 characters.',
        ]);
        DB::beginTransaction();
        try {
            CustomerCompanyWarehouse::where([['id', $id], ['companyId', $request->companyId]])
                ->update($request->except('racks', '_token', 'userId', 'companyId'));
            CustomerGoodWarehouse::whereNotIn('rackId', collect($request->racks)
                ->map(function ($rack_request) {
                    return intval($rack_request['id']);
                })->all())->where('warehouseId', $id)->delete();
            CustomerWarehouseRack::whereNotIn('id', collect($request->racks)
                ->map(function ($rack_request) {
                    return intval($rack_request['id']);
                })->all())->where('warehouseId', $id)->delete();
            foreach ($request->racks as $index => $rack) {
                if (!empty($rack['id'])) {
                    CustomerWarehouseRack::where([['id', $rack['id']], ['warehouseId', $id]])->update($rack);
                } else {
                    $rack['warehouseId'] = $id;
                    CustomerWarehouseRack::create($rack);
                }
            }
            DB::commit();
            $response = ['message' => 'updating resources successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $code = 422;
            $response = ['message' => 'failed updating resources'];
        }
        return response()->json($response, $code);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, $companyId)
    {
        DB::beginTransaction();
        try {
            CustomerCompanyWarehouse::where([['id', $id], ['companyId', $companyId]])->delete();
            CustomerGoodWarehouse::where('warehouseId', $id)->delete();
            CustomerWarehouseRack::where('warehouseId', $id)->delete();
            DB::commit();
            $response = ['message' => 'deleting resources successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed deleting resources'];
            $code = 422;
        }
        return response()->json($response, $code);
    }
}
