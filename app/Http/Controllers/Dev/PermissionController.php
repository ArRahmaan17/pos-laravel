<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = ['children' => []];

        $routes = collect(Route::getRoutes())->toArray();
        foreach ($routes as $route) {
            $route = collect($route)->toArray();
            if (array_find(
                $route['action']['middleware'],
                fn($v) => $v === 'App\Http\Middleware\checkPageAuthorization'
            )) {
                if (!array_find($route['action']['middleware'], fn($v) => $v === 'App\Http\Middleware\AuthorizationOnly')) {
                    unset($route['action']['uses']);
                    $p = explode('.', $route['action']['as']);
                    $count = count($p);
                    $p = explode('.', $route['action']['as']);
                    $id = $p[count($p) - 1];
                    foreach ($p as $key => $value) {
                        if ($value !== 'data-table') {
                            if ($value === $id) {
                                $result['children'][] = [
                                    'uri' => $route['uri'],
                                    'unique' => $route['uri'] . '_' . ($route['methods'][0]),
                                    'id' => $id,
                                    'parent' => $p[$key - 1],
                                    'action' => $route['action'],
                                ];
                            } else {
                                $result['children'][] = [
                                    'uri' => '#' . $value,
                                    'unique' => '#' . $value,
                                    'id' => $value,
                                    'parent' => $key === 0 ? null : $p[$key - 1],
                                    'action' => $route['action'],
                                ];
                            }
                        }
                    }
                }
            }
        }
        $result['children'] = removeDuplicate($result['children'], 'unique');
        $routes = arrayTree($result['children'], 'id');

        return view('dev.permission', compact('routes'));
    }

    public function dataTable(Request $request)
    {
        $totalData = Permission::orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = Permission::select('*');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->get();
        } else {
            $assets = Permission::select('*')
                ->where('name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('route', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->get();

            $totalFiltered = Permission::select('*')
                ->where('name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('route', 'like', '%' . $request['search']['value'] . '%');

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
            $row['place'] = $item->place;
            $row['child'] = Permission::getChildMenu($item->id);
            $row['action'] = "<button class='btn btn-icon btn-outline-success parent' data-permission='" . $item->id . "' ><i class='bx bx-plus' ></i></button><button class='btn btn-icon btn-outline-warning edit' data-permission='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-permission='" . $item->id . "' class='btn btn-icon btn-outline-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
        dd($request->all());
        DB::beginTransaction();
        $request->validate([
            'name' => 'required|min:2|max:20|unique:permissions,name',
            'route' => 'required',
            'icon' => 'required',
            'parent' => 'required',
        ]);
        try {
            $data = $request->except('_token', 'id');
            $data['dev_only'] = isset($data['dev_only']) ? 1 : 0;
            Permission::create($data);
            DB::commit();
            $response = ['message' => 'Resource create successfully'];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $code = 422;
            $response = ['message' => 'Failed creating resource'];
        }

        return response()->json($response, $code);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Permission::with(['child'])->find($id)->setHidden([]);
        $response = ['message' => 'Showing resource successfully', 'data' => $data];
        $code = 200;
        if (empty($data)) {
            $response = ['message' => 'Failed showing resource', 'data' => $data];
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
            'name' => 'required|min:2|max:15|unique:permissions,name,' . $id,
            'route' => 'required',
            'icon' => 'required',
            'parent' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token', 'id');
            $data['dev_only'] = isset($data['dev_only']) ? 1 : 0;
            Permission::find($id)->update($data);
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
            if (empty(collect(Permission::with('child')->find($id)->child)->toArray())) {
                Permission::destroy($id);
                DB::commit();
                $response = ['message' => 'Deleting resource successfully'];
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
