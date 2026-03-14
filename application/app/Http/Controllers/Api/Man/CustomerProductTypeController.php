<?php

namespace App\Http\Controllers\Api\Man;

use App\Helpers\RedisHelper;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CustomerProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerProductTypeController extends Controller
{
    protected $company_id;

    public function __construct(Request $request)
    {
        $this->company_id = $request->header('x-customer-company-id');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (RedisHelper::exists("cutomer_product_categories:{$this->company_id}")) {
            $data = json_decode(RedisHelper::get("cutomer_product_categories:{$this->company_id}"));
        } else {
            $company = Company::find($request->header('x-customer-company-id'));
            $data = CustomerProductType::orderBy('id', 'asc')->where('business_id', $company->business_id)->get();
            RedisHelper::set("cutomer_product_categories:{$this->company_id}", json_encode($data));
        }
        $response = ['message' => 'showing resource successfully', 'data' => $data];
        $code = 200;

        return response()->json($response, $code);
    }

    public function dataTable(Request $request)
    {
        $company = Company::find($request->header('x-customer-company-id'));
        try {
            $totalData = CustomerProductType::orderBy('id', 'asc')->where('business_id', $company->business_id)
                ->count();
            $totalFiltered = $totalData;
            if (empty($request['search']['value'])) {
                $assets = CustomerProductType::select('*')->where('business_id', $company->business_id);

                if ($request['length'] != '-1') {
                    $assets->limit($request['length']);
                    if ($request['start'] > 0) {
                        $assets->where('id', '>', $request['start']);
                    }
                }
                if (isset($request['order'][0]['column'])) {
                    $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
                }
                $assets = $assets->get();
            } else {
                $assets = CustomerProductType::select('*')->where('business_id', $company->business_id)
                    ->where('name', 'like', '%'.$request['search']['value'].'%')
                    ->orWhere('description', 'like', '%'.$request['search']['value'].'%');

                if (isset($request['order'][0]['column'])) {
                    $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
                }
                if ($request['length'] != '-1') {
                    $assets->limit($request['length']);
                    if ($request['start'] > 0) {
                        $assets->where('id', '>', $request['start']);
                    }
                }
                $assets = $assets->get();

                $totalFiltered = CustomerProductType::select('*')->where('business_id', $company->business_id)
                    ->where('name', 'like', '%'.$request['search']['value'].'%')
                    ->orWhere('description', 'like', '%'.$request['search']['value'].'%');

                if (isset($request['order'][0]['column'])) {
                    $totalFiltered->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
                }
                $totalFiltered = $totalFiltered->count();
            }
            $dataFiltered = [];
            foreach ($assets as $index => $item) {
                $row = [];
                $row['order_number'] = $request['start'] + ($index + 1);
                $row['name'] = $item->name;
                $row['description'] = $item->description;
                $row['action'] = "<button class='btn btn-icon btn-outline-warning edit' data-role='".$item->id."' ><i class='bx bx-pencil' ></i></button><button data-role='".$item->id."' class='btn btn-icon btn-outline-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
            'name' => 'required|min:2|max:10|unique:product_categories,name',
            'description' => 'required|min:6|max:100',
        ]);
        try {
            $company = Company::find($this->company_id);
            $request->merge(['business_id' => $this->company_id]);
            CustomerProductType::create($request->except('_token', 'id'));
            DB::commit();
            RedisHelper::del("cutomer_product_categories:{$this->company_id}");
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
        $data = CustomerProductType::find($id)->where('business_id', $this->company_id);
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
            'name' => 'required|unique:product_categories,name,'.$id,
            'description' => 'required|min:6|max:100',
        ]);
        DB::beginTransaction();
        try {
            CustomerProductType::find($id)->update($request->except('_token', 'id'));
            RedisHelper::del("cutomer_product_categories:{$this->company_id}");
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
            CustomerProductType::find($id)->where('business_id', $this->company_id)->destroy($id);
            DB::commit();
            RedisHelper::del("cutomer_product_categories:{$this->company_id}");
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
