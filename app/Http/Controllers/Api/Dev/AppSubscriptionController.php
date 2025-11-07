<?php

namespace App\Http\Controllers\Api\Dev;

use App\Http\Controllers\Controller;
use App\Models\AppDetailSubscription;
use App\Models\AppSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $appSubscriptions = AppSubscription::with('planFeature')->orderBy('id', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => $appSubscriptions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve app subscriptions'
            ], 500);
        }
    }

    public function dataTable(Request $request)
    {
        try {
            $totalData = AppSubscription::with('planFeature')->orderBy('id', 'asc')->count();
            $totalFiltered = $totalData;

            if (empty($request['search']['value'])) {
                $assets = AppSubscription::with('planFeature')->select('*');

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
                $assets = AppSubscription::with('planFeature')->select('*')
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

                $totalFiltered = AppSubscription::with('planFeature')->select('*')
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
                $row['price'] = $item->price;
                $row['duration_days'] = $item->duration_days;
                $row['plan_feature'] = $item->planFeature;
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
                'name' => 'required|min:2|max:50|unique:app_subscriptions,name',
                'description' => 'required|min:6|max:500',
                'price' => 'required|numeric|min:0',
                'duration_days' => 'required|integer|min:1',
                'plan_features' => 'nullable|array',
            ]);

            $appSubscription = AppSubscription::create($request->only(['name', 'description', 'price', 'duration_days']));

            // Handle plan features if provided
            if ($request->has('plan_features') && is_array($request->plan_features)) {
                foreach ($request->plan_features as $feature) {
                    AppDetailSubscription::create([
                        'subscription_id' => $appSubscription->id,
                        'feature_name' => $feature['name'] ?? '',
                        'feature_description' => $feature['description'] ?? '',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'App Subscription created successfully',
                'data' => $appSubscription->load('planFeature')
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
                'message' => 'Failed creating App Subscription'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $appSubscription = AppSubscription::with('planFeature')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $appSubscription
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'App Subscription not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve App Subscription'
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
            $appSubscription = AppSubscription::findOrFail($id);

            $request->validate([
                'name' => 'required|min:2|max:50|unique:app_subscriptions,name,' . $id,
                'description' => 'required|min:6|max:500',
                'price' => 'required|numeric|min:0',
                'duration_days' => 'required|integer|min:1',
                'plan_features' => 'nullable|array',
            ]);

            $appSubscription->update($request->only(['name', 'description', 'price', 'duration_days']));

            // Handle plan features if provided
            if ($request->has('plan_features')) {
                // Delete existing features
                AppDetailSubscription::where('subscription_id', $appSubscription->id)->delete();

                // Add new features
                if (is_array($request->plan_features)) {
                    foreach ($request->plan_features as $feature) {
                        AppDetailSubscription::create([
                            'subscription_id' => $appSubscription->id,
                            'feature_name' => $feature['name'] ?? '',
                            'feature_description' => $feature['description'] ?? '',
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'App Subscription updated successfully',
                'data' => $appSubscription->load('planFeature')
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'App Subscription not found'
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
                'message' => 'Failed updating App Subscription'
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
            $appSubscription = AppSubscription::findOrFail($id);

            // Delete related plan features
            AppDetailSubscription::where('subscription_id', $appSubscription->id)->delete();

            $appSubscription->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'App Subscription deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'App Subscription not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed deleting App Subscription'
            ], 500);
        }
    }
}
