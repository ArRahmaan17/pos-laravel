<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerTemporaryProduct;
use App\Models\Product\CustomerCompanyGood;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductWeight;
use App\Traits\ImageHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerCompanyGoodController extends Controller
{
    use ImageHandler;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = ProductWeight::get();
        $categories = ProductCategory::with('category')->where('business_id', session('userLogged')['company']['business_id'])->get();

        return view('man.customer-company-good', compact('units', 'categories'));
    }

    public function dataTable(Request $request)
    {
        $totalData = CustomerCompanyGood::orderBy('products.id', 'asc')->where('company_id', session('userLogged')['company']['id'])
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerCompanyGood::with('unit')->select('*');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $assets = $assets->where('company_id', session('userLogged')['company']['id'])->get();
        } else {
            $assets = CustomerCompanyGood::with('unit')->select('*')
                ->where('products.name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('products.price', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where('company_id', session('userLogged')['company']['id'])->get();

            $totalFiltered = CustomerCompanyGood::select('*')
                ->where('products.name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('products.price', 'like', '%'.$request['search']['value'].'%');

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
            $row['price'] = $item->price;
            $row['buy_price'] = $item->buy_price;
            $row['stock'] = $item->stock;
            $row['unit'] = $item->unit->name;
            $row['weight_id'] = $item->unit->id;
            $row['weight_id'] = $item->unit->id;
            $row['picture'] = $item->picture;
            $row['status'] = ($item->status === 'archive') ? '<span class="badge bg-label-danger">'.$item->status.'</span>' : (($item->status === 'draft') ? '<span class="badge bg-label-warning">'.$item->status.'</span>' : '<span class="badge bg-label-success">'.$item->status.'</span>');
            $row['action'] = "<button class='btn btn-icon btn-outline-warning edit' data-customer-company-good='".$item->id."' ><i class='bx bx-pencil' ></i></button><button data-customer-company-good='".$item->id."' class='btn btn-icon btn-outline-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
            $row['action_temp'] = "<button class='btn btn-icon btn-outline-warning edit-temp' data-customer-company-good='".$item->id."' ><i class='bx bx-pencil' ></i></button><button data-customer-company-good='".$item->id."' class='btn btn-icon btn-outline-danger delete-temp'><i class='bx bxs-trash-alt' ></i></button>";
            $row['action_stocktaking'] = "<button type='button' class='btn btn-icon btn-outline-warning edit-stock' data-customer-company-good='".$item->id."' ><i class='bx bx-pencil' ></i></button>";
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
            $data = $request->except('_token', 'id');
            $data['picture'] = 'default-product.png';
            $data['company_id'] = session('userLogged')['company']['id'];
            $data['user_id'] = session('userLogged')['user']['id'];
            $data['price'] = str_replace(',', '.', str_replace('.', '', $request->price));
            $data['buy_price'] = str_replace(',', '.', str_replace('.', '', $request->buy_price));
            if ($request->picture) {
                $filename = md5($request->name.now()->format('Y-m-d h:i:s')).'.'.$request->file('picture')->clientExtension();
                $data['picture'] = $filename;
                $this->uploadAndWatermark($request->file('picture'), '', 'temp-customer-product', $filename);
            }
            $data['orderCode'] = lastCompanyOrderCode('IN');
            CustomerTemporaryProduct::create($data);
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

    public function storeTempProduct(string $date)
    {
        DB::beginTransaction();
        try {
            if (! getScope() !== 'user_created') {
                throw new Exception('Not Authorize');
            }
            $data = CustomerTemporaryProduct::with('reference')->whereDate('created_at', now()->format('Y-m-d'))->where(['company_id' => session('userLogged')['company']['id'], 'accepted' => 0])->get();
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
                    Storage::disk('public-asset')->move('temp-customer-product/'.$value['picture'], 'customer-product/'.$value['picture']);
                }
            }
            if (! empty($dataUpdate)) {
                CustomerCompanyGood::upsert($dataUpdate, ['id'], ['stock', 'name', 'picture', 'price', 'buy_price', 'weight_id']);
                foreach ($dataUpdate as $index => $value) {
                    if (Storage::disk('public-asset')->exists('temp-customer-product/'.$value['picture'])) {
                        Storage::disk('public-asset')->move('temp-customer-product/'.$value['picture'], 'customer-product/'.$value['picture']);
                        Storage::disk('public-asset')->delete('temp-customer-product/'.$value['picture']);
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
            CustomerTemporaryProduct::whereDate('created_at', now()->format('Y-m-d'))->where(['company_id' => session('userLogged')['company']['id'], 'accepted' => 0])->update(['accepted' => 1, 'accepted_by' => session('userLogged')['user']['id']]);
            $response = ['message' => 'creating resource successfully'];
            $code = 200;
            DB::commit();
        } catch (Exception $th) {
            DB::rollBack();
            $response = ['message' => 'failed creating resource'.($th->getCode() === 0) ? ', '.$th->getMessage() : ''];
            $code = 422;
        }

        return response()->json($response, $code);
    }

    public function tempProduct()
    {
        $data = CustomerTemporaryProduct::with('unit', 'reference')->whereDate('created_at', now()->format('Y-m-d'))->where(['company_id' => session('userLogged')['company']['id'], 'accepted' => 0])->get();
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
    public function show(string $id)
    {
        $data = CustomerCompanyGood::where([['id', $id], ['company_id', session('userLogged')['company']['id']]])->first();
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
            $data['company_id'] = session('userLogged')['company']['id'];
            $data['user_id'] = session('userLogged')['user']['id'];
            $data['customerCompanyGoodId'] = $id;
            $data['transaction_created'] = now()->format('Y-m-d');
            $data['orderCode'] = lastCompanyOrderCode('ADJ');
            CustomerTemporaryProduct::create($data);
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
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'orderCode' => lastCompanyOrderCode('REMOVE'),
                'customerCompanyGoodId' => $id,
                'company_id' => session('userLogged')['company']['id'],
                'user_id' => session('userLogged')['user']['id'],
            ];
            CustomerTemporaryProduct::create($data);
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
