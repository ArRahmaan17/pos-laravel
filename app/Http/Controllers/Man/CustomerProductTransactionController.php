<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompanyDiscount;
use App\Models\CustomerCompanyGood;
use App\Models\CustomerDetailProductTransaction;
use App\Models\CustomerProductTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class CustomerProductTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('man.customer-product-transaction');
    }

    public function productDataTable(Request $request)
    {
        $totalData = CustomerCompanyGood::where('status', 'publish')->orderBy('customer_company_goods.id', 'asc')
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
            $assets = $assets->where('status', 'publish')->get();
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
            $assets = $assets->where('status', 'publish')->get();

            $totalFiltered = CustomerCompanyGood::select('*')
                ->where('customer_company_goods.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('customer_company_goods.price', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where('status', 'publish')->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['name'] = $item->name;
            $row['id'] = $item->id;
            $row['price'] = $item->price;
            $row['stock'] = $item->stock;
            $row['unit'] = $item->unit->name;
            $row['picture'] = $item->picture;
            $row['action'] = "<button class='btn btn-icon btn-success add-cart' data-customer-product='" . $item->id . "' ><i class='bx bx-plus' ></i></button>";
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
    public function discountDataTable(Request $request)
    {
        $totalData = CustomerCompanyDiscount::orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerCompanyDiscount::select('*');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->get();
        } else {
            $assets = CustomerCompanyDiscount::select('*')
                ->where('code', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('description', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('maxTransactionDiscount', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('minTransactionPrice', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('status', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('percentage', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->get();

            $totalFiltered = CustomerCompanyDiscount::select('*')
                ->where('code', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('description', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('maxTransactionDiscount', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('minTransactionPrice', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('status', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('percentage', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['code'] = $item->code;
            $row['id'] = $item->id;
            $row['description'] = $item->description;
            $row['percentage'] = $item->percentage;
            $row['maxTransactionDiscount'] = $item->maxTransactionDiscount;
            $row['minTransactionPrice'] = $item->minTransactionPrice;
            $row['action'] = "<button class='btn btn-icon btn-success use-discount' data-customer-company-discount='" . $item->id . "' ><i class='bx bx-check-double' ></i></button>";
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
        DB::beginTransaction();
        try {
            $companyId = session('userLogged')['company']['id'];
            $goodIds = collect($request->transactions)->pluck('id');
            $goods = CustomerCompanyGood::where('companyId', $companyId)
                ->whereIn('id', $goodIds)
                ->get()
                ->keyBy('id');

            $total = array_reduce($request->transactions, function ($acc, $product) use ($goods) {
                return $acc += intval($goods[$product['id']]->price) * intval($product['quantity']);
            }, 0);
            $data_discount = CustomerCompanyDiscount::where('id', $request->discount['id'])->first();
            $discount = 0;
            if ($total >= $data_discount->minTransactionPrice) {
                $discount = (
                    floatval($total)
                    - floatval(($data_discount->maxTransactionDiscount != null) ? ($data_discount->maxTransactionDiscount * $data_discount->percentage / 100) : 0)
                ) * $data_discount->percentage / 100;
            }
            $data = [
                'orderCode' => $this->checkOrderCode($request->orderCode),
                'userId' => session('userLogged')['user']['id'],
                'companyId' => session('userLogged')['company']['id'],
                'total' => $total,
                'discount' => $discount,
                'discountCode' => $data_discount->code,
            ];
            CustomerProductTransaction::create($data);
            $goodIds = collect($request->transactions)->pluck('id');
            $goods = CustomerCompanyGood::whereIn('id', $goodIds)->get()->keyBy('id');

            $dataDetail = [];
            foreach ($request->transactions as $index => $product) {
                $dataDetail[] = [
                    'orderCode' => $data['orderCode'],
                    'goodId' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $goods[$product['id']]->price,
                    'total' => $goods[$product['id']]->price * intval($product['quantity']),
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ];
            }
            CustomerDetailProductTransaction::insert($dataDetail);
            $companyId = session('userLogged')['company']['id'];
            $goodIds = collect($request->transactions)->pluck('id');
            $goods = CustomerCompanyGood::where('companyId', $companyId)
                ->whereIn('id', $goodIds)
                ->get();

            foreach ($request->transactions as $product) {
                $goods->where('id', $product['id'])->first()->decrement('stock', intval($product['quantity']));
            }
            $goods->push();
            DB::commit();
            $response = [
                'message' => 'transaction created successfully',
                'orderCode' => lastCompanyOrderCode(),
                'lastOrderCode' => $data['orderCode']
            ];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed creating transaction'];
            $code = 422;
        }
        return response()->json($response, $code);
    }
    public function checkOrderCode(string $orderCode)
    {
        if (CustomerProductTransaction::where('orderCode',)->count() == 0) {
            return $orderCode;
        } else {
            return lastCompanyOrderCode();
        }
    }
    public function validateDiscountCode($discountCode)
    {
        $data = CustomerCompanyDiscount::where('companyId', session('userLogged')['company']['id'])
            ->where('code', $discountCode)->first();
        $response = ['message' => 'invalid discount code', 'data' => $data];
        $code = 404;
        if ($data) {
            $response = ['message' => 'valid discount code', 'data' => $data];
            $code = 200;
        }
        return response()->json($response, $code);
    }
    public function viewPdf(string $orderCode)
    {
        $data = CustomerProductTransaction::with('details.good')
            ->where('orderCode', $orderCode)
            ->where('companyId', session('userLogged')['company']['id'])
            ->first();
        $pdf = App::make('dompdf.wrapper');
        $pdf = $pdf->loadView('report.transaction-receipt', ($data) ? $data->toArray() : [])->setPaper(array(0, 0, 300, 280), 'portrait');
        return $pdf->stream('Transaction-' . $orderCode . '.pdf');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
