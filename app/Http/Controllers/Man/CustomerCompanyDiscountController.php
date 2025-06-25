<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompanyDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerCompanyDiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('man.customer-company-discount');
    }

    public function dataTable(Request $request)
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
                ->orWhere('maxApply', 'like', '%' . $request['search']['value'] . '%')
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
                ->orWhere('maxApply', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('percentage', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['code'] = $item->code;
            $row['description'] = $item->description;
            $row['percentage'] = $item->percentage;
            $row['max_discount'] = $item->maxTransactionDiscount;
            $row['min_transaction'] = $item->minTransactionPrice;
            $row['max_apply'] = $item->maxApply == 0 ? 'Unlimited' : $item->maxApply . 'x';
            $row['status'] = ($item->status == 'archive') ? '<span class="badge bg-label-danger">' . $item->status . '</span>' : (($item->status == 'draft') ? '<span class="badge bg-label-warning">' . $item->status . '</span>' : '<span class="badge bg-label-success">' . $item->status . '</span>');
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-customer-company-discount='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-customer-company-discount='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
            'code' => 'required|unique:customer_company_discounts,code',
            'description' => 'required|min:5|max:150',
            'percentage' => 'required|min:2|max:4',
            'maxTransactionDiscount' => 'required',
            'minTransactionPrice' => 'required',
            'status' => 'required|in:archive,draft,publish',
            'maxApply' => 'required|min:0',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token');
            $data['percentage'] = intval(implode('', explode('%', $data['percentage'])));
            $data['maxTransactionDiscount'] = intval(convertStringToNumber($data['maxTransactionDiscount']));
            $data['minTransactionPrice'] = intval(convertStringToNumber($data['minTransactionPrice']));
            $data['companyId'] = session('userLogged')['company']['id'];
            $data['maxApply'] = convertStringToNumber($data['maxApply']);
            CustomerCompanyDiscount::create($data);
            DB::commit();
            $response = ['message' => 'resources created successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed creating resources'];
            $code = 422;
        }

        return response()->json($response, $code);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $discount = CustomerCompanyDiscount::where('id', $id)->where('companyId', session('userLogged')['company']['id'])->first();
        $response = ['message' => 'failed showing resources', 'data' => $discount];
        $code = 404;
        if ($discount) {
            $response = ['message' => 'showing resources successfully', 'data' => $discount];
            $code = 200;
        }

        return response()->json($response, $code);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'code' => 'required|unique:customer_company_discounts,code,' . $id,
            'description' => 'required|min:5|max:150',
            'percentage' => 'required|min:2|max:4',
            'minTransactionPrice' => 'required',
            'status' => 'required|in:archive,draft,publish',
            'maxApply' => 'required|min:0',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token');
            $data['percentage'] = intval(implode('', explode('%', $data['percentage'])));
            $data['maxTransactionDiscount'] = $request->has('maxTransactionDiscount') ? intval(convertStringToNumber($data['maxTransactionDiscount'])) : null;
            $data['minTransactionPrice'] = intval(convertStringToNumber($data['minTransactionPrice']));
            $data['companyId'] = session('userLogged')['company']['id'];
            $data['maxApply'] = convertStringToNumber($data['maxApply']);
            CustomerCompanyDiscount::where(['id' => $id, 'companyId' => session('userLogged')['company']['id']])->update($data);
            DB::commit();
            $response = ['message' => 'resources updated successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed updating resources'];
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
            CustomerCompanyDiscount::where(['id' => $id, 'companyId' => session('userLogged')['company']['id']])->delete();
            DB::commit();
            $response = ['message' => 'resources deleted successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed deleting resources'];
            $code = 422;
        }

        return response()->json($response, $code);
    }
}
