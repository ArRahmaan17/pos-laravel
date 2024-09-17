<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompanyGood;
use Illuminate\Http\Request;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
