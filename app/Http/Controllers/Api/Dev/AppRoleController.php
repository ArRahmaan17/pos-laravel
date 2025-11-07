<?php

namespace App\Http\Controllers\Api\Dev;

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
        try {
            $appRoles = AppRole::orderBy('id', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => $appRoles
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve app roles'
            ], 500);
        }
    }

    public function dataTable(Request $request)
    {
        try {
            $totalData = AppRole::orderBy('id', 'asc')->count();
            $totalFiltered = $totalData;

            if (empty($request['search']['value'])) {
                $assets = AppRole::select('*');

                if ($request['length'] != '-1') {
                    $assets->limit($request['length']);
                    if ($request['start'] > 0) {
                        $assets->where('id', '>', $request['start']);
                    }
                }
                if (isset($request['order'][0]['column'])) {
                    $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
                }
                $assets = $assets->get();
            } else {
                $assets = AppRole::select('*')
                    ->where('name', 'like', '%' . $request['search']['value'] . '%')
                    ->orWhere('description', 'like', '%' . $request['search']['value'] . '%');

                if (isset($request['order'][0]['column'])) {
                    $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
                }
                if ($request['length'] != '-1') {
                    $assets->limit($request['length']);
                    if ($request['start'] > 0) {
                        $assets->where('id', '>', $request['start']);
                    }
                }
                $assets = $assets->get();

                $totalFiltered = AppRole::select('*')
                    ->where('name', 'like', '%' . $request['search']['value'] . '%')
                    ->orWhere('description', 'like', '%' . $request['search']['value'] . '%');

                if (isset($request['order'][0]['column'])) {
                    $totalFiltered->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
                }
                $totalFiltered = $totalFiltered->count();
            }

            $dataFiltered = [];
            foreach ($assets as $index => $item) {
                $row = [];
                $row['order_number'] = $request['start'] + ($index + 1);
                $row['id'] = $item->id;
                $row['name'] = $item->name;
                $row['description'] = $item->description;
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
                'message' => 'Failed to retrieve data table'
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
                'name' => 'required|min:2|max:10|unique:app_roles,name',
                'description' => 'required|min:6|max:100',
            ]);

            $appRole = AppRole::create($request->only(['name', 'description']));
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'App Role created successfully',
                'data' => $appRole
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed creating App Role'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $appRole = AppRole::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $appRole
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'App Role not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve App Role'
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
            $appRole = AppRole::findOrFail($id);

            $request->validate([
                'name' => 'required|min:2|max:10|unique:app_roles,name,' . $id,
                'description' => 'required|min:6|max:100',
            ]);

            $appRole->update($request->only(['name', 'description']));
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'App Role updated successfully',
                'data' => $appRole
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'App Role not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed updating App Role'
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
            $appRole = AppRole::findOrFail($id);
            $appRole->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'App Role deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'App Role not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed deleting App Role'
            ], 500);
        }
    }
}
