<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerRoleAccessibility;
use Illuminate\Http\Request;

class CustomerRoleAccessibilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('man.customer-role-accessibility');
    }

    public function dataTable(Request $request)
    {
        $where = [['userId', '=', session('userLogged')->user->id]];
        if (getRole() === "Developer") {
            $where = [['userId', '<>', 0]];
        }
        $totalData = CustomerRoleAccessibility::where($where)->orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerRoleAccessibility::select('*');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->where($where)->get();
        } else {
            $assets = CustomerRoleAccessibility::select('*')
                ->where('name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('description', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where($where)->get();

            $totalFiltered = CustomerRoleAccessibility::select('*')
                ->where('name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('description', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where($where)->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['name'] = $item->name;
            $row['description'] = $item->description;
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-customer-role='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-customer-role='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
