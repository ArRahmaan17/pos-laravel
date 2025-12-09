<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\CustomerRole;
use App\Models\MasterTask;
use App\Models\Task;
use App\Models\TaskDetail;
use App\Models\UserCustomerRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CustomerTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer_roles = CustomerRole::where('user_id', session('userLogged')['company']['user_id'])->get();
        $employees = UserCustomerRole::join('customer_roles as cr', 'cr.id', '=', 'user_customer_roles.role_id')
            ->join('companies as cc', 'user_customer_roles.company_id', '=', 'cc.id')
            ->join('users as u', 'user_customer_roles.user_id', '=', 'u.id')
            ->select('u.name', 'u.phone_number', 'cr.name as role_name', 'cr.id as role_id', 'u.username', 'u.id')
            ->where('cc.id', session('userLogged')['company']['id'])
            ->orderBy('id', 'asc')->get();

        return view('man.customer-tasks', compact('customer_roles', 'employees'));
    }

    public function dataTable(Request $request)
    {
        $where = [
            ['tasks.company_id', '=', session('userLogged')['company']['id']],
        ];
        if (! in_array(session('userLogged')['role']['scope']['code'], ['Developer', 'Manager'])) {
            $where[] = ['tasks.user_id',  '=', session('userLogged')['user']['id']];
        }
        $totalData = Task::join('users', 'users.id', '=', 'tasks.user_id')->with('user', 'details', 'details.master')->select('tasks.*', 'users.name as user_name')
            ->where($where)->orderBy('tasks.id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = Task::join('users', 'users.id', '=', 'tasks.user_id')->with('user', 'details', 'details.master')->select('tasks.*', 'users.name as user_name');
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $assets = $assets->where($where)->get();
        } else {
            $assets = Task::join('users', 'users.id', '=', 'tasks.user_id')->with('user', 'details', 'details.master')->select('tasks.*', 'users.name as user_name')
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

            $totalFiltered = Task::join('users', 'users.id', '=', 'tasks.user_id')->with('user', 'details', 'details.master')->select('tasks.*', 'users.name as user_name')
                ->where('name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('description', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where($where)->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $item = collect($item)->toArray();
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['name'] = $item['name'];
            $row['details'] = $item['details'];
            $row['user_name'] = $item['user']['name'];
            $row['activity'] = (($item['start_at'] && $item['end_at']) ? now()->createFromTimeString($item['start_at'])->diffForHumans(now()->createFromTimeString($item['end_at']), true) : (($item['start_at'] && $item['end_at'] === null) ? 'Started '.now()->createFromTimeString($item['start_at'])->diffForHumans(now()) : 'Not Started Yet'));
            $row['percentage'] = $item['percentage'].' %';
            $row['time_limit'] = now()->createFromTimeString(now()->format('Y-m-d H:i:s'))->diffInDays($item['time_limit'], false).' days left';
            $row['action'] = ((! $item['start_at']) ? "<button class='btn btn-outline-success btn-icon start' data-customer-task-status='new' data-customer-task-management='".$item['id']."'><i class='bx bx-play'></i></button>" : '').((! $item['end_at']) ? "<button class='btn btn-icon btn-outline-warning edit' data-customer-task-management='".$item['id']."' ><i class='bx bx-pencil' ></i></button><button data-customer-task-management='".$item['id']."' class='btn btn-icon btn-outline-danger delete'><i class='bx bxs-trash-alt' ></i></button>" : '');
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
            'role_id' => 'exists:customer_roles,id',
            'user_id' => 'exists:users,id',
            'time_limit' => 'required|date',
            'details.*.type' => 'required|in:new,unfinish',
            'details.*.masterId' => [
                'required_if:details.*.type,new',
                Rule::exists('master_tasks', 'id')->where('company_id', session('userLogged')['company']['id']),
            ],
            'details.*.id' => [
                'required_if:details.*.type,unfinish',
                Rule::exists('task_items', 'id')->where('user_id', $request->user_id),
            ],
        ]);
        $dataTask = [
            'user_id' => $request->user_id ?? session('userLogged')['user']['id'],
            'company_id' => session('userLogged')['company']['id'],
            'time_limit' => $request->time_limit,
            'name' => $request->name,
        ];
        $dataTaskDetails = $request->details;
        DB::beginTransaction();
        try {
            $result = Task::create($dataTask);
            $dataTaskDetails = array_map(function ($detail) use ($result) {
                $detail['task_id'] = $result->id;
                $detail['created_at'] = now();
                $detail['updated_at'] = now();
                unset($detail['type']);

                return $detail;
            }, $dataTaskDetails);
            TaskDetail::insert($dataTaskDetails);
            $status = 200;
            $message = ['message' => 'resources created successfully'];
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = 422;
            $message = ['message' => 'failed created resources'];
        }

        return response()->json($message, $status);
    }

    public function getEvidence($id)
    {
        $detail = TaskDetail::find($id);
        $detail->evidence;

        return Storage::disk('company-task-evidence')->get(md5($detail->task_id));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $where = [
            'user_id' => session('userLogged')['user']['id'],
            'company_id' => session('userLogged')['company']['id'],
            'id' => $id,
            ['time_limit', '>=', now()->format('Y-m-d')],
        ];
        if (in_array(session('userLogged')['role']['scope']['code'], ['Developer', 'Manager'])) {
            unset($where['user_id']);
        }
        $status = 404;
        $message = ['message' => 'showing resources successfully', 'data' => null];
        $builder = Task::with('details', 'details.master', 'user')->where($where);
        if ($builder->exists()) {
            $status = 200;
            $message = ['message' => 'showing resources successfully', 'data' => $builder->first()];
        }

        return response()->json($message, $status);
    }

    public function startTask(string $id, $type = 'new')
    {
        $where = [
            'user_id' => session('userLogged')['user']['id'],
            'company_id' => session('userLogged')['company']['id'],
            'id' => $id,
            'start_at' => null,
            'end_at' => null,
            ['time_limit', '>=', now()->format('Y-m-d')],
        ];
        if (in_array(session('userLogged')['role']['scope']['code'], ['Developer', 'Manager'])) {
            unset($where['user_id']);
        }
        DB::beginTransaction();
        try {
            if ($type === 'new') {
                Task::where($where)->update(['start_at' => now()]);
            }
            unset($where['user_id'], $where['company_id'], $where[0]);
            TaskDetail::where($where)->update(['start_at' => now()]);
            DB::commit();
            $status = 200;
            $message = ['message' => 'task started successfully'];
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = 422;
            $message = ['message' => 'failed staring task'];
        }

        return response()->json($message, $status);
    }

    public function unfinishTask()
    {
        $dataTask = TaskDetail::join('master_tasks', 'master_tasks.id', '=', 'task_items.masterId')->whereNot('status', 1)->where('master_tasks.repeateable', 1)->get()->toArray();
        $status = 200;
        $message = ['message' => 'unfinish tasks found', 'data' => $dataTask];
        if (empty($dataTask)) {
            $status = 404;
            $message = ['message' => 'unfinish tasks not found', 'data' => $dataTask];
        }

        return response()->json($message, $status);
    }

    public function newTask(Request $request)
    {
        $dataTask = MasterTask::where([
            'company_id' => session('userLogged')['company']['id'],
            'role_id' => $request->role ?? session('userLogged')['role']['id'],
        ])->orderBy('id')->get()->toArray();
        $status = 200;
        $message = ['message' => 'tasks found', 'data' => $dataTask];
        if (empty($dataTask)) {
            $status = 404;
            $message = ['message' => 'tasks not found, please contact manager to create new task for '.session('userLogged')['role']['scope']['code'], 'data' => $dataTask];
        }

        return response()->json($message, $status);
    }

    public function finishTask(Request $request, $id, $type)
    {
        $request->validate(['filepond' => 'required|image']);
        $filename = 'task-'.$id.'-'.$type.'-'.date('Y-m-d-His').rand(1, 100).'.'.$request->file('filepond')->getClientOriginalExtension();
        Storage::disk('company-task-evidence')->putFileAs(md5(session('userLogged')['company']['id']).'/'.md5($id), $request->file('filepond'), $filename);
        DB::beginTransaction();
        try {
            $data = TaskDetail::find($id);
            $evidence = json_decode($data->evidence ?? '[]');
            $evidence = (! empty($evidence)) ? array_merge($evidence, [$filename]) : [$filename];
            TaskDetail::where('id', $id)->update([
                'evidence' => json_encode($evidence),
                'status' => 1,
                'end_at' => now(),
            ]);
            $builder = TaskDetail::where(['task_id' => $data->task_id]);
            [$countDetail, $countFinish] = [$builder->count(), $builder->where('status', 1)->count()];
            Task::find($data->task_id)->update(['percentage' => (($countFinish / $countDetail) * 100), 'end_at' => ($countDetail === $countFinish) ? now() : null]);
            DB::commit();
            $status = 200;
            $message = ['message' => 'update resources successfully'];
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = 422;
            $message = ['message' => 'failed updating resources'];
        }

        return response()->json($message, $status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|min:4|max:30',
            'user_id' => 'exists:users,id',
            'time_limit' => 'required|date',
            'details.*.type' => 'required|in:new,unfinish,finish',
            'details.*.masterId' => [
                'required_if:details.*.type,new',
                Rule::exists('master_tasks', 'id')->where('company_id', session('userLogged')['company']['id']),
            ],
            'details.*.id' => [
                'required_if:details.*.type,unfinish',
                Rule::exists('task_items', 'id'),
            ],
        ]);
        $dataTask = [
            'user_id' => $request->user_id ?? session('userLogged')['user']['id'],
            'company_id' => session('userLogged')['company']['id'],
            'time_limit' => $request->time_limit,
            'name' => $request->name,
        ];
        $dataTaskDetails = $request->details;
        DB::beginTransaction();
        try {
            Task::find($id)->update($dataTask);
            $dataTaskDetails = array_map(function ($detail) use ($id) {
                $detail['task_id'] = $id;
                ($detail['type'] === 'new') ? $detail['masterId'] = $detail['masterId'] : $detail['id'] = $detail['id'];
                $detail['created_at'] = now();
                $detail['updated_at'] = now();
                unset($detail['type']);

                return $detail;
            }, $dataTaskDetails);
            $dataInsert = array_values(array_filter($dataTaskDetails, function ($task) {
                return isset($task['masterId']);
            }));
            TaskDetail::insert($dataInsert);
            $builder = TaskDetail::where(['task_id' => $id]);
            [$countDetail, $countFinish] = [$builder->count(), $builder->where('status', 1)->count()];
            Task::find($id)->update(['percentage' => (($countFinish / $countDetail) * 100)]);
            $status = 200;
            $message = ['message' => 'resources updated successfully'];
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = 422;
            $message = ['message' => 'failed updated resources'];
        }

        return response()->json($message, $status);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            if (! Task::where(['id' => $id, 'start_at' => null])->delete()) {
                throw new Exception('failed destroy resources');
            }
            $status = 200;
            $message = ['message' => 'resources destroy successfully'];
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = 422;
            $message = ['message' => 'failed destroy resources'];
        }

        return response()->json($message, $status);
    }

    public function destroyDetail(string $id)
    {
        DB::beginTransaction();
        try {
            $taskDetailData = TaskDetail::where(['id' => $id])->first();
            TaskDetail::where(['id' => $id, 'start_at' => null])->delete();
            $builder = TaskDetail::where(['task_id' => $taskDetailData->task_id]);
            [$countDetail, $countFinish] = [$builder->count(), $builder->where('status', 1)->count()];
            Task::find($taskDetailData->task_id)->update(['percentage' => (($countFinish / $countDetail) * 100)]);
            Storage::disk('company-task-evidence')->deleteDirectory(md5(session('userLogged')['company']['id']).'/'.md5($id));
            $status = 200;
            $message = ['message' => 'detail resources destroy successfully'];
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = 422;
            $message = ['message' => 'failed destroy detail resources'];
        }

        return response()->json($message, $status);
    }
}
