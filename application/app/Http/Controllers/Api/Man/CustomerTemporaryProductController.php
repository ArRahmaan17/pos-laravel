<?php

namespace App\Http\Controllers\Api\Man;

use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use App\Models\CustomerTemporaryProduct;
use App\Models\Product\CustomerCompanyGood;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductWeight;
use App\Traits\ImageHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerTemporaryProductController extends Controller
{
    use ImageHandler;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $company = $request->header('x-customer-company-id');

        $units = ProductWeight::get();
        $categories = ProductCategory::with('category')->where('business_id', $company->business_id)->get();

        return response()->json([
            'message' => 'Data retrieved successfully',
            'data' => [
                'units' => $units,
                'categories' => $categories,
            ],
        ], 200);
    }

    public function dataTable(Request $request)
    {
        $company = $request->header('x-customer-company-id');
        $company = Company::find($company);
        try {
            $totalData = CustomerTemporaryProduct::select(DB::raw('DATE(created_at) as created_at'))
                ->orderBy('created_at', 'desc')
                ->where('company_id', $company->id)
                ->groupByRaw('transaction_created')
                ->count();

            $totalFiltered = $totalData;

            if (empty($request['search'])) {
                $assets = CustomerTemporaryProduct::with('changedProduct', 'changedProduct.unit', 'changedProduct.reference', 'changedProduct.creater', 'changedProduct.reference.unit', 'changedProduct.accepter')
                    ->select(
                        'transaction_created',
                        DB::raw('sum(accepted = 1) as sum_accepted'),
                        DB::raw('sum(accepted = 0) as sum_not_accepted'),
                        DB::raw("sum(orderCode like '" . buatSingkatan($company->name) . "-IN-%') as sum_product_in"),
                        DB::raw("sum(orderCode like '" . buatSingkatan($company->name) . "-RESTOCK-%') as sum_product_restock"),
                        DB::raw("sum(orderCode like '" . buatSingkatan($company->name) . "-REMOVE-%') as sum_product_remove"),
                    );

                if ($request['length'] != '-1') {
                    $assets->limit($request['length']);
                    if ($request['start'] > 0) {
                        $assets->where('id', '>', $request['start']);
                    }
                }

                if (isset($request['order']['name'])) {
                    $assets->orderByRaw($request['order']['name'] . ' ' . $request['order']['dir']);
                }

                $assets = $assets->where('company_id', $company->id)
                    ->groupByRaw('transaction_created, company_id')
                    ->get();
            } else {
                $assets = CustomerTemporaryProduct::with('changedProduct', 'changedProduct.unit', 'changedProduct.reference', 'changedProduct.creater', 'changedProduct.reference.unit', 'changedProduct.accepter')
                    ->select(
                        'transaction_created',
                        DB::raw('sum(accepted = 1) as sum_accepted'),
                        DB::raw('sum(accepted = 0) as sum_not_accepted'),
                        DB::raw("sum(orderCode like '" . buatSingkatan($company->name) . "-IN-%') as sum_product_in"),
                        DB::raw("sum(orderCode like '" . buatSingkatan($company->name) . "-RESTOCK-%') as sum_product_restock"),
                        DB::raw("sum(orderCode like '" . buatSingkatan($company->name) . "-REMOVE-%') as sum_product_remove"),
                    )
                    ->where('orderCode', 'like', '%' . $request['search']['value'] . '%')
                    ->orWhere('created_at', 'like', '%' . $request['search']['value'] . '%');

                if (isset($request['order']['name'])) {
                    $assets->orderByRaw($request['order']['name'] . ' ' . $request['order']['dir']);
                }

                if ($request['length'] != '-1') {
                    $assets->limit($request['length']);
                    if ($request['start'] > 0) {
                        $assets->where('id', '>', $request['start']);
                    }
                }

                $assets = $assets->where('company_id', $company->id)
                    ->groupByRaw('transaction_created')
                    ->get();

                $totalFiltered = CustomerTemporaryProduct::select(
                    'transaction_created',
                    DB::raw('sum(accepted = 1) as sum_accepted'),
                    DB::raw('sum(accepted = 0) as sum_not_accepted'),
                    DB::raw("sum(orderCode like '" . buatSingkatan($company->name) . "-IN-%') as sum_product_in"),
                    DB::raw("sum(orderCode like '" . buatSingkatan($company->name) . "-RESTOCK-%') as sum_product_restock"),
                    DB::raw("sum(orderCode like '" . buatSingkatan($company->name) . "-REMOVE-%') as sum_product_remove"),
                )
                    ->where('orderCode', 'like', '%' . $request['search']['value'] . '%')
                    ->orWhere('created_at', 'like', '%' . $request['search']['value'] . '%');

                if (isset($request['order']['column'])) {
                    $totalFiltered->orderByRaw($request['order']['name'] . ' ' . $request['order']['dir']);
                }

                $totalFiltered = $totalFiltered->where('company_id', $company->id)
                    ->groupByRaw('transaction_created')
                    ->count();
            }

            $dataFiltered = [];
            foreach ($assets as $_ => $item) {
                $row = [];
                $row['transaction_created'] = $item->transaction_created;
                $row['sum_not_accepted'] = $item->sum_not_accepted;
                $row['sum_accepted'] = $item->sum_accepted;
                $row['sum_product_in'] = $item->sum_product_in;
                $row['sum_product_restock'] = $item->sum_product_restock;
                $row['sum_product_remove'] = $item->sum_product_remove;
                $row['changedProduct'] = $item->changedProduct;
                $dataFiltered[] = $row;
            }
            if ($totalFiltered === 0 || count($dataFiltered) === 0) {
                throw new Exception('No data found');
            }
            $response = [
                'message' => 'Data retrieved successfully',
                'recordsFiltered' => $totalFiltered,
                'recordsTotal' => count($dataFiltered),
                'data' => $dataFiltered,
            ];
            $code = 200;
        } catch (\Throwable $th) {
            $response = ['message' => 'Failed retrieving data', 'data' => []];
            $code = 404;
        }

        return response()->json($response, $code);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $company = Company::find($request->header('x-customer-company-id'));

        $request->validate([
            'products.*.name' => [
                'required_if:products.*.status,IN',
                'required_if:products.*.status,RESTOCK',
                'min:6',
                'max:40',
                function ($attribute, $value, $fail) use ($request, $company) {
                    preg_match('/products\.(\d+)\.name/', $attribute, $indexes);
                    $products = $request->products;
                    $product = $products[$indexes[1]];
                    if (! $product) {
                        return;
                    }
                    $query = DB::table('adjustment_products')->where('name', $product['name']);
                    if (! empty($product['customerCompanyGoodId']) && $product['status'] != 'REMOVE') {
                        $query->where('customerCompanyGoodId', '!=', $product['customerCompanyGoodId'])->where('company_id', $company->id);
                    }
                    if ($query->exists()) {
                        $fail("The name '{$value}' has already been taken.");
                    }
                },
                function ($attribute, $value, $fail) use ($request, $company) {
                    preg_match('/products\.(\d+)\.name/', $attribute, $indexes);
                    $products = $request->products;
                    $product = $products[$indexes[1]];
                    if (! $product) {
                        return;
                    }
                    $query = DB::table('products')->where('name', $product['name']);
                    if (! empty($product['customerCompanyGoodId']) && $product['status'] != 'REMOVE') {
                        $query->where('id', '!=', $product['customerCompanyGoodId'])->where('company_id', $company->id);
                    }
                    if ($query->exists()) {
                        $fail("The name '{$value}' has already been taken.");
                    }
                },
            ],
            'products.*.stock' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|max:5|regex:/(\d{1,3}(?:\.\d{3})*)/i',
            'products.*.price' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'products.*.buy_price' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'products.*.status' => 'required|in:IN,RESTOCK,REMOVE',
            'products.*.weight_id' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|exists:product_weights,id',
            'products.*.category_id' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|exists:product_categories,id',
            'products.*.customerCompanyGoodId' => 'required_if:products.*.status,REMOVE|required_if:products.*.status,RESTOCK|exists:products,id',
            'products.*.picture' => 'image|between:1,800|dimensions:ratio=1/1|mimes:png,jpg',
        ]);

        DB::beginTransaction();
        try {
            $orderCode = ['in' => lastCompanyOrderCode('IN'), 'restock' => lastCompanyOrderCode('ADJ'), 'remove' => lastCompanyOrderCode('REMOVE')];
            $default_data = [
                'transaction_created' => now()->format('Y-m-d'),
                'company_id' => $company->id,
                'user_id' => $user->id,
                'customerCompanyGoodId' => null,
                'name' => null,
                'status' => null,
                'picture' => null,
                'stock' => null,
                'stock_reference' => null,
                'price' => null,
                'buy_price' => null,
                'weight_id' => null,
                'category_id' => null,
                'accepted' => 0,
                'accepted_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $referenceProducts = CustomerCompanyGood::whereIn('id', array_map(function ($product) {
                return $product['customerCompanyGoodId'];
            }, array_filter($request->products, function ($product) {
                return ! empty($product['customerCompanyGoodId']);
            })))->get()->toArray();

            $resultTempProduct = [];
            foreach ($request->products as $key => $value) {
                foreach ($default_data as $indexDefault => $valueDefault) {
                    $resultTempProduct[$key][$indexDefault] = (! empty($request->products[$key][$indexDefault])) ? (in_array($indexDefault, ['stock', 'price', 'buy_price']) ? str_replace(',', '.', str_replace('.', '', $request->products[$key][$indexDefault])) : $request->products[$key][$indexDefault]) : $valueDefault;

                    if ($indexDefault === 'picture') {
                        if (! empty($request->products[$key][$indexDefault])) {
                            $filename = md5($request->products[$key]['name'] . now()->format('Y-m-d h:i:s')) . '.' . $request->products[$key][$indexDefault]->extension();
                            $this->uploadAndWatermark($request->products[$key][$indexDefault], '', 'temp-customer-product', $filename);
                            $resultTempProduct[$key][$indexDefault] = $filename;
                        } else {
                            $resultTempProduct[$key][$indexDefault] = ($resultTempProduct[$key]['customerCompanyGoodId']) ? collect($referenceProducts)->filter(function ($ref) use ($resultTempProduct, $key) {
                                return $ref['id'] === $resultTempProduct[$key]['customerCompanyGoodId'];
                            })->first()['picture'] : 'default-product.png';
                        }
                    }

                    if ($indexDefault === 'status') {
                        $resultTempProduct[$key]['orderCode'] = $orderCode[strtolower($resultTempProduct[$key][$indexDefault])];
                        if (in_array($resultTempProduct[$key][$indexDefault], ['IN', 'RESTOCK'])) {
                            $resultTempProduct[$key][$indexDefault] = 'publish';
                        } else {
                            $resultTempProduct[$key][$indexDefault] = null;
                        }
                    }

                    if ($indexDefault === 'stock_reference') {
                        $resultTempProduct[$key][$indexDefault] = collect($referenceProducts)->filter(function ($ref) use ($resultTempProduct, $key) {
                            return $ref['id'] === $resultTempProduct[$key]['customerCompanyGoodId'];
                        })->first()['stock'] ?? 0;
                    }
                }
            }

            CustomerTemporaryProduct::insert($resultTempProduct);
            $response = ['message' => 'Creating resource successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'Failed creating resource', 'error' => $th->getMessage()];
            $code = 422;
        }

        return response()->json($response, $code);
    }

    public function storeTempProduct(Request $request, string $date)
    {
        $user = $request->user();
        $company = $request->header('x-customer-company-id');

        DB::beginTransaction();
        try {
            if (! in_array($user->role->name, ['Developer', 'Manager'])) {
                throw new Exception('Not Authorized');
            }

            $data = CustomerTemporaryProduct::with('reference')
                ->whereDate('created_at', $date ?? now()->format('Y-m-d'))
                ->where(['company_id' => $company->id, 'accepted' => 0])
                ->get();

            $dataUpdate = [];
            $dataDelete = [];
            $dataInsert = [];

            foreach ($data as $index => $value) {
                $record = [
                    'stock' => $value->stock,
                    'name' => $value->name,
                    'picture' => $value->picture,
                    'price' => $value->price,
                    'buy_price' => $value->buy_price,
                    'weight_id' => $value->weight_id,
                    'category_id' => $value->category_id,
                    'company_id' => $value->company_id,
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

            if (! empty($dataInsert)) {
                CustomerCompanyGood::insert($dataInsert);
                foreach ($dataInsert as $index => $value) {
                    Storage::disk('public-asset')->move('temp-customer-product/' . $value['picture'], 'customer-product/' . $value['picture']);
                    Storage::disk('public-asset')->delete('temp-customer-product/' . $value['picture']);
                }
            }

            if (! empty($dataUpdate)) {
                CustomerCompanyGood::upsert($dataUpdate, ['id'], ['stock', 'name', 'picture', 'price', 'buy_price', 'weight_id', 'category_id']);
                foreach ($dataUpdate as $index => $value) {
                    if (Storage::disk('public-asset')->exists('temp-customer-product/' . $value['picture'])) {
                        Storage::disk('public-asset')->move('temp-customer-product/' . $value['picture'], 'customer-product/' . $value['picture']);
                        Storage::disk('public-asset')->delete('temp-customer-product/' . $value['picture']);
                    }
                }
            }

            if (! empty($dataDelete)) {
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

            CustomerTemporaryProduct::whereDate('created_at', $date ?? now()->format('Y-m-d'))
                ->where(['company_id' => $company->id, 'accepted' => 0])
                ->update(['accepted' => 1, 'accepted_by' => $user->id]);

            $response = ['message' => 'Creating resource successfully'];
            $code = 200;
            DB::commit();
        } catch (Exception $th) {
            DB::rollBack();
            $response = ['message' => 'Failed creating resource' . ($th->getCode() === 0) ? ', ' . $th->getMessage() : ''];
            $code = 422;
        }

        return response()->json($response, $code);
    }

    public function show(Request $request, string $date)
    {
        $user = $request->user();
        $company = $request->header('x-customer-company-id');

        $data = CustomerTemporaryProduct::with('reference')
            ->where('transaction_created', $date)
            ->where(['company_id' => $company->id, 'accepted' => 0])
            ->get()
            ->map(function ($temp) {
                $temp['status'] = statusTransaction($temp['orderCode']);

                return $temp;
            });

        $response = ['message' => 'Showing resource successfully', 'data' => $data];
        $code = 200;

        if (empty($data)) {
            $response = ['message' => 'Failed showing resource', 'data' => $data];
            $code = 404;
        }

        return response()->json($response, $code);
    }

    /**
     * Display the specified resource.
     */
    public function showTemp(Request $request, string $id)
    {
        $user = $request->user();
        $company = $request->header('x-customer-company-id');

        $data = CustomerCompanyGood::where([['id', $id], ['company_id', $company->id]])->first();

        $response = ['message' => 'Showing resource successfully', 'data' => $data];
        $code = 200;

        if (empty($data)) {
            $response = ['message' => 'Failed showing resource', 'data' => $data];
            $code = 404;
        }

        return response()->json($response, $code);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $company = $request->header('x-customer-company-id');

        $request->validate([
            'products.*.name' => ['required_if:products.*.status,IN', 'required_if:products.*.status,RESTOCK', 'min:6', 'max:40', function ($attribute, $value, $fail) use ($request, $company) {
                preg_match('/products\.(\d+)\.name/', $attribute, $indexes);
                $product = $request->products[$indexes[1]];
                if (! $product) {
                    return;
                }
                $query = DB::table('adjustment_products')->where('name', $product['name']);
                if (! empty($product['id']) && $product['status'] != 'REMOVE') {
                    $query->where('id', '!=', $product['id'])->where('company_id', $company->id);
                }
                if ($query->exists()) {
                    $fail("The name '{$value}' has already been taken.");
                }
            }, function ($attribute, $value, $fail) use ($request, $company) {
                preg_match('/products\.(\d+)\.name/', $attribute, $indexes);
                $products = $request->products;
                $product = $products[$indexes[1]];
                if (! $product) {
                    return;
                }
                $query = DB::table('products')->where('name', $product['name']);
                if (! empty($product['customerCompanyGoodId']) && $product['status'] != 'REMOVE') {
                    $query->where('id', '!=', $product['customerCompanyGoodId'])->where('company_id', $company->id);
                }
                if ($query->exists()) {
                    $fail("The name '{$value}' has already been taken.");
                }
            }],
            'products.*.stock' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|max:5|regex:/(\d{1,3}(?:\.\d{3})*)/i',
            'products.*.price' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'products.*.buy_price' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'products.*.status' => 'required|in:IN,RESTOCK,REMOVE',
            'products.*.company_id' => 'required|exists:companies,id|in:' . $company->id,
            'products.*.weight_id' => 'required_if:products.*.status,IN|required_if:products.*.status,RESTOCK|exists:product_weights,id',
            'products.*.customerCompanyGoodId' => 'required_if:products.*.status,REMOVE|required_if:products.*.status,RESTOCK|exists:products,id',
            'products.*.picture' => 'image|between:1,800|dimensions:ratio=1/1|mimes:png,jpg',
        ]);

        DB::beginTransaction();
        try {
            [$referenceProductId, $referenceTempProducts] = [array_map(function ($product) {
                return $product['customerCompanyGoodId'];
            }, array_filter($request->products, function ($product) {
                return ! empty($product['customerCompanyGoodId']);
            })), array_map(function ($product) {
                return $product['id'];
            }, array_filter($request->products, function ($product) {
                return ! empty($product['id']);
            }))];

            $referenceProducts = CustomerCompanyGood::whereIn('id', $referenceProductId)->get()->toArray();
            $updatedTempId = collect($request->products)->filter(function ($value, $key) {
                return ! empty($value['id']);
            })->map(function ($del) {
                return $del['id'];
            })->all();

            $referenceTemporaryProducts = CustomerTemporaryProduct::whereIn('id', $referenceTempProducts)->get()->toArray();
            $orderCode = ['in' => lastCompanyOrderCode('IN', $id), 'restock' => lastCompanyOrderCode('RESTOCK', $id), 'remove' => lastCompanyOrderCode('REMOVE', $id)];

            $default_data = [
                'id' => null,
                'transaction_created' => now()->format('Y-m-d'),
                'company_id' => $company->id,
                'user_id' => $user->id,
                'customerCompanyGoodId' => null,
                'name' => null,
                'status' => null,
                'picture' => null,
                'stock' => null,
                'stock_reference' => null,
                'price' => null,
                'buy_price' => null,
                'weight_id' => null,
                'accepted' => 0,
                'accepted_by' => null,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];

            $resultTempProduct = [];
            foreach ($request->products as $key => $value) {
                foreach ($default_data as $indexDefault => $valueDefault) {
                    $resultTempProduct[$key][$indexDefault] = (! empty($request->products[$key][$indexDefault])) ? (in_array($indexDefault, ['stock', 'price', 'buy_price']) ? str_replace(',', '.', str_replace('.', '', $request->products[$key][$indexDefault])) : $request->products[$key][$indexDefault]) : $valueDefault;

                    if ($indexDefault === 'picture') {
                        if (! empty($request->products[$key][$indexDefault])) {
                            $filename = md5($request->products[$key]['name'] . now()->format('Y-m-d h:i:s')) . '.' . $request->products[$key][$indexDefault]->extension();
                            $this->uploadAndWatermark($request->products[$key][$indexDefault], '', 'temp-customer-product', $filename);
                            $resultTempProduct[$key][$indexDefault] = $filename;
                        } else {
                            $resultTempProduct[$key][$indexDefault] = ($resultTempProduct[$key]['customerCompanyGoodId']) ? collect($referenceProducts)->filter(function ($ref) use ($resultTempProduct, $key) {
                                return $ref['id'] === $resultTempProduct[$key]['customerCompanyGoodId'];
                            })->first()['picture'] : 'default-product.png';
                        }
                    }

                    if ($indexDefault === 'status') {
                        $resultTempProduct[$key]['orderCode'] = ($resultTempProduct[$key]['id']) ? collect($referenceTemporaryProducts)->filter(function ($ref) use ($resultTempProduct, $key) {
                            return $ref['id'] === $resultTempProduct[$key]['id'];
                        })->first()['orderCode'] : $orderCode[strtolower($resultTempProduct[$key][$indexDefault])];
                        if (in_array($resultTempProduct[$key][$indexDefault], ['IN', 'RESTOCK'])) {
                            $resultTempProduct[$key][$indexDefault] = 'publish';
                        } else {
                            $resultTempProduct[$key][$indexDefault] = null;
                        }
                    }

                    if ($indexDefault === 'stock_reference') {
                        $resultTempProduct[$key][$indexDefault] = collect($referenceProducts)->filter(function ($ref) use ($resultTempProduct, $key) {
                            return $ref['id'] === $resultTempProduct[$key]['customerCompanyGoodId'];
                        })->first()['stock'] ?? 0;
                    }
                }
            }

            CustomerTemporaryProduct::whereNotIn('id', $updatedTempId)
                ->where(['transaction_created' => $id, 'accepted' => 0])
                ->delete();

            CustomerTemporaryProduct::upsert($resultTempProduct, ['id'], ['orderCode', 'transaction_created',  'user_id', 'company_id', 'customerCompanyGoodId', 'name', 'picture', 'stock', 'price', 'buy_price', 'weight_id', 'accepted', 'accepted_by', 'status']);

            $response = ['message' => 'Updating resource successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'Failed updating resource', 'error' => $th->getMessage()];
            $code = 422;
        }

        return response()->json($response, $code);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $company = $request->header('x-customer-company-id');

        DB::beginTransaction();
        try {
            $where = [
                'transaction_created' => $id,
                'accepted' => 0,
                'company_id' => $company->id,
            ];

            if (! in_array($user->role->name, ['Manager', 'Developer'])) {
                $where[] = ['user_id' => $user->id];
            }

            $builder = CustomerTemporaryProduct::where($where);
            $data = $builder->get();
            $builder->delete();

            foreach ($data as $key => $value) {
                if (Storage::disk('temp-customer-product')->exists($value['picture'])) {
                    Storage::disk('temp-customer-product')->delete($value['picture']);
                }
            }

            $response = ['message' => 'Deleting resource successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'Failed deleting resource', 'error' => $th->getMessage()];
            $code = 422;
        }

        return response()->json($response, $code);
    }
}
