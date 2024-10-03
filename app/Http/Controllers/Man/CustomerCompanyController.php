<?php

namespace App\Http\Controllers\Man;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use App\Models\CompanyAddress;
use App\Models\CustomerCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $assets = $assets->get();
        } else {
            $assets = CustomerCompany::with('address', 'type')->join('users as uc', 'customer_companies.userId', '=', 'uc.id')
                ->select('customer_companies.name', 'customer_companies.phone_number', 'customer_companies.id', 'customer_companies.businessId')
                ->where('customer_companies.name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('customer_companies.phone_number', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->get();

            $totalFiltered = CustomerCompany::join('users as uc', 'customer_companies.userId', '=', 'uc.id')
                ->select('customer_companies.name', 'customer_companies.phone_number', 'customer_companies.id', 'customer_companies.businessId')
                ->where('customer_companies.name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('customer_companies.phone_number', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
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
            $row['address'] = $item->address->place.'<br>'.$item->address->address.' '.$item->address->city.' '.$item->address->province.' '.$item->address->zipCode;
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-customer-company='".$item->id."' ><i class='bx bx-pencil' ></i></button><button data-customer-company='".$item->id."' class='btn btn-icon btn-danger delete'><i class='bx bxs-trash-alt' ></i></button>";
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
            'picture' => ['file', 'extensions:jpg,png'],
            'name' => ['required', 'min:6', 'max:30'],
            'phone_number' => ['required', 'min:10', 'max:19'],
            'email' => ['required', 'email', 'unique:customer_companies,email'],
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
            if ($request->has('picture')) {
                $profile_picture = md5(now('Asia/Jakarta')->format('Y-m-d H:i:s')).'.'.$request->file('picture')->getClientOriginalExtension();
                $profile_picture = Storage::disk('company-profile')
                    ->putFileAs('/', $request->picture, $profile_picture);
                $data['picture'] = $profile_picture;
            } else {
                $data['picture'] = 'default-picture.png';
            }
            $data['userId'] = (getRole() === 'Developer' ? $request->userId : session('userLogged')['user']['id']);
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
        $data = CustomerCompany::with('address', 'type')->find($id);
        $code = 200;
        $response = ['message' => 'Showing resource successfully', 'data' => $data];
        if (empty($data)) {
            $code = 404;
            $response = ['message' => 'Failed showing resource', 'data' => $data];
        }

        return response()->json($response, $code);
    }

    public function company()
    {
        $where = [['userId', '=', session('userLogged')['user']['id']]];
        if (getRole() === 'Developer') {
            $where = [['userId', '<>', null]];
        }
        $data = CustomerCompany::with('address', 'type')->where($where)->get();
        $code = 200;
        $response = ['message' => 'Showing resource successfully', 'data' => $data];
        if (empty($data)) {
            $code = 404;
            $response = ['message' => 'Failed showing resource', 'data' => $data];
        }

        return response()->json($response, $code);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'picture' => ['file', 'extensions:jpg,png'],
            'name' => ['required', 'min:6', 'max:30'],
            'phone_number' => ['required', 'min:10', 'max:19', 'unique:customer_companies,phone_number,'.$id],
            'email' => ['required', 'email', 'unique:customer_companies,email,'.$id],
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
            if ($request->has('picture')) {
                $company = CustomerCompany::find($id);
                $profile_picture = md5(now('Asia/Jakarta')->format('Y-m-d H:i:s')).'.'.$request->file('picture')
                    ->getClientOriginalExtension();
                if ($company->picture != 'default-picture.png') {
                    Storage::disk('company-profile')
                        ->delete($company->picture);
                }
                $profile_picture = Storage::disk('company-profile')
                    ->putFileAs('/', $request->picture, $profile_picture);
                $data['picture'] = $profile_picture;
            }
            $data['userId'] = (getRole() === 'Developer' ? $request->userId : session('userLogged')['user']['id']);
            $data['phone_number'] = unFormattedPhoneNumber($data['phone_number']);
            CustomerCompany::where('id', $id)->update($data);
            $address = $request->only('address')['address'];
            CompanyAddress::where('companyId', $id)->update($address);
            $code = 200;
            $response = ['message' => 'Updating resources successfully'];
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $code = 422;
            $response = ['message' => 'Failed updating resources'];
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
            CompanyAddress::where('companyId', $id)->delete();
            CustomerCompany::where('id', $id)->delete();
            $code = 200;
            $response = ['message' => 'Deleting resources successfully'];
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $code = 422;
            $response = ['message' => 'Failed deleting resources'];
        }

        return response()->json($response, $code);
    }
}
