<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\AppGoodUnit;
use App\Models\CustomerCompanyGood;
use App\Models\CustomerTemporaryProduct;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerTemporaryProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = AppGoodUnit::get();

        return view('man.customer-temp-product', compact('units'));
    }

    public function dataTable(Request $request)
    {
        $totalData = CustomerTemporaryProduct::select(DB::raw('DATE(created_at) as created_at'))->orderBy('created_at', 'desc')->where('companyId', session('userLogged')['company']['id'])->groupByRaw('transaction_created')->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerTemporaryProduct::with('changedProduct', 'changedProduct.unit', 'changedProduct.reference', 'changedProduct.creater', 'changedProduct.reference.unit')
                ->select(
                    'transaction_created',
                    DB::raw('sum(accepted = 1) as sum_accepted'),
                    DB::raw('sum(accepted = 0) as sum_not_accepted'),
                    DB::raw("sum(orderCode like '" . buatSingkatan(session('userLogged')['company']['name']) . "-IN-%') as sum_product_in"),
                    DB::raw("sum(orderCode like '" . buatSingkatan(session('userLogged')['company']['name']) . "-RESTOCK-%') as sum_product_restock"),
                    DB::raw("sum(orderCode like '" . buatSingkatan(session('userLogged')['company']['name']) . "-REMOVE-%') as sum_product_remove"),
                );
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->where('companyId', session('userLogged')['company']['id'])->groupByRaw('transaction_created, companyId')->get();
        } else {
            $assets = CustomerTemporaryProduct::with('changedProduct', 'changedProduct.unit', 'changedProduct.reference', 'changedProduct.creater', 'changedProduct.reference.unit')->select(
                'transaction_created',
                DB::raw('sum(accepted = 1) as sum_accepted'),
                DB::raw('sum(accepted = 0) as sum_not_accepted'),
                DB::raw("sum(orderCode like '" . buatSingkatan(session('userLogged')['company']['name']) . "-IN-%') as sum_product_in"),
                DB::raw("sum(orderCode like '" . buatSingkatan(session('userLogged')['company']['name']) . "-RESTOCK-%') as sum_product_restock"),
                DB::raw("sum(orderCode like '" . buatSingkatan(session('userLogged')['company']['name']) . "-REMOVE-%') as sum_product_remove"),
            )->where('orderCode', 'like', '%' . $request['search']['value'] . '%')->orWhere('created_at', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where('companyId', session('userLogged')['company']['id'])->groupByRaw('transaction_created')->get();

            $totalFiltered = CustomerTemporaryProduct::select(
                'transaction_created',
                DB::raw('sum(accepted = 1) as sum_accepted'),
                DB::raw('sum(accepted = 0) as sum_not_accepted'),
                DB::raw("sum(orderCode like '" . buatSingkatan(session('userLogged')['company']['name']) . "-IN-%') as sum_product_in"),
                DB::raw("sum(orderCode like '" . buatSingkatan(session('userLogged')['company']['name']) . "-RESTOCK-%') as sum_product_restock"),
                DB::raw("sum(orderCode like '" . buatSingkatan(session('userLogged')['company']['name']) . "-REMOVE-%') as sum_product_remove"),
            )
                ->where('orderCode', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('created_at', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where('companyId', session('userLogged')['company']['id'])->groupByRaw('transaction_created')->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['transaction_created'] = $item->transaction_created;
            $row['sum_not_accepted'] = $item->sum_not_accepted;
            $row['sum_accepted'] = $item->sum_accepted;
            $row['sum_product_in'] = $item->sum_product_in;
            $row['sum_product_restock'] = $item->sum_product_restock;
            $row['sum_product_remove'] = $item->sum_product_remove;
            $row['changedProduct'] = $item->changedProduct;
            $row['action'] = ((!$item->accepted && in_array(session('userLogged')['role']['name'], ['Manager', 'Developer'])) ? "<button class='btn btn-icon btn-success acc' data-customer-temporary-product='" . $item->transaction_created . "' ><i class='bx bx-check' ></i></button>" : "") .  "<button class='btn btn-icon btn-warning edit' data-customer-temporary-product='" . $item->transaction_created . "' ><i class='bx bx-pencil' ></i></button><button data-customer-temporary-product='" . $item->transaction_created . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
        $attributesName = [
            'name' => 'Name',
            'stock' => 'Stock',
            'price' => 'Price',
            'buyPrice' => 'Buy Price',
            'status' => 'Status',
            'companyId' => 'Company',
            'unitId' => 'Unit',
            'picture' => 'Picture',
        ];
        $request->validate([
            'products.*.name' => [
                'required_if:products.*.status,IN',
                'required_if:products.*.status,RESTOCK',
                'min:6',
                'max:40',
                function ($attribute, $value, $fail) use ($request) {
                    preg_match('/products\.(\d+)\.name/', $attribute, $indexes);
                    $products = $request->products;
                    $product = $products[$indexes[1]];
                    if (!$product) {
                        return;
                    }
                    $query = DB::table('customer_temporary_products')->where('name', $product['name']);
                    if (!empty($product['customerCompanyGoodId']) && $product['status'] != 'REMOVE') {
                        $query->where('customerCompanyGoodId', '!=', $product['customerCompanyGoodId'])->where('companyId', session('userLogged')['company']['id']);
                    }
                    if ($query->exists()) {
                        $fail("The name '{$value}' has already been taken.");
                    }
                },
                function ($attribute, $value, $fail) use ($request) {
                    preg_match('/products\.(\d+)\.name/', $attribute, $indexes);
                    $products = $request->products;
                    $product = $products[$indexes[1]];
                    if (!$product) {
                        return;
                    }
                    $query = DB::table('customer_company_goods')->where('name', $product['name']);
                    if (!empty($product['customerCompanyGoodId']) && $product['status'] != 'REMOVE') {
                        $query->where('id', '!=', $product['customerCompanyGoodId'])->where('companyId', session('userLogged')['company']['id']);
                    }
                    if ($query->exists()) {
                        $fail("The name '{$value}' has already been taken.");
                    }
                }
            ],
            'products.*.stock' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|max:8|regex:/(\d{1,3}(?:\.\d{3})*)/i',
            'products.*.price' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'products.*.buyPrice' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'products.*.status' => 'required|in:IN,RESTOCK,REMOVE',
            // 'products.*.companyId' => 'required|exists:customer_companies,id|in:' . session('userLogged')['company']['id'],
            'products.*.unitId' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|exists:app_good_units,id',
            'products.*.customerCompanyGoodId' => 'required_if:products.*.status,REMOVE|required_if:products.*.status,RESTOCK|exists:customer_company_goods,id',
            'products.*.picture' => 'image|between:1,800|dimensions:ratio=1/1|mimes:png,jpg',
        ]);
        DB::beginTransaction();
        try {
            $orderCode = ['in' => lastCompanyOrderCode('IN'), 'restock' => lastCompanyOrderCode('RESTOCK'), 'remove' => lastCompanyOrderCode('REMOVE')];
            $default_data = [
                'transaction_created' => now()->format('Y-m-d'),
                'companyId' => session('userLogged')['company']['id'],
                'userId' => session('userLogged')['user']['id'],
                'customerCompanyGoodId' => null,
                'name' => null,
                'status' => null,
                'picture' => null,
                'stock' => null,
                'price' => null,
                'buyPrice' => null,
                'unitId' => null,
                'accepted' => 0,
                'accepted_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $resultTempProduct = [];
            foreach ($request->products as $key => $value) {
                foreach ($default_data as $indexDefault => $valueDefault) {
                    $resultTempProduct[$key][$indexDefault] = (!empty($request->products[$key][$indexDefault])) ? (in_array($indexDefault, ['stock', 'price', 'buyPrice']) ? str_replace(',', '.', str_replace('.', '', $request->products[$key][$indexDefault])) : $request->products[$key][$indexDefault]) : $valueDefault;
                    if ($indexDefault == 'picture' && !empty($request->products[$key][$indexDefault])) {
                        $filename = md5($request->products[$key]['name'] . now()->format('Y-m-d h:i:s')) . '.' . $request->products[$key][$indexDefault]->extension();
                        if (Storage::disk('public-asset')->directories('temp-customer-product')) {
                            Storage::disk('public-asset')->makeDirectory('temp-customer-product');
                        }
                        Storage::disk('temp-customer-product')->putFileAs('/', $request->products[$key][$indexDefault], $filename);
                        $resultTempProduct[$key][$indexDefault] = $filename;
                    }
                    if ($indexDefault == 'status') {
                        $resultTempProduct[$key]['orderCode'] = $orderCode[strtolower($resultTempProduct[$key][$indexDefault])];
                        if (in_array($resultTempProduct[$key][$indexDefault], ['IN', 'RESTOCK'])) {
                            $resultTempProduct[$key][$indexDefault] = 'publish';
                        } else {
                            $resultTempProduct[$key][$indexDefault] = null;
                        }
                    }
                }
            }
            CustomerTemporaryProduct::insert($resultTempProduct);
            $response = ['message' => 'creating resource successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed creating resource'];
            $code = 422;
        }

        return response()->json($response, $code);
    }

    public function storeTempProduct(String $date)
    {
        DB::beginTransaction();
        try {
            if (!in_array(getRole(), ['Developer', 'Manager'])) {
                throw new Exception('Not Authorize');
            }
            $data = CustomerTemporaryProduct::with('product')->whereDate('created_at', $date ?? now()->format('Y-m-d'))->where(['companyId' => session('userLogged')['company']['id'], 'accepted' => 0])->get();
            $dataUpdate = [];
            $dataDelete = [];
            $dataInsert = [];
            foreach ($data as $index => $value) {
                $record = [
                    'stock' => $value->stock,
                    'name' => $value->name,
                    'picture' => $value->picture,
                    'price' => $value->price,
                    'buyPrice' => $value->buyPrice,
                    'unitId' => $value->unitId,
                    'companyId' => $value->companyId,
                    'status' => $value->status,
                    'picture' => $value->picture,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if ($value->customerCompanyGoodId) {
                    $record['id'] = $value->customerCompanyGoodId;
                    if (count(explode('REMOVE', $value->orderCode)) > 1) {
                        $record['product'] = $value->product;
                        $dataDelete[] = $record;
                    } else {
                        $dataUpdate[] = $record;
                    }
                } else {
                    $dataInsert[] = $record;
                }
            }
            if (!empty($dataInsert)) {
                CustomerCompanyGood::insert($dataInsert);
                foreach ($dataInsert as $index => $value) {
                    Storage::disk('public-asset')->move('temp-customer-product/' . $value['picture'], 'customer-product/' . $value['picture']);
                }
            }
            if (!empty($dataUpdate)) {
                CustomerCompanyGood::upsert($dataUpdate, ['id'], ['stock', 'name', 'picture', 'price', 'buyPrice', 'unitId']);
                foreach ($dataUpdate as $index => $value) {
                    if (Storage::disk('public-asset')->exists('temp-customer-product/' . $value['picture'])) {
                        Storage::disk('public-asset')->move('temp-customer-product/' . $value['picture'], 'customer-product/' . $value['picture']);
                    }
                }
            }
            if (!empty($dataDelete)) {
                foreach ($dataDelete as $index => $value) {
                    if ($value['product']['picture'] != 'default-product.png') {
                        Storage::disk('customer-product')->delete($value['product']['picture']);
                    }
                }
                CustomerCompanyGood::whereIn(
                    'id',
                    array_map(function ($data) {
                        return $data['id'];
                    }, $dataDelete)
                )->delete();
            }
            CustomerTemporaryProduct::whereDate('created_at', $date ?? now()->format('Y-m-d'))->where(['companyId' => session('userLogged')['company']['id'], 'accepted' => 0])->update(['accepted' => 1]);
            $response = ['message' => 'creating resource successfully'];
            $code = 200;
            DB::commit();
        } catch (\Exception $th) {
            DB::rollBack();
            $response = ['message' => 'failed creating resource' . ($th->getCode() == 0) ? ', ' . $th->getMessage() : ''];
            $code = 422;
        }

        return response()->json($response, $code);
    }

    public function show(string $date)
    {
        $data = CustomerTemporaryProduct::with('reference')->where('transaction_created', $date)->where(['companyId' => session('userLogged')['company']['id'], 'accepted' => 0])->get()->map(function ($temp) {
            $temp['status'] = statusTransaction($temp['orderCode']);
            return $temp;
        });
        $response = ['message' => 'showing resource successfully', 'data' => $data];
        $code = 200;
        if (empty($data)) {
            $response = ['message' => 'failed showing resource', 'data' => $data];
            $code = 404;
        }

        return response()->json($response, $code);
    }

    /**
     * Display the specified resource.
     */
    public function showTemp(string $id)
    {
        $data = CustomerCompanyGood::where([['id', $id], ['companyId', session('userLogged')['company']['id']]])->first();
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
            'products.*.name' => ['required_if:products.*.status,IN', 'required_if:products.*.status,RESTOCK', 'min:6', 'max:40', function ($attribute, $value, $fail) use ($request) {
                preg_match('/products\.(\d+)\.name/', $attribute, $indexes);
                $product = $request->products[$indexes[1]];
                if (!$product) {
                    return;
                }
                $query = DB::table('customer_temporary_products')->where('name', $product['name']);
                if (!empty($product['customerCompanyGoodId']) && $product['status'] != 'REMOVE') {
                    $query->where('customerCompanyGoodId', '!=', $product['customerCompanyGoodId'])->where('companyId', session('userLogged')['company']['id']);
                }
                if ($query->exists()) {
                    $fail("The name '{$value}' has already been taken.");
                }
            }, function ($attribute, $value, $fail) use ($request) {
                preg_match('/products\.(\d+)\.name/', $attribute, $indexes);
                $products = $request->products;
                $product = $products[$indexes[1]];
                if (!$product) {
                    return;
                }
                $query = DB::table('customer_company_goods')->where('name', $product['name']);
                if (!empty($product['customerCompanyGoodId']) && $product['status'] != 'REMOVE') {
                    $query->where('id', '!=', $product['customerCompanyGoodId'])->where('companyId', session('userLogged')['company']['id']);
                }
                if ($query->exists()) {
                    $fail("The name '{$value}' has already been taken.");
                }
            }],
            'products.*.stock' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|max:8|regex:/(\d{1,3}(?:\.\d{3})*)/i',
            'products.*.price' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'products.*.buyPrice' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'products.*.status' => 'required|in:IN,RESTOCK,REMOVE',
            'products.*.companyId' => 'required|exists:customer_companies,id|in:' . session('userLogged')['company']['id'],
            'products.*.unitId' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|exists:app_good_units,id',
            'products.*.customerCompanyGoodId' => 'required_if:products.*.status,REMOVE|required_if:products.*.status,RESTOCK|exists:customer_company_goods,id',
            'products.*.picture' => 'image|between:1,800|dimensions:ratio=1/1|mimes:png,jpg',
        ]);
        DB::beginTransaction();
        try {
            $dataExists = collect($request->products)->filter(function ($value, $key) {
                return !empty($value['id']);
            })->all();
            dd($dataExists);
            $data = $request->except('_token', 'id');
            $data['picture'] = CustomerCompanyGood::find($id)->picture;
            if ($request->file('picture')) {
                $filename = md5($request->name . now()->format('Y-m-d h:i:s')) . '.' . $request->file('picture')->clientExtension();
                $data['picture'] = $filename;
                Storage::disk('temp-customer-product')->putFileAs('/', $request->file('picture'), $filename);
            }
            $data['price'] = str_replace(',', '.', str_replace('.', '', $request->price));
            $data['buyPrice'] = str_replace(',', '.', str_replace('.', '', $request->buyPrice));
            $data['companyId'] = session('userLogged')['company']['id'];
            $data['userId'] = session('userLogged')['user']['id'];
            $data['customerCompanyGoodId'] = $id;
            $data['orderCode'] = lastCompanyOrderCode('RESTOCK');
            CustomerTemporaryProduct::create($data);
            $response = ['message' => 'updating resource successfully'];
            $code = 200;
            DB::commit();
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
        DB::beginTransaction();
        try {
            $data = [
                'orderCode' => lastCompanyOrderCode('REMOVE'),
                'customerCompanyGoodId' => $id,
                'companyId' => session('userLogged')['company']['id'],
                'userId' => session('userLogged')['user']['id'],
            ];
            CustomerTemporaryProduct::create($data);
            // Storage::disk('customer-product')->delete(CustomerCompanyGood::find($id)->picture);
            // CustomerCompanyGood::where([
            //     ['id', $id],
            //     ['userId', session('userLogged')['company']['id']],
            // ])->delete();
            $response = ['message' => 'deleting resource successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed deleting resource'];
            $code = 422;
        }

        return response()->json($response, $code);
    }
}
