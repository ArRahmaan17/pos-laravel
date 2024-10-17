<?php

namespace App\Http\Controllers\Dev;

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
        return view('dev.app-subscription');
    }

    public function dataTable(Request $request)
    {
        $totalData = AppSubscription::with('planFeature')->orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = AppSubscription::with('planFeature')->select('*');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
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
                $assets->limit($request['length'])
                    ->offset($request['start']);
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
            $row['name'] = $item->name;
            $row['description'] = $item->description;
            $row['price'] = $item->price;
            $row['plans'] = $item->plans;
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-app-subscription='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-app-subscription='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
            'name' => 'required|min:6|max:40',
            'description' => 'required|min:6|max:40',
            'price' => 'required|numeric|max_digits:16',
            'planFeature' => 'required|array',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token', 'id', 'planFeature');
            $subscription = AppSubscription::create($data);
            $subs_feature = array_map(function ($data) use ($subscription) {
                return ['subscriptionId' => $subscription->id, 'planFeature' => $data, 'created_at' => now('Asia/Jakarta'), 'updated_at' => now('Asia/Jakarta')];
            }, $request->planFeature);
            AppDetailSubscription::insert($subs_feature);
            $response = ['message' => 'creating resource successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed creating resource'];
            $code = 422;
        }

        return response()->json($response, $code);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = AppSubscription::with('planFeature')->find($id);
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
            'name' => 'required|min:6|max:40',
            'description' => 'required|min:6|max:40',
            'price' => 'required|numeric|max_digits:16',
            'planFeature' => 'required|array',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token', 'id', 'planFeature');
            AppSubscription::where('id', $id)->update($data);
            $subs_feature = array_map(function ($data) use ($id) {
                return ['subscriptionId' => $id, 'planFeature' => $data, 'created_at' => now('Asia/Jakarta'), 'updated_at' => now('Asia/Jakarta')];
            }, $request->planFeature);
            AppDetailSubscription::where('subscriptionId', $id)->delete();
            AppDetailSubscription::insert($subs_feature);
            $response = ['message' => 'updating resource successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed updating resource'];
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
            AppSubscription::where('id', $id)->delete();
            AppDetailSubscription::where('subscriptionId', $id)->delete();
            $response = ['message' => 'deleting resource successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => 'failed deleting resource'];
            $code = 422;
        }

        return response()->json($response, $code);
    }
}
