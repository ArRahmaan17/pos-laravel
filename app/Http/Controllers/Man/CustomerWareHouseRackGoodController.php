<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompany;
use App\Models\CustomerCompanyWarehouse;
use App\Models\CustomerWarehouseRack;
use App\Models\CustomerWarehouseRackGood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerWareHouseRackGoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $where = [['companyId', '=', session('userLogged')->company->id ?? 10]];
        if (getRole() == 'Developer') {
            $where = [['companyId', '<>', 0]];
        }
        $warehouses = CustomerCompanyWarehouse::where($where)->get();
        return view('man.customer-good-rack', compact('warehouses'));
    }

    public function racks($id)
    {
        $data = CustomerWarehouseRack::with(['products'])->where(
            'warehouseId',
            (getRole() == 'Developer') ? $id : session('userLogged')->company->id
        )->get()->map(function ($data) {
            $data->products->load('product');
            return $data;
        })->all();
        $response = ['message' => 'showing resource successfully', 'data' => $data];
        $code = 200;
        if (empty($data)) {
            $code = 404;
            $response = ['message' => 'failed showing resource', 'data' => $data];
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
            CustomerWarehouseRackGood::where('goodId', $goodId)->update(['rackId' => $rackId]);
            DB::commit();
            $response = ['message' => 'updating resource successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed updating resource'];
            $code = 422;
        }
        return response()->json($response, $code);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
