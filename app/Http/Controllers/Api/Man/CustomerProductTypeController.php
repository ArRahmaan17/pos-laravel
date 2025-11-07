<?php

namespace App\Http\Controllers\Api\Man;

use App\Helpers\RedisHelper;
use App\Http\Controllers\Controller;
use App\Models\CustomerCompany;
use App\Models\CustomerProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CustomerProductTypeController extends Controller
{
    protected $companyId;

    public function __construct(Request $request)
    {
        $this->companyId = $request->header('x-customer-company-id');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (RedisHelper::exists("cutomer_product_types:{$this->companyId}")) {
            $data = json_decode(RedisHelper::get("cutomer_product_types:{$this->companyId}"));
        } else {
            $company = CustomerCompany::find($request->header('x-customer-company-id'));
            $data = CustomerProductType::orderBy('id', 'asc')->where('businessId', $company->businessId)->get();
            RedisHelper::set("cutomer_product_types:{$this->companyId}", json_encode($data));
        }
        $response = ['message' => 'showing resource successfully', 'data' => $data];
        $code = 200;
        return response()->json($response, $code);
    }

    public function dataTable(Request $request)
    {
        $company = CustomerCompany::find($request->header('x-customer-company-id'));
        try {
            $totalData = CustomerProductType::orderBy('id', 'asc')->where('businessId', $company->businessId)
                ->count();
            $totalFiltered = $totalData;
            if (empty($request['search']['value'])) {
                $assets = CustomerProductType::select('*')->where('businessId', $company->businessId);

                if ($request['length'] != '-1') {
                    $assets->limit($request['length']);
                    if ($request['start'] > 0) {
                        $assets->where('id', '>', $request['start']);
                    }
                }
                if (isset($request['order'][0]['column'])) {
                    $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
                }
                $assets = $assets->get();
            } else {
                $assets = CustomerProductType::select('*')->where('businessId', $company->businessId)
                    ->where('name', 'like', '%' . $request['search']['value'] . '%')
                    ->orWhere('description', 'like', '%' . $request['search']['value'] . '%');

                if (isset($request['order'][0]['column'])) {
                    $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
                }
                if ($request['length'] != '-1') {
                    $assets->limit($request['length']);
                    if ($request['start'] > 0) {
                        $assets->where('id', '>', $request['start']);
                    }
                }
                $assets = $assets->get();

                $totalFiltered = CustomerProductType::select('*')->where('businessId', $company->businessId)
                    ->where('name', 'like', '%' . $request['search']['value'] . '%')
                    ->orWhere('description', 'like', '%' . $request['search']['value'] . '%');

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
                $row['description'] = $item->description;
                $row['action'] = "<button class='btn btn-icon btn-warning edit' data-app-role='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-app-role='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
                $dataFiltered[] = $row;
            }
            $response = [
                'draw' => $request['draw'],
                'recordsFiltered' => $totalFiltered,
                'recordsTotal' => count($dataFiltered),
                'aaData' => $dataFiltered,
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'failed retrieving data', 'data' => []], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        $request->validate([
            'name' => 'required|min:2|max:10|unique:customer_product_types,name',
            'description' => 'required|min:6|max:100',
        ]);
        try {
            $company = CustomerCompany::find($this->companyId);
            $request->merge(['businessId' => $this->companyId]);
            CustomerProductType::create($request->except('_token', 'id'));
            DB::commit();
            RedisHelper::del("cutomer_product_types:{$this->companyId}");
            $response = ['message' => 'Customer Product Type create successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $code = 422;
            $response = ['message' => 'Failed creating Customer Product Type'];
        }

        return response()->json($response, $code);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $data = CustomerProductType::find($id)->where('businessId', $this->companyId);
        $response = ['message' => 'showing resource successfully', 'data' => $data];
        $code = 200;
        if (empty($data)) {
            $response = ['message' => 'failed showing resource', 'data' => $data];
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
            'name' => 'required|unique:customer_product_types,name,' . $id,
            'description' => 'required|min:6|max:100',
        ]);
        DB::beginTransaction();
        try {
            CustomerProductType::find($id)->update($request->except('_token', 'id'));
            RedisHelper::del("cutomer_product_types:{$this->companyId}");
            DB::commit();
            $response = ['message' => 'Updating resource successfully'];
            $code = 200;
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
    public function destroy(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            CustomerProductType::find($id)->where('businessId', $this->companyId)->destroy($id);
            DB::commit();
            RedisHelper::del("cutomer_product_types:{$this->companyId}");
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
