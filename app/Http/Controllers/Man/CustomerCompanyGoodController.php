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
use League\CommonMark\Extension\SmartPunct\EllipsesParser;

class CustomerCompanyGoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = AppGoodUnit::get();

        return view('man.customer-company-good', compact('units'));
    }

    public function dataTable(Request $request)
    {
        $totalData = CustomerCompanyGood::orderBy('customer_company_goods.id', 'asc')->where('companyId', session('userLogged')['company']['id'])
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerCompanyGood::with('unit')->select('*');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->where('companyId', session('userLogged')['company']['id'])->get();
        } else {
            $assets = CustomerCompanyGood::with('unit')->select('*')
                ->where('customer_company_goods.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('customer_company_goods.price', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where('companyId', session('userLogged')['company']['id'])->get();

            $totalFiltered = CustomerCompanyGood::select('*')
                ->where('customer_company_goods.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('customer_company_goods.price', 'like', '%' . $request['search']['value'] . '%');

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
            $row['price'] = $item->price;
            $row['buyPrice'] = $item->buyPrice;
            $row['stock'] = $item->stock;
            $row['unit'] = $item->unit->name;
            $row['unitId'] = $item->unit->id;
            $row['picture'] = $item->picture;
            $row['status'] = ($item->status == 'archive') ? '<span class="badge bg-label-danger">' . $item->status . '</span>' : (($item->status == 'draft') ? '<span class="badge bg-label-warning">' . $item->status . '</span>' : '<span class="badge bg-label-success">' . $item->status . '</span>');
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-customer-company-good='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-customer-company-good='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
            $row['action_temp'] = "<button class='btn btn-icon btn-warning edit-temp' data-customer-company-good='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-customer-company-good='" . $item->id . "' class='btn btn-icon btn-danger delete-temp'><i class='bx bxs-trash-alt' ></i></button>";
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
            'name' => 'required|min:6|max:40|unique:customer_temporary_products,name|unique:customer_company_goods,name',
            'stock' => 'required|max:8',
            'price' => 'required|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'buyPrice' => 'required|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'status' => 'required|in:archive,draft,publish',
            'companyId' => 'required|exists:customer_companies,id',
            'unitId' => 'required|exists:app_good_units,id',
            'picture' => 'image|between:1,800|dimensions:ratio=1/1|mimes:png,jpg',
        ], [
            'unitId' => 'The unit field is required.',
            'companyId' => 'The company field is required.',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token', 'id');
            $data['picture'] = 'default-product.png';
            $data['companyId'] = session('userLogged')['company']['id'];
            $data['userId'] = session('userLogged')['user']['id'];
            $data['price'] = str_replace(',', '.', str_replace('.', '', $request->price));
            $data['buyPrice'] = str_replace(',', '.', str_replace('.', '', $request->buyPrice));
            if ($request->picture) {
                $filename = md5($request->name . now()->format('Y-m-d h:i:s')) . '.' . $request->file('picture')->clientExtension();
                $data['picture'] = $filename;
                if (Storage::disk('public-asset')->directories('temp-customer-product')) {
                    Storage::disk('public-asset')->makeDirectory('temp-customer-product');
                }
                Storage::disk('temp-customer-product')->putFileAs('/', $request->picture, $filename);
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

    public function storeTempProduct(String $date)
    {
        DB::beginTransaction();
        try {
            if (!in_array(getRole(), ['Developer', 'Manager'])) {
                throw new Exception('Not Authorize');
            }
            $data = CustomerTemporaryProduct::with('reference')->whereDate('created_at', now()->format('Y-m-d'))->where(['companyId' => session('userLogged')['company']['id'], 'accepted' => 0])->get();
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
                        Storage::disk('public-asset')->delete('temp-customer-product/' . $value['picture']);
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
            CustomerTemporaryProduct::whereDate('created_at', now()->format('Y-m-d'))->where(['companyId' => session('userLogged')['company']['id'], 'accepted' => 0])->update(['accepted' => 1, 'accepted_by' => session('userLogged')['user']['id']]);
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

    public function tempProduct()
    {
        $data = CustomerTemporaryProduct::with('unit', 'product')->whereDate('created_at', now()->format('Y-m-d'))->where(['companyId' => session('userLogged')['company']['id'], 'accepted' => 0])->get();
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
            'name' => 'required|min:6|max:40|unique:customer_company_goods,name,' . $id . '|unique:customer_temporary_products,name',
            'id' => 'required|numeric',
            'stock' => 'required|max:8',
            'price' => 'required|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'buyPrice' => 'required|max:16|regex:/(\d{1,3}(?:\.\d{3})*)(?:,(\d{2}))/i',
            'status' => 'required|in:archive,draft,publish',
            'companyId' => 'required|exists:customer_companies,id',
            'picture' => 'image|between:1,800|dimensions:ratio=1/1|mimes:png,jpg',
        ], [
            'unitId' => 'The unit field is required.',
            'companyId' => 'The unit field is required.',
        ]);
        DB::beginTransaction();
        try {
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
