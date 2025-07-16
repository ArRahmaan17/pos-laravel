<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompanyMasterTask;
use App\Models\CustomerCompanyTask;
use App\Models\CustomerCompanyTaskDetail;
use App\Models\CustomerRole;
use App\Models\UserCustomerRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer_roles = CustomerRole::where('userId', session('userLogged')['user']['id'])->get();
        $employees =  UserCustomerRole::join('customer_roles as cr', 'cr.id', '=', 'user_customer_roles.roleId')
            ->join('customer_companies as cc', 'user_customer_roles.companyId', '=', 'cc.id')
            ->join('users as u', 'user_customer_roles.userId', '=', 'u.id')
            ->select('u.name', 'u.phone_number', 'cr.name as role_name', 'cr.id as roleId', 'u.username', 'u.id')
            ->where('cc.id', session('userLogged')['company']['id'])
            ->orderBy('id', 'asc')->get();
        return view('man.customer-tasks', compact('customer_roles', 'employees'));
    }

    public function dataTable(Request $request)
    {
        $where = [
            ['customer_company_tasks.companyId', '=', session('userLogged')['company']['id']]
        ];
        if (!in_array(session('userLogged')['role']['name'], ['Developer', 'Manager'])) {
            $where[] = ['customer_company_tasks.userId',  '=', session('userLogged')['user']['id']];
        }
        $totalData = CustomerCompanyTask::with('user', 'details', 'master')
            ->where($where)->orderBy('customer_company_tasks.id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerCompanyTask::with('user', 'details', 'master')->select('*');
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->where($where)->get();
        } else {
            $assets = CustomerCompanyTask::with('user', 'details', 'master')->select('*')
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

            $totalFiltered = CustomerCompanyTask::with('user', 'details', 'master')->select('*')
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
            $row['activity'] = ($item->start_at && $item->end_at) ? now()->createFromTimeString($item->start_at)->diffInHours(now()->createFromTimeString($item->end_at), true) : 'Not Started Yet';
            $row['percentage'] = $item->percentage . ' %';
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-customer-task-management='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-customer-task-management='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
            'name' => 'required|min:4|max:30',
            'roleId' => 'exists:customer_roles,id',
            'userId' => 'exists:users,id',
            'details.*.type' => 'required|in:new,unfinish',
            'details.*.masterId' => [
                'required_if:details.*.type,new',
                Rule::exists('customer_company_master_tasks', 'id')->where('companyId', session('userLogged')['company']['id']),
            ],
            'details.*.id' => [
                'required_if:details.*.type,unfinish',
                Rule::exists('customer_company_task_details', 'id')->where('userId', $request->roleId),
            ],
        ]);
        $dataTask = [
            'userId' => $request->userId ?? session('userLogged')['user']['id'],
            'companyId' => session('userLogged')['company']['id'],
            'name' => $request->name,
        ];
        $dataTaskDetails = $request->details;
        DB::beginTransaction();
        try {
            $result = CustomerCompanyTask::create($dataTask);
            $dataTaskDetails = array_map(function ($detail) use ($result) {
                $detail['taskId'] = $result->id;
                $detail['created_at'] = now();
                $detail['updated_at'] = now();
                unset($detail['type']);
                return $detail;
            }, $dataTaskDetails);
            CustomerCompanyTaskDetail::insert($dataTaskDetails);
            $status = 200;
            $message = ['message' => 'resources created successfully'];
            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            $status = 422;
            $message = ['message' => 'failed created resources'];
        }
        return response()->json($message, $status);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function unfinishTask()
    {
        $dataTask = CustomerCompanyTaskDetail::join('customer_company_master_tasks', 'customer_company_master_tasks.id', '=', 'customer_company_task_details.masterId')->whereNot('status', 1)->get()->toArray();
        $status = 200;
        $message = ['message' => "unfinish tasks found", 'data' => $dataTask];
        if (empty($dataTask)) {
            $status = 404;
            $message = ['message' => "unfinish tasks not found", 'data' => $dataTask];
        }
        return response()->json($message, $status);
    }

    public function newTask(Request $request)
    {
        $dataTask = CustomerCompanyMasterTask::where([
            'companyId' => session('userLogged')['company']['id'],
            'roleId' => $request->role ?? session('userLogged')['role']['id'],
        ])->orderBy('id')->get()->toArray();
        $status = 200;
        $message = ['message' => "tasks found", 'data' => $dataTask];
        if (empty($dataTask)) {
            $status = 404;
            $message = ['message' => "tasks not found, please contact manager to create new task for session('userLogged')['role']['name']", 'data' => $dataTask];
        }
        return response()->json($message, $status);
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
