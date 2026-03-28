<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('settings.role');
    }
    public function role($id = null)
    {
        $companyIdScope = $id ?? session('userLogged')['company']['id'];
        
        $data = Role::where(function($q) use ($companyIdScope) {
                // Ensure they can only see roles for the targeted company OR their own if none provided
                $q->where('company_id', $companyIdScope)->orWhereNull('company_id');
            })->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Failed showing resource', 
                'data' => dataToOption($data)
            ], 404);
        }

        return response()->json([
            'message' => 'Showing resource successfully', 
            'data' => dataToOption($data)
        ], 200);
    }

    public function dataTable(Request $request)
    {
        $companyId = session('userLogged')['company']['id'] ?? null;
        
        $query = Role::query()
            ->where(function($q) use ($companyId) {
                $q->whereNull('company_id')->orWhere('company_id', $companyId);
            });

        $totalData = $query->count();
        $totalFiltered = $totalData;

        if (!empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('description', 'like', "%{$searchValue}%");
            });
            $totalFiltered = $query->count();
        }

        // Ordering Logic
        if ($request->has('order.0.column') && $request->has('order.0.dir')) {
            $columnIdx = $request->input('order.0.column');
            $columnName = $request->input("columns.{$columnIdx}.name") ?: 'id';
            $query->orderBy($columnName, $request->input('order.0.dir'));
        } else {
            $query->orderBy('id', 'asc');
        }

        // Pagination
        if ($request->input('length') != -1) {
            $query->limit($request->input('length'))->offset($request->input('start'));
        }

        $assets = $query->get();

        $dataFiltered = $assets->map(function ($item, $index) use ($request) {
            return [
                'order_number' => $request->input('start') + ($index + 1),
                'name'         => $item->name,
                'description'  => $item->description,
                'company'      => $item->company->name ?? 'System',
                'action'       => $item->company_id === null 
                    ? "<button class='btn btn-icon btn-outline-success copy' data-role='{$item->id}'><i class='bx bx-copy'></i></button>"
                    : "<button class='btn btn-icon btn-outline-warning edit' data-role='{$item->id}'><i class='bx bx-pencil'></i></button>" .
                      "<button data-role='{$item->id}' class='btn btn-icon btn-outline-danger delete'><i class='bx bxs-trash-alt'></i></button>",
            ];
        });

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsFiltered' => $totalFiltered,
            'recordsTotal'    => $totalData,
            'aaData'          => $dataFiltered,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:2|max:50|unique:roles,name',
            'description' => 'required|min:6|max:100',
        ]);

        return DB::transaction(function () use ($validated) {
            try {
                Role::create(array_merge($validated, [
                    'company_id' => session('userLogged')['company']['id'] ?? null,
                ]));

                return response()->json(['message' => 'App Role created successfully'], 201);
            } catch (\Throwable $th) {
                return response()->json(['message' => 'Failed creating App Role: '.$th->getMessage()], 422);
            }
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $companyId = session('userLogged')['company']['id'] ?? null;
        $data = Role::where(function($q) use ($companyId) {
                $q->whereNull('company_id')->orWhere('company_id', $companyId);
            })->find($id);

        if (!$data) {
            return response()->json(['message' => 'Failed showing resource: Not Found or Unauthorized'], 404);
        }

        return response()->json(['message' => 'Showing resource successfully', 'data' => $data], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $companyId = session('userLogged')['company']['id'] ?? null;
        $role = Role::where('company_id', $companyId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|max:50|unique:roles,name,'.$id,
            'description' => 'required|min:6|max:100',
        ]);

        return DB::transaction(function () use ($role, $validated) {
            try {
                $role->update($validated);
                return response()->json(['message' => 'Updating resource successfully'], 200);
            } catch (\Throwable $th) {
                return response()->json(['message' => 'Failed updating resource'], 422);
            }
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $companyId = session('userLogged')['company']['id'] ?? null;
        $role = Role::where('company_id', $companyId)->findOrFail($id);

        return DB::transaction(function () use ($role) {
            try {
                if ($role->role_users()->exists()) {
                    return response()->json([
                        'message' => "Failed deleting resource. This role is assigned to users and cannot be deleted."
                    ], 422);
                }

                $role->delete();
                return response()->json(['message' => 'Deleting resource successfully'], 200);
            } catch (\Throwable $th) {
                return response()->json(['message' => 'Failed deleting resource'], 422);
            }
        });
    }
}
