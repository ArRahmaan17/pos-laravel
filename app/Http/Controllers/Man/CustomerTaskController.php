<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompanyMasterTask;
use App\Models\CustomerCompanyTask;
use App\Models\CustomerRole;
use Illuminate\Http\Request;

class CustomerTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer_roles = CustomerRole::where('userId', session('userLogged')['user']['id'])->get();
        return view('man.customer-tasks', compact('customer_roles'));
    }

    public function dataTable(Request $request)
    {
        $where = [['customer_company_tasks.userId', '=', session('userLogged')['user']['id']]];
        $totalData = CustomerCompanyTask::with('user', 'details')
            ->leftJoin('users', 'users.id', '=', 'customer_company_tasks.userId')
            ->leftJoin('user_customer_roles', 'user_customer_roles.userId', '=', 'users.id')
            ->leftJoin('customer_roles', 'customer_roles.id', '=', 'user_customer_roles.userId')
            ->leftJoin('customer_company_task_details', 'customer_company_task_details.taskId', '=', 'customer_company_tasks.id')
            ->leftJoin('customer_company_master_tasks', 'customer_company_master_tasks.id', '=', 'customer_company_task_details.taskId')
            ->where($where)->orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerCompanyTask::with('user', 'details')->select('*')
                ->leftJoin('users', 'users.id', '=', 'customer_company_tasks.userId')
                ->leftJoin('user_customer_roles', 'user_customer_roles.userId', '=', 'users.id')
                ->leftJoin('customer_roles', 'customer_roles.id', '=', 'user_customer_roles.userId')
                ->leftJoin('customer_company_task_details', 'customer_company_task_details.taskId', '=', 'customer_company_tasks.id')
                ->leftJoin('customer_company_master_tasks', 'customer_company_master_tasks.id', '=', 'customer_company_task_details.taskId');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->where($where)->get();
        } else {
            $assets = CustomerCompanyTask::with('user', 'details')->select('*')
                ->leftJoin('users', 'users.id', '=', 'customer_company_tasks.userId')
                ->leftJoin('user_customer_roles', 'user_customer_roles.userId', '=', 'users.id')
                ->leftJoin('customer_roles', 'customer_roles.id', '=', 'user_customer_roles.userId')
                ->leftJoin('customer_company_task_details', 'customer_company_task_details.taskId', '=', 'customer_company_tasks.id')
                ->leftJoin('customer_company_master_tasks', 'customer_company_master_tasks.id', '=', 'customer_company_task_details.taskId')
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

            $totalFiltered = CustomerCompanyTask::with('user', 'details')->select('*')
                ->leftJoin('users', 'users.id', '=', 'customer_company_tasks.userId')
                ->leftJoin('user_customer_roles', 'user_customer_roles.userId', '=', 'users.id')
                ->leftJoin('customer_roles', 'customer_roles.id', '=', 'user_customer_roles.userId')
                ->leftJoin('customer_company_task_details', 'customer_company_task_details.taskId', '=', 'customer_company_tasks.id')
                ->leftJoin('customer_company_master_tasks', 'customer_company_master_tasks.id', '=', 'customer_company_task_details.taskId')
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
            $row['role'] = $item->name;
            $row['username'] = $item->name;
            $row['activity'] = $item->name;
            $row['percentage'] = $item->name;
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function unfinishTask(string $id)
    {
        //
    }

    public function newTask(Request $request)
    {
        $dataTask = CustomerCompanyMasterTask::where([
            'companyId' => session('userLogged')['company']['id'],
            'roleId' => $request->role ?? session('userLogged')['role']['id'],
        ])->get();
        $status = 200;
        $message = ['message' => "tasks found", 'data' => $dataTask];
        if (!$dataTask) {
            $status = 404;
            $message = ['message' => "tasks not found, please contact manager to create new task for session('userLogged')['role']['name']", 'data' => $dataTask];
        }
        return response()->json($message, $status);
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
