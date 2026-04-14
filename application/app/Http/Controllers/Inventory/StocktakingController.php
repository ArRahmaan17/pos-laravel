<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompanyGood;
use App\Models\CustomerCompanyStocktaking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StocktakingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('inventory.stocktaking');
    }

    public function dataTable(Request $request)
    {
        $where = [['customer_company_stocktakings.company_id', '=', session('userLogged')['company']['id']]];
        $totalData = CustomerCompanyStocktaking::join('products', 'products.id', '=', 'customer_company_stocktakings.goodId')->where($where)->orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerCompanyStocktaking::join('products', 'products.id', '=', 'customer_company_stocktakings.goodId')->select('customer_company_stocktakings.*', 'products.name');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $assets = $assets->where($where)->get();
        } else {
            $assets = CustomerCompanyStocktaking::join('products', 'products.id', '=', 'customer_company_stocktakings.goodId')->select('customer_company_stocktakings.*', 'products.name')
                ->where('name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('description', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where($where)->get();

            $totalFiltered = CustomerCompanyStocktaking::join('products', 'products.id', '=', 'customer_company_stocktakings.goodId')->select('customer_company_stocktakings.*', 'products.name')
                ->where('name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('description', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where($where)->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['name'] = $item->name;
            $row['expect_stock'] = $item->expect_stock;
            $row['real_stock'] = $item->real_stock;
            $row['status'] = (! $item->status) ? '<span class="badge rounded-pill bg-label-warning">Wait for approval</span>' : '<span class="badge rounded-pill bg-label-success">Approved</span>';
            $row['action'] = ((! $item->status) ? "<button class='btn btn-icon btn-outline-success approve' data-customer-product-stocktaking='".$item->id."' ><i class='bx bx-check'></i></button><button class='btn btn-icon btn-outline-warning edit' data-customer-product-stocktaking='".$item->id."' ><i class='bx bx-pencil' ></i></button><button data-customer-product-stocktaking='".$item->id."' class='btn btn-icon btn-outline-danger delete'><i class='bx bxs-trash-alt' ></i></button>" : "<button data-customer-product-stocktaking='".$item->id."' class='btn btn-icon btn-info show-stocktaking'><i class='bx bx-search'></i></button>");
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
            'product.*.goodId' => 'required|exists:products,id',
            'product.*.expect_stock' => 'required|regex:/(\d{1,3}(?:\.\d{3})*)/i',
            'product.*.real_stock' => 'required|regex:/(\d{1,3}(?:\.\d{3})*)/i',
        ]);
        DB::beginTransaction();
        try {
            $data = array_values(array_map(function ($product) {
                return [
                    'goodId' => $product['goodId'],
                    'expect_stock' => implode('', explode('.', $product['expect_stock'])),
                    'real_stock' => implode('', explode('.', $product['real_stock'])),
                    'user_id' => session('userLogged')['user']['id'],
                    'company_id' => session('userLogged')['company']['id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $request->product));
            CustomerCompanyStocktaking::insert($data);
            DB::commit();
            $status = 200;
            $message = ['message' => 'Successfully create resources'];
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = 422;
            $message = ['message' => 'Failed create resources'];
        }

        return response()->json($message, $status);
    }

    public function approveStocktaking($id)
    {
        DB::beginTransaction();
        try {
            $builder = CustomerCompanyStocktaking::find($id);
            $builder->update(['status' => 1]);
            CustomerCompanyGood::find($builder->goodId)->update(['stock' => $builder->real_stock]);
            DB::commit();
            $status = 200;
            $message = ['message' => 'Successfully approving resources'];
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = 422;
            $message = ['message' => 'Failed approving resources'];
        }

        return response()->json($message, $status);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = CustomerCompanyStocktaking::join('products', 'customer_company_stocktakings.goodId', '=', 'products.id')->select('customer_company_stocktakings.*', 'products.name')->find($id);
        $status = 200;
        $message = ['message' => 'Successfully showing resources', 'data' => $data];
        if (! $data) {
            $status = 404;
            $message = ['message' => 'Failed showing resources', 'data' => $data];
        }

        return response()->json($message, $status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'product.*.goodId' => 'required|exists:products,id',
            'product.*.expect_stock' => 'required|regex:/(\d{1,3}(?:\.\d{3})*)/i',
            'product.*.real_stock' => 'required|regex:/(\d{1,3}(?:\.\d{3})*)/i',
        ]);
        $data = $request->product;
        DB::beginTransaction();
        try {
            $dataStocktaking = CustomerCompanyStocktaking::where('status', 0)->whereIn('id', [array_map(function ($prd) {
                return $prd['id'];
            }, $request->product)])->get();
            $dataStocktaking->map(function ($stock) use ($request) {
                $stock->expect_stock = implode('', explode('.', $request->product[$stock->goodId]['expect_stock']));
                $stock->real_stock = implode('', explode('.', $request->product[$stock->goodId]['real_stock']));
                $stock->save();
            });
            DB::commit();
            $status = 200;
            $message = ['message' => 'Successfully updating resource'];
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = 422;
            $message = ['message' => 'Failed updating resource'];
        }

        return response()->json($message, $status);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $status = 422;
        $message = ['message' => 'Failed delete resource'];
        if (CustomerCompanyStocktaking::where('status', 0)->where('id', $id)->delete()) {
            $status = 200;
            $message = ['message' => 'Successfully delete resource'];
        }

        return response()->json($message, $status);
    }
}
