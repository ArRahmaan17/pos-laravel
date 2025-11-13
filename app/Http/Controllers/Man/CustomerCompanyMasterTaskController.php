<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompanyMasterTask;
use App\Models\CustomerCompanyTaskDetail;
use App\Models\CustomerRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerCompanyMasterTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer_roles = CustomerRole::where('user_id', session('userLogged')['user']['id'])->get();

        return view('man.customer-master-tasks', compact('customer_roles'));
    }

    public function dataTable(Request $request)
    {
        $where = [['company_id', '=', session('userLogged')['company']['id']]];
        $totalData = CustomerCompanyMasterTask::with('role')->where($where)->orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerCompanyMasterTask::with('role')->select('*');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $assets = $assets->where($where)->get();
        } else {
            $assets = CustomerCompanyMasterTask::with('role')->select('*')
                ->where('name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('description', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where($where)->get();

            $totalFiltered = CustomerCompanyMasterTask::with('role')->select('*')
                ->where('name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('description', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where($where)->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['name'] = $item->name;
            $row['description'] = $item->description;
            $row['role'] = $item->role->name;
            $row['priority'] = $item->priority;
            $row['repeateable'] = $item->repeateable == 0 ? 'No' : 'Yes';
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-customer-master-tasks='".$item->id."' ><i class='bx bx-pencil' ></i></button><button data-customer-master-tasks='".$item->id."' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
            'name' => ['required', 'min:4', 'max:30', Rule::unique('master_tasks', 'name')->where('company_id', session('userLogged')['company']['id'])],
            'description' => 'required|min:4',
            'role_id' => 'required|exists:customer_roles,id',
            'priority' => 'required|in:P1,P2,P3,P4',
            'repeateable' => 'required|in:1,0',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token');
            $data['company_id'] = session('userLogged')['company']['id'];
            CustomerCompanyMasterTask::create($data);
            DB::commit();
            $status = 200;
            $message = ['message' => 'resources created successfully'];
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = 422;
            $message = ['message' => 'failed creating resources'];
        }

        return response()->json($message, $status);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = CustomerCompanyMasterTask::where('company_id', session('userLogged')['company']['id'])->find($id);
        $status = 200;
        $message = ['message' => 'showing resources successfully', 'data' => $data];
        if (! $data) {
            $status = 404;
            $message = ['message' => 'failed showing resources', 'data' => $data];
        }

        return response()->json($message, $status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'min:4', 'max:30', Rule::unique('master_tasks', 'name')->where('company_id', session('userLogged')['company']['id'])->whereNot('id', $id)],
            'description' => 'required|min:4',
            'role_id' => 'required|exists:customer_roles,id',
            'priority' => 'required|in:P1,P2,P3,P4',
            'repeateable' => 'required|in:1,0',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token');
            $data['company_id'] = session('userLogged')['company']['id'];
            CustomerCompanyMasterTask::find($id)->update($data);
            DB::commit();
            $status = 200;
            $message = ['message' => 'resources updated successfully'];
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = 422;
            $message = ['message' => 'failed updating resources'];
        }

        return response()->json($message, $status);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $status = 422;
        $message = ['message' => 'failed deleting resources'];
        if (! CustomerCompanyTaskDetail::where('masterId', $id)->exists()) {
            $status = 200;
            $message = ['message' => 'resources deleted successfully'];
            DB::beginTransaction();
            try {
                CustomerCompanyMasterTask::find($id)->delete();
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
            }
        }

        return response()->json($message, $status);
    }
}
