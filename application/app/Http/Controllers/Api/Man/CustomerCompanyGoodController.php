<?php

namespace App\Http\Controllers\Api\Man;

use App\Http\Controllers\Controller;
use App\Models\Product\CustomerCompanyGood;
use App\Traits\ImageHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use ImageHandler;

    public function dataTable(Request $request)
    {
        $company = $request->header('x-customer-company-id');
        $searchable = [];
        try {
            $totalData = CustomerCompanyGood::orderBy('products.id', 'asc')->where('company_id', $company)
                ->count();
            if (empty($request['search'])) {
                $assets = CustomerCompanyGood::with('unit')->select('*');

                if ($request['length'] != '-1') {
                    $assets->limit($request['length']);
                    if ($request['start'] > 0) {
                        $assets->where('id', '>', $request['start']);
                    }
                }
                if (isset($request['order'][0]['column'])) {
                    $assets->orderByRaw($request['order']['name'].' '.$request['order']['dir']);
                }
                $assets = $assets->where('company_id', $company)->get();
            } else {
                $assets = CustomerCompanyGood::with('unit')->select('*')
                    ->search($request['search']);

                if (isset($request['order']['name'])) {
                    $assets->orderByRaw($request['order']['name'].' '.$request['order']['dir']);
                }
                if ($request['length'] != '-1') {
                    $assets->limit($request['length']);
                    if ($request['start'] > 0) {
                        $assets->where('id', '>', $request['start']);
                    }
                }
                $assets = $assets->where('company_id', $company)->get();

                $totalFiltered = CustomerCompanyGood::select('*')
                    ->search($request['search']);

                if (isset($request['order'][0]['column'])) {
                    $totalFiltered->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
                }
                $totalFiltered = $totalFiltered->count();
            }
            $totalFiltered = $totalData;
            $dataFiltered = [];
            foreach ($assets as $_ => $item) {
                $row = [];
                $row['id'] = $item->id;
                $row['name'] = $item->name;
                $row['price'] = $item->price;
                $row['buy_price'] = $item->buy_price;
                $row['stock'] = $item->stock;
                $row['status'] = $item->status;
                $row['unit'] = $item->unit->name;
                $row['weight_id'] = $item->unit->id;
                $row['type'] = $item->type->name;
                $row['category_id'] = $item->type->id;
                $row['picture'] = $item->picture;
                $dataFiltered[] = $row;
            }

            if ($totalFiltered === 0 || count($dataFiltered) === 0) {
                throw new Exception('No data found');
            }
            $response = [
                'recordsFiltered' => $totalFiltered,
                'recordsTotal' => count($dataFiltered),
                'data' => $dataFiltered,
                'searchable' => $searchable,
                'message' => 'Data retrieved successfully',
            ];
            $code = 200;
        } catch (\Throwable $th) {
            $response = ['message' => 'failed retrieving data'];
            $code = 404;
        }

        return response()->json($response, $code);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:6|max:40|unique:adjustment_products,name|unique:products,name',
            'stock' => 'required|max:8',
            'price' => 'required|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'buy_price' => 'required|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'status' => 'required|in:archive,draft,publish',
            'company_id' => 'required|exists:companies,id',
            'weight_id' => 'required|exists:product_weights,id',
            'category_id' => 'required|exists:product_categories,id',
            'picture' => 'image|between:1,800|dimensions:ratio=1/1|mimes:png,jpg',
        ], [
            'weight_id' => 'The unit field is required.',
            'category_id' => 'The type field is required.',
            'company_id' => 'The company field is required.',
        ]);
        DB::beginTransaction();
        try {
            $user = $request->user();
            $company = $request->header('x-customer-company-id');

            $data = $request->except('_token', 'id');
            $data['picture'] = 'default-product.png';
            $data['company_id'] = $company;
            $data['user_id'] = $user->id;
            $data['price'] = str_replace(',', '.', str_replace('.', '', $request->price));
            $data['buy_price'] = str_replace(',', '.', str_replace('.', '', $request->buy_price));
            if ($request->picture) {
                $filename = md5($request->name.now()->format('Y-m-d h:i:s')).'.'.$request->file('picture')->clientExtension();
                $data['picture'] = $filename;
                $this->uploadAndWatermark($request->file('picture'), '', 'temp-customer-product', $filename);
            }
            $data['orderCode'] = lastCompanyOrderCode('IN');
            TemporaryProduct::create($data);
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

    public function tempProduct(Request $request)
    {
        $user = $request->user();
        $company = $request->header('x-customer-company-id');

        $data = TemporaryProduct::with('unit', 'reference')->whereDate('created_at', now()->format('Y-m-d'))->where(['company_id' => $company, 'accepted' => 0])->get();
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
    public function show(Request $request, string $id)
    {
        $company = $request->header('x-customer-company-id');

        $data = CustomerCompanyGood::where([['id', $id], ['company_id', $company]])->first();
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
            'name' => 'required|min:6|max:40|unique:products,name,'.$id.'|unique:adjustment_products,name, '.$id,
            'id' => 'required|numeric',
            'stock' => 'required|max:8',
            'price' => 'required|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'buy_price' => 'required|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'status' => 'required|in:archive,draft,publish',
            'company_id' => 'required|exists:companies,id',
            'weight_id' => 'required|exists:product_weights,id',
            'category_id' => 'required|exists:product_categories,id',
            'picture' => 'image|between:1,800|dimensions:ratio=1/1|mimes:png,jpg',
        ], [
            'weight_id' => 'The unit field is required.',
            'category_id' => 'The type field is required.',
            'company_id' => 'The unit field is required.',
        ]);
        DB::beginTransaction();
        try {
            $user = $request->user();
            $company = $request->header('x-customer-company-id');

            $data = $request->except('_token', 'id');
            $referenceProduct = CustomerCompanyGood::find($id);
            $data['picture'] = $referenceProduct->picture;
            if ($request->file('picture')) {
                $filename = md5($request->name.now()->format('Y-m-d h:i:s')).'.'.$request->file('picture')->clientExtension();
                $data['picture'] = $filename;
                $this->uploadAndWatermark($request->file('picture'), '', 'temp-customer-product', $filename);
            }
            $data['price'] = str_replace(',', '.', str_replace('.', '', $request->price));
            $data['buy_price'] = str_replace(',', '.', str_replace('.', '', $request->buy_price));
            $data['stock'] = str_replace(',', '.', str_replace('.', '', $request->stock));
            $data['stock_reference'] = str_replace(',', '.', str_replace('.', '', $referenceProduct->stock));
            $data['company_id'] = $company;
            $data['user_id'] = $user->id;
            $data['customerCompanyGoodId'] = $id;
            $data['transaction_created'] = now()->format('Y-m-d');
            $data['orderCode'] = lastCompanyOrderCode('ADJ');
            TemporaryProduct::create($data);
            $response = ['message' => 'updating resource successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            $response = ['message' => 'failed updating resource'];
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
            $user = $request->user();
            $company = $request->header('x-customer-company-id');

            $data = [
                'orderCode' => lastCompanyOrderCode('REMOVE'),
                'customerCompanyGoodId' => $id,
                'company_id' => $company,
                'user_id' => $user->id,
            ];
            TemporaryProduct::create($data);
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
