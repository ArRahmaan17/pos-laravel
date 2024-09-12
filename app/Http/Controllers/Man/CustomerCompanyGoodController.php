<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\AppGoodUnit;
use App\Models\CustomerCompanyGood;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerCompanyGoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = AppGoodUnit::where('id', '<>', 1)->get();
        return view('man.customer-company-good', compact('units'));
    }

    public function dataTable(Request $request)
    {
        $totalData = CustomerCompanyGood::orderBy('customer_company_goods.id', 'asc')
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
            $assets = $assets->get();
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
            $assets = $assets->get();

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
            $row['stock'] = $item->stock;
            $row['unit'] = $item->unit->name;
            $row['status'] = ($item->status == 'archive') ? '<span class="badge bg-label-danger">' . $item->status . '</span>' : (($item->status == 'draft') ? '<span class="badge bg-label-warning">' . $item->status . '</span>' : '<span class="badge bg-label-success">' . $item->status . '</span>');
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-customer-company-good='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-customer-company-good='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
            'name' => 'required|min:6|max:40',
            'stock' => 'required|numeric|max_digits:8',
            'price' => 'required|numeric|max_digits:16',
            'status' => 'required|in:archive,draft,publish',
            'companyId' => 'required',
            'unitId' => 'required',
            'picture' => 'image|between:50,800|dimensions:ratio=1/1',
        ], [
            'unitId' => 'The unit field is required.',
            'companyId' => 'The company field is required.',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token', 'id');
            $data['picture'] = 'customer-product/default-product.webp';
            if ($request->picture) {
                $filename = md5($request->name . now('Asia/Jakarta')->format('Y-m-d')) . '.' . $request->file('picture')->clientExtension();
                $data['picture'] = $filename;
                Storage::disk('customer-product')->putFileAs('/', $request->picture, $filename);
            }
            CustomerCompanyGood::create($data);
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = CustomerCompanyGood::where([['id', $id], ['companyId', session('userLogged')->company->id ?? 10]])->first();
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
            'name' => 'required|min:6|max:40',
            'id' => 'required|numeric',
            'stock' => 'required|numeric|max_digits:8',
            'price' => 'required|numeric|max_digits:16',
            'status' => 'required|in:archive,draft,publish',
            'unitId' => 'required',
            'companyId' => 'required',
            'picture' => 'image|between:50,800|dimensions:ratio=1/1',
        ], [
            'unitId' => 'The unit field is required.',
            'companyId' => 'The unit field is required.',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token', 'id');
            if ($request->file('picture')) {
                Storage::disk('customer-product')->delete(CustomerCompanyGood::find($id)->picture);
                $filename = md5($request->name . now('Asia/Jakarta')->format('Y-m-d')) . '.' . $request->file('picture')->clientExtension();
                $data['picture'] = $filename;
                Storage::disk('customer-product')->putFileAs('/', $request->file('picture'), $filename);
            }
            CustomerCompanyGood::where([
                ['id', $id],
                ['companyId', session('userLogged')->company->id ?? 10]
            ])->update($data);
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
            Storage::disk('customer-product')->delete(CustomerCompanyGood::find($id)->picture);
            CustomerCompanyGood::where([
                ['id', $id],
                ['companyId', session('userLogged')->company->id ?? 10]
            ])->delete();
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
