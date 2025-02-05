<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompany;
use App\Models\CustomerCompanyGood;
use App\Models\CustomerCompanyWarehouse;
use App\Models\CustomerWarehouseRackGood;
use Exception;
use Illuminate\Support\Facades\DB;

class CustomerWareHouseRackGoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $where = [['userId', '=', session('userLogged')['company']['userId']]];
        $companies = CustomerCompany::where($where)->get();

        return view('man.customer-good-rack', compact('companies'));
    }

    public function racks($id)
    {
        $where = [['companyId', '=', session('userLogged')['company']['id']]];
        if (getRole() == 'Developer') {
            $where = [['companyId', '<>', 0]];
        }
        $data = CustomerCompanyWarehouse::with(['racks.products.product'])->where($where)->get();
        $warehouse_company = CustomerCompanyWarehouse::with('company')->first();
        $shelf_less = CustomerCompanyGood::shelf_less(isset($warehouse_company->company) ? $warehouse_company->company->id : 0);
        $response = ['message' => 'showing resource successfully', 'data' => $data, 'shelf_less' => $shelf_less];
        $code = 200;
        if (empty($data)) {
            $code = 404;
            $response = ['message' => 'failed showing resource', 'data' => $data, 'shelf_less' => $shelf_less];
        }

        return response()->json($response, $code);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($rackId, string $goodId)
    {
        DB::beginTransaction();
        try {
            if ($rackId == 'shelfless') {
                throw new Exception('Products that have been entered into the account cannot be removed again', 422);
            }
            if (CustomerWarehouseRackGood::where('goodId', $goodId)->count() == 1) {
                CustomerWarehouseRackGood::where('goodId', $goodId)->update(['rackId' => $rackId]);
            } else {
                CustomerWarehouseRackGood::create(['rackId' => $rackId, 'goodId' => $goodId]);
            }
            DB::commit();
            $response = ['message' => 'updating resource successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => $th->getCode() == 422 ? $th->getMessage() : 'failed updating resource'];
            $code = 422;
        }

        return response()->json($response, $code);
    }
}
