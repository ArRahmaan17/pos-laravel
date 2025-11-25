<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompanyDiscount;
use App\Models\CustomerCompanyGood;
use App\Models\CustomerDetailProductTransaction;
use App\Models\CustomerProductTransaction;
use Exception;
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

    public function dataTable(Request $request)
    {
        $totalData = CustomerProductTransaction::with('details')->where([
            ['company_id', '=', session('userLogged')['company']['id']],
        ])->orderBy('id', 'asc')->join('users', 'users.id', '=', 'transactions.user_id')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerProductTransaction::with('details')->select('*')->join('users', 'users.id', '=', 'transactions.user_id');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $assets = $assets->where([
                ['company_id', '=', session('userLogged')['company']['id']],
            ])->get();
        } else {
            $assets = CustomerProductTransaction::with('details')->select('*')->join('users', 'users.id', '=', 'transactions.user_id')
                ->where('transactions.orderCode', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('transactions.total', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('transactions.discount', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('users.name', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where([
                ['company_id', '=', session('userLogged')['company']['id']],
            ])->get();

            $totalFiltered = CustomerProductTransaction::with('details')->select('*')->join('users', 'users.id', '=', 'transactions.user_id')
                ->where('transactions.orderCode', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('transactions.total', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('transactions.discount', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('users.name', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where([
                ['company_id', '=', session('userLogged')['company']['id']],
            ])->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['orderCode'] = $item->orderCode;
            $row['id'] = $item->id;
            $row['total'] = $item->total;
            $row['discount'] = $item->discount;
            $row['name'] = $item->name;
            $row['details'] = $item->details;
            $row['action'] = "<button class='btn btn-icon btn-success print-transaction' data-customer-product-transaction='".$item->orderCode."' ><i class='bx bxs-printer' ></i></button>";
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

    public function productDataTable(Request $request)
    {
        $totalData = CustomerCompanyGood::where([
            ['status', '=', 'publish'],
            ['company_id', '=', session('userLogged')['company']['id']],
        ])->orderBy('products.id', 'asc')
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
            $assets = $assets->where([
                ['status', '=', 'publish'],
                ['company_id', '=', session('userLogged')['company']['id']],
            ])->get();
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
            $assets = $assets->where([
                ['status', '=', 'publish'],
                ['company_id', '=', session('userLogged')['company']['id']],
            ])->get();

            $totalFiltered = CustomerCompanyGood::select('*')
                ->where('products.name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('products.price', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where([
                ['status', '=', 'publish'],
                ['company_id', '=', session('userLogged')['company']['id']],
            ])->count();
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
            $row['action'] = "<button class='btn btn-icon btn-success add-cart' data-customer-product='".$item->id."' ><i class='bx bxs-cart-add' ></i></button>";
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
        $totalData = CustomerCompanyDiscount::where([
            ['status', '=', 'publish'],
            ['company_id', '=', session('userLogged')['company']['id']],
        ])->orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerCompanyDiscount::select('*');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $assets = $assets->where([
                ['status', '=', 'publish'],
                ['company_id', '=', session('userLogged')['company']['id']],
            ])->get();
        } else {
            $assets = CustomerCompanyDiscount::select('*')
                ->where('code', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('description', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('max_transaction_discount', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('min_transaction_price', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('status', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('percentage', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where([
                ['status', '=', 'publish'],
                ['company_id', '=', session('userLogged')['company']['id']],
            ])->get();

            $totalFiltered = CustomerCompanyDiscount::select('*')
                ->where('code', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('description', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('max_transaction_discount', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('min_transaction_price', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('status', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('percentage', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where([
                ['status', '=', 'publish'],
                ['company_id', '=', session('userLogged')['company']['id']],
            ])->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $appliedDiscount = CustomerCompanyDiscount::appliedDiscounts($item->code);
            $row['code'] = $item->code;
            $row['id'] = $item->id;
            $row['description'] = $item->description;
            $row['percentage'] = $item->percentage;
            $row['max_transaction_discount'] = $item->max_transaction_discount;
            $row['min_transaction_price'] = $item->min_transaction_price;
            $row['applyLeft'] = (($item->maxApply == 0) ? 'Unlimited' : ($appliedDiscount < $item->maxApply)) ? ($item->maxApply - $appliedDiscount).' x' : '0 x';
            $row['action'] = (CustomerCompanyDiscount::appliedDiscounts($item->code) < $item->maxApply || $item->maxApply == 0) ? "<button class='btn btn-icon btn-success use-discount' data-customer-company-discount='".$item->id."' ><i class='bx bx-check-double' ></i></button>" : "<button class='btn btn-icon btn-danger disabled'><i class='bx bx-x' ></i></button>";
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
            $company_id = session('userLogged')['company']['id'];
            $goodIds = collect($request->transactions)->pluck('id');
            $goods = CustomerCompanyGood::where('company_id', $company_id)
                ->whereIn('id', $goodIds)
                ->get()
                ->keyBy('id');

            $total = array_reduce($request->transactions, function ($acc, $product) use ($goods) {
                return $acc += intval($goods[$product['id']]->price) * intval($product['quantity']);
            }, 0);
            $discount = 0;
            $discountId = null;
            if ($request->discount != null && $request->discount['id'] != null) {
                $data_discount = CustomerCompanyDiscount::find($request->discount['id']);
                $appliedDiscount = CustomerCompanyDiscount::appliedDiscounts($data_discount->code);
                if ($data_discount->maxApply != 0 && $data_discount->maxApply == $appliedDiscount) {
                    throw new Exception('Max applied discount already reached', 422);
                }
                if ($total >= $data_discount->min_transaction_price && ($data_discount->max_apply == 0 || $data_discount->max_apply >= CustomerCompanyDiscount::appliedDiscounts($data_discount->code))) {
                    $discount = (
                        floatval($total)
                        - floatval(($data_discount->max_transaction_discount != null) ? ($data_discount->max_transaction_discount * $data_discount->percentage / 100) : 0)
                    ) * $data_discount->percentage / 100;
                    $discountId = $data_discount->id;
                }
            }
            $data = [
                'orderCode' => $this->checkOrderCode($request->orderCode),
                'user_id' => session('userLogged')['user']['id'],
                'company_id' => session('userLogged')['company']['id'],
                'total' => $total,
                'discount' => $discount,
                'discountId' => $discountId,
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
                    'stock_reference' => $goods[$product['id']]->stock,
                    'price' => $goods[$product['id']]->price,
                    'total' => $goods[$product['id']]->price * intval($product['quantity']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            CustomerDetailProductTransaction::insert($dataDetail);
            $company_id = session('userLogged')['company']['id'];
            $goodIds = collect($request->transactions)->pluck('id');
            $goods = CustomerCompanyGood::where('company_id', $company_id)
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
                'lastOrderCode' => $data['orderCode'],
            ];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = [
                'message' => ($th->getCode() == 422) ? 'Failed creating transaction. '.$th->getMessage() : 'Failed creating transaction. Unexpected error on processing your transaction',
            ];
            $code = 422;
        }

        return response()->json($response, $code);
    }

    private function checkOrderCode(string $orderCode)
    {
        if (CustomerProductTransaction::where('orderCode', $orderCode)->count() == 0) {
            return $orderCode;
        } else {
            return lastCompanyOrderCode();
        }
    }

    private function checkDiscountCode($code)
    {
        $appliedDiscount = CustomerCompanyDiscount::appliedDiscounts($code);

        return CustomerCompanyDiscount::where([
            'company_id' => session('userLogged')['company']['id'],
            'code' => $code,
        ])->where('maxApply', '>', $appliedDiscount)->first();
    }

    private function checkProductStock($id, $quantities)
    {
        $goods = CustomerCompanyGood::whereIn('id', $id)->get()->toArray();
        $status_product = array_map(function ($good, $quantity) {
            return ['status' => $good['stock'] >= intval($quantity), 'id' => $good['id'], 'stock' => $good['stock']];
        }, $goods, $quantities);

        return $status_product;
    }

    public function validateTransactionItems(Request $request)
    {
        $status_product = $this->checkProductStock(collect($request->products)->pluck('id')->toArray(), collect($request->products)->pluck('quantity')->toArray());
        $isAppliedDiscount = ($request->discount) ? true : false;
        if ($isAppliedDiscount) {
            $status_discount = $this->checkDiscountCode($request->discount);
        }
        $data_success = [];
        $data_errors = [];
        if (in_array(false, array_map(function ($status) {
            return $status['status'];
        }, $status_product)) == false) {
            $data_success['products'] = array_filter($status_product, function ($status) {
                return $status['status'] == true;
            });
        } else {
            $data_errors['products'] = array_filter($status_product, function ($status) {
                return $status['status'] == false;
            });
        }
        if ($isAppliedDiscount) {

            if ($status_discount) {
                $data_success['discount'] = ['status' => true, 'code' => $status_discount->code];
            } else {
                $data_errors['discount'] = ['status' => false, 'code' => []];
            }
        }
        $response = ['data' => $data_success, 'errors' => $data_errors];
        $code = 200;
        $response['message'] = 'All products and discount code are valid';
        if (count($data_errors) > 0) {
            $response['message'] = 'we found some errors on stocks product or max applied discount code';
            $code = 422;
        }

        return response()->json($response, $code);
    }

    public function validateDiscountCode($discountCode)
    {
        $data = $this->checkDiscountCode($discountCode);
        $response = ['message' => 'Invalid discount code or max applied has been reached', 'data' => $data];
        $code = 404;
        if ($data) {
            $response = ['message' => 'Valid discount code, discount applied', 'data' => $data];
            $code = 200;
        }

        return response()->json($response, $code);
    }

    public function viewPdf(string $orderCode)
    {
        $data = CustomerProductTransaction::with('details.good')
            ->where('orderCode', $orderCode)
            ->where('company_id', session('userLogged')['company']['id'])
            ->first();
        if ($data) {
            $pdf = App::make('dompdf.wrapper');
            $pdf = $pdf->loadView('report.sales.transaction-receipt', ($data) ? $data->toArray() : [])->setPaper([0, 0, 300, 280], 'portrait');

            return $pdf->stream('Transaction-'.$orderCode.'.pdf');
        } else {
            return redirect()->route('dashboard.index')->with('error', 'Transaction not found <b>'.$orderCode.'</b>');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
