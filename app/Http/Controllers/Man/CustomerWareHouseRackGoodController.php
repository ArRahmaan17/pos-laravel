<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompany;
use App\Models\CustomerCompanyGood;
use App\Models\CustomerCompanyWarehouse;
use App\Models\CustomerWarehouseRack;
use App\Models\CustomerWarehouseRackGood;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerWareHouseRackGoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $where = [['userId', '=', session('userLogged')->user->id ?? 1]];
        if (getRole() == 'Developer') {
            $where = [['userId', '<>', 0]];
        }
        $companies = CustomerCompany::where($where)->get();
        return view('man.customer-good-rack', compact('companies'));
    }

    public function racks($id)
    {
        $where = [['companyId', '=', session('userLogged')->company->id ?? 10]];
        if (getRole() == 'Developer') {
        }
        $where = [['companyId', '<>', 0]];
        $data = CustomerCompanyWarehouse::with(['racks'])->where($where)->get()->map(function ($data) {
            $data->racks->load('products');
            collect($data->racks)->map(function ($product) {
                $product->products->load('product');
                return $product;
            });
            return $data;
        })->all();
        $warehouse_company = CustomerCompanyWarehouse::with('company')->first();
        $shelf_less = CustomerCompanyGood::shelf_less($warehouse_company->company->id);
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
                throw new Exception("Products that have been entered into the account cannot be removed again", 422);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
