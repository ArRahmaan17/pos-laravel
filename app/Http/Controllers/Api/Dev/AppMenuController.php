<?php

namespace App\Http\Controllers\Api\Dev;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class AppMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $routes = Route::getRoutes()->getRoutesByMethod()['GET'];
            $menus = Permission::orderBy('parent', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'menus' => $menus,
                    'routes' => array_keys($routes),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve app menus',
            ], 500);
        }
    }

    public function dataTable(Request $request)
    {
        try {
            $totalData = Permission::orderBy('id', 'asc')->count();
            $totalFiltered = $totalData;

            if (empty($request['search']['value'])) {
                $assets = Permission::select('*');

                if ($request['length'] != '-1') {
                    $assets->limit($request['length']);
                    if ($request['start'] > 0) {
                        $assets->where('id', '>', $request['start']);
                    }
                }
                if (isset($request['order'][0]['column'])) {
                    $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
                }
                $assets = $assets->get();
            } else {
                $assets = Permission::select('*')
                    ->where('name', 'like', '%'.$request['search']['value'].'%')
                    ->orWhere('route', 'like', '%'.$request['search']['value'].'%');

                if (isset($request['order'][0]['column'])) {
                    $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
                }
                if ($request['length'] != '-1') {
                    $assets->limit($request['length']);
                    if ($request['start'] > 0) {
                        $assets->where('id', '>', $request['start']);
                    }
                }
                $assets = $assets->get();

                $totalFiltered = Permission::select('*')
                    ->where('name', 'like', '%'.$request['search']['value'].'%')
                    ->orWhere('route', 'like', '%'.$request['search']['value'].'%');

                if (isset($request['order'][0]['column'])) {
                    $totalFiltered->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
                }
                $totalFiltered = $totalFiltered->count();
            }

            $dataFiltered = [];
            foreach ($assets as $index => $item) {
                $row = [];
                $row['order_number'] = $request['start'] + ($index + 1);
                $row['id'] = $item->id;
                $row['name'] = $item->name;
                $row['route'] = $item->route;
                $row['parent'] = $item->parent;
                $row['icon'] = $item->icon;
                $row['created_at'] = $item->created_at;
                $row['updated_at'] = $item->updated_at;
                $dataFiltered[] = $row;
            }

            $response = [
                'draw' => $request['draw'],
                'recordsFiltered' => $totalFiltered,
                'recordsTotal' => count($dataFiltered),
                'aaData' => $dataFiltered,
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data table',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required|min:2|max:50|unique:permissions,name',
                'route' => 'required|min:2|max:100',
                'parent' => 'nullable|integer',
                'icon' => 'nullable|string|max:50',
            ]);

            $appMenu = Permission::create($request->only(['name', 'route', 'parent', 'icon']));
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'data' => $appMenu,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed creating Permission',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $appMenu = Permission::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $appMenu,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Permission not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Permission',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $appMenu = Permission::findOrFail($id);

            $request->validate([
                'name' => 'required|min:2|max:50|unique:permissions,name,'.$id,
                'route' => 'required|min:2|max:100',
                'parent' => 'nullable|integer',
                'icon' => 'nullable|string|max:50',
            ]);

            $appMenu->update($request->only(['name', 'route', 'parent', 'icon']));
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'data' => $appMenu,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Permission not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed updating Permission',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $appMenu = Permission::findOrFail($id);
            $appMenu->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Permission not found',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed deleting Permission',
            ], 500);
        }
    }
}
