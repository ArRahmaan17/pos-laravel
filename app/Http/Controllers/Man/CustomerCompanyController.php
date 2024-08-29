<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use App\Models\CompanyAddress;
use App\Models\CustomerCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::user_manager();
        $types = BusinessType::all();
        return view('man.customer-company', compact('users', 'types'));
    }

    public function dataTable(Request $request)
    {
        $totalData = CustomerCompany::join('users as uc', 'customer_companies.userId', '=', 'uc.id')
            ->orderBy('customer_companies.id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerCompany::with('address', 'type')->join('users as uc', 'customer_companies.userId', '=', 'uc.id')
                ->select('customer_companies.name', 'customer_companies.phone_number', 'customer_companies.id', 'customer_companies.businessId');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->get();
        } else {
            $assets = CustomerCompany::with('address', 'type')->join('users as uc', 'customer_companies.userId', '=', 'uc.id')
                ->select('customer_companies.name', 'customer_companies.phone_number', 'customer_companies.id', 'customer_companies.businessId')
                ->where('customer_companies.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('customer_companies.phone_number', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->get();

            $totalFiltered = CustomerCompany::join('users as uc', 'customer_companies.userId', '=', 'uc.id')
                ->select('customer_companies.name', 'customer_companies.phone_number', 'customer_companies.id', 'customer_companies.businessId')
                ->where('customer_companies.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('customer_companies.phone_number', 'like', '%' . $request['search']['value'] . '%');

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
            $row['phone_number'] = formatIndonesianPhoneNumber($item->phone_number);
            $row['business'] = $item->type->name;
            $row['address'] = $item->address->address;
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-customer-company='" . $item->id . "' ><i class='bx bx-pencil' ></i></button><button data-customer-company='" . $item->id . "' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
            'picture' => ['file'],
            'name' => ['required', 'min:6', 'max:30'],
            'phone_number' => ['required', 'min:10', 'max:19'],
            'email' => ['required', 'email'],
            'businessId' => ['required'],
            'address.place' => ['required', 'min:4', 'max:30'],
            'address.address' => ['required', 'min:4', 'max:30'],
            'address.city' => ['required', 'min:4', 'max:30'],
            'address.province' => ['required', 'min:4', 'max:30'],
            'address.zipCode' => ['required', 'min:4', 'max:30'],
        ], [
            'businessId.required' => 'The business type is required.',
            'address.address.required' => 'The address is required.',
            'address.place.required' => 'The building is required.',
            'address.city.required' => 'The address city is required.',
            'address.province.required' => 'The address province is required.',
            'address.zipCode.required' => 'The address zip code is required.',
            'address.address.min' => 'The address must be at least 4 characters.',
            'address.place.min' => 'The building must be at least 4 characters.',
            'address.city.min' => 'The address city must be at least 4 characters.',
            'address.province.min' => 'The address province must be at least 4 characters.',
            'address.zipCode.min' => 'The address zip code must be at least 4 characters.',
            'address.address.max' => 'The address must not be greater than 30 characters.',
            'address.place.max' => 'The building must not be greater than 30 characters.',
            'address.city.max' => 'The address city must not be greater than 30 characters.',
            'address.province.max' => 'The address province must not be greater than 30 characters.',
            'address.zipCode.max' => 'The address zip code must not be greater than 30 characters.',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('address', '_token');
            $data['userId'] = (getRole() === 'Developer' ? $request->userId : session('userLogged')->user->id);
            $data['phone_number'] = unFormattedPhoneNumber($data['phone_number']);
            $company = CustomerCompany::create($data);
            $address = $request->only('address')['address'];
            $address['companyId'] = $company->id;
            CompanyAddress::create($address);
            $code = 200;
            $response = ['message' => 'Creating resources successfully'];
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $code = 422;
            $response = ['message' => 'Failed creating resources'];
        }
        return response()->json($response, $code);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
