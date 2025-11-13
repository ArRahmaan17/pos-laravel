<?php

namespace App\Http\Controllers\Api\Dev;

use App\Helpers\RedisHelper;
use App\Http\Controllers\Controller;
use App\Models\AppGoodUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppGoodUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            if (RedisHelper::exists('product_weight_units')) {
                $appGoodUnits = json_decode(RedisHelper::get('product_weight_units'));
            } else {
                $appGoodUnits = AppGoodUnit::orderBy('id', 'asc')->get();
                RedisHelper::set('product_weight_units', json_encode($appGoodUnits));
            }

            return response()->json([
                'message' => 'showing resource successfully',
                'data' => $appGoodUnits,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'failed showing resource',
                'data' => null,
            ], 500);
        }
    }

    public function dataTable(Request $request)
    {
        try {
            $totalData = AppGoodUnit::orderBy('id', 'asc')->count();
            $totalFiltered = $totalData;

            if (empty($request['search']['value'])) {
                $assets = AppGoodUnit::select('*');

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
                $assets = AppGoodUnit::select('*')
                    ->where('name', 'like', '%'.$request['search']['value'].'%')
                    ->orWhere('description', 'like', '%'.$request['search']['value'].'%');

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

                $totalFiltered = AppGoodUnit::select('*')
                    ->where('name', 'like', '%'.$request['search']['value'].'%')
                    ->orWhere('description', 'like', '%'.$request['search']['value'].'%');

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
                'name' => 'required|min:2|max:50|unique:product_weight_units,name',
                'description' => 'required|min:6|max:100',
            ]);

            $appGoodUnit = AppGoodUnit::create($request->only(['name', 'description']));
            DB::commit();
            RedisHelper::del('product_weight_units');

            return response()->json([
                'success' => true,
                'message' => 'App Good Unit created successfully',
                'data' => $appGoodUnit,
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
                'message' => 'Failed creating App Good Unit',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $appGoodUnit = AppGoodUnit::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $appGoodUnit,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'App Good Unit not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve App Good Unit',
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
            $appGoodUnit = AppGoodUnit::findOrFail($id);
            $request->validate([
                'name' => 'required|min:2|max:50|unique:product_weight_units,name,'.$id,
                'description' => 'required|min:6|max:100',
            ]);

            $appGoodUnit->update($request->only(['name', 'description']));
            RedisHelper::del('product_weight_units');
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'App Good Unit updated successfully',
                'data' => $appGoodUnit,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'App Good Unit not found',
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
                'message' => 'Failed updating App Good Unit',
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
            $appGoodUnit = AppGoodUnit::findOrFail($id);
            $appGoodUnit->delete();
            RedisHelper::del('product_weight_units');
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'App Good Unit deleted successfully',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'App Good Unit not found',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed deleting App Good Unit',
            ], 500);
        }
    }
}
