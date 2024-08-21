<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use App\Models\AppRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dev.app-role');
    }
    public function dataTable(Request $request)
    {
        $totalData = AppRole::orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = AppRole::select('*');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->get();
        } else {
            $assets = AppRole::select('*')
                ->where('name', 'ilike', '%' . $request['search']['value'] . '%')
                ->where('description', 'ilike', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->get();

            $totalFiltered = AppRole::select('*')
                ->where('name', 'ilike', '%' . $request['search']['value'] . '%')
                ->where('description', 'ilike', '%' . $request['search']['value'] . '%');

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
            $row['description'] = $item->description;
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-app-role='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-app-role='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
        $request->validate([
            'name' => 'required|min:2|max:10|unique:app_roles,name',
            'description' => 'required|min:6|max:100'
        ]);
        try {
            AppRole::create($request->except('_token', 'id'));
            DB::commit();
            $response = ['message' => 'App Role create successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $code = 422;
            $response = ['message' => 'Failed creating App Role'];
        }
        return response()->json($response, $code);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = AppRole::find($id);
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
            'id' => 'required',
            'name' => 'required|unique:app_roles,name,' . $id,
            'description' => 'required|min:6|max:100',
        ]);
        DB::beginTransaction();
        try {
            AppRole::find($id)->update($request->except('_token', 'id'));
            $response = ['message' => 'Updating resource successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'Failed updating resource'];
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
            if (empty(collect(AppRole::with('role_users')->find($id)->role_users)->toArray())) {
                AppRole::destroy($id);
                DB::commit();
                $response = ['message' => 'deleting resource successfully'];
                $code = 200;
            } else {
                $response = ['message' => "Failed deleting resource. This data is still being used in other data. You can't delete it until it's removed from those data"];
                $code = 422;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed deleting resource'];
            $code = 422;
        }
        return response()->json($response, $code);
    }
}
