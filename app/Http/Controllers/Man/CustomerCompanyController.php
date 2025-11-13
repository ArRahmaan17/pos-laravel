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
        $where = [['user_id', '=', session('userLogged')['user']['id']]];
        if (getRole() === 'Developer') {
            $where = [['user_id', '<>', null]];
        }
        $totalData = CustomerCompany::with('address', 'type', 'manager')
            ->orderBy('companies.id', 'asc')
            ->where($where)
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = CustomerCompany::with('address', 'type', 'manager')
                ->select('companies.name', 'companies.phone_number', 'companies.id', 'companies.bussiness_id', 'companies.user_id');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $assets = $assets->where($where)->get();
        } else {
            $assets = CustomerCompany::with('address', 'type', 'manager')
                ->select('companies.name', 'companies.phone_number', 'companies.id', 'companies.bussiness_id', 'companies.user_id')
                ->where('companies.name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('companies.phone_number', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where($where)->get();

            $totalFiltered = CustomerCompany::with('address', 'type', 'manager')
                ->select('companies.name', 'companies.phone_number', 'companies.id', 'companies.bussiness_id', 'companies.user_id')
                ->where('companies.name', 'like', '%'.$request['search']['value'].'%')
                ->orWhere('companies.phone_number', 'like', '%'.$request['search']['value'].'%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['order'][0]['name'].' '.$request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where($where)->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['order_number'] = $request['start'] + ($index + 1);
            $row['name'] = '<div class="font-sm">'.$item->name.'</div><div class="text-xs">'.$item->manager->name.'</div>';
            $row['phone_number'] = formatIndonesianPhoneNumber($item->phone_number);
            $row['business'] = $item->type->name;
            $row['address'] = '<div class="font-sm">'.$item->address->place.'</div><div class="text-xs">'.$item->address->address.' '.$item->address->city.' '.$item->address->province.' '.$item->address->zip_code.'</div>';
            $row['action'] = "<button class='btn btn-icon btn-warning edit' data-customer-company='".$item->id."' ><i class='bx bx-pencil' ></i></button><button class='btn btn-icon ".($item->id != session('userLogged')['company']['id'] ? 'btn-info activate' : 'btn-danger logout')."' data-customer-company='".$item->id."' >".($item->id != session('userLogged')['company']['id'] ? "<i class='bx bxs-log-in' ></i>" : "<i class='bx bxs-log-out' ></i>").'</button>';
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
        // session()->activity()
        $request->validate([
            'picture' => ['file', 'extensions:jpg,png'],
            'name' => ['required', 'min:6', 'max:30'],
            'phone_number' => ['required', 'min:10', 'max:19'],
            'email' => ['required', 'email', 'unique:companies,email'],
            'bussiness_id' => ['required'],
            'address.place' => ['required', 'min:4', 'max:30'],
            'address.address' => ['required', 'min:4', 'max:30'],
            'address.city' => ['required', 'min:4', 'max:30'],
            'address.province' => ['required', 'min:4', 'max:30'],
            'address.zip_code' => ['required', 'min:4', 'max:30'],
        ], [
            'bussiness_id.required' => 'The business type is required.',
            'address.address.required' => 'The address is required.',
            'address.place.required' => 'The building is required.',
            'address.city.required' => 'The address city is required.',
            'address.province.required' => 'The address province is required.',
            'address.zip_code.required' => 'The address zip code is required.',
            'address.address.min' => 'The address must be at least 4 characters.',
            'address.place.min' => 'The building must be at least 4 characters.',
            'address.city.min' => 'The address city must be at least 4 characters.',
            'address.province.min' => 'The address province must be at least 4 characters.',
            'address.zip_code.min' => 'The address zip code must be at least 4 characters.',
            'address.address.max' => 'The address must not be greater than 30 characters.',
            'address.place.max' => 'The building must not be greater than 30 characters.',
            'address.city.max' => 'The address city must not be greater than 30 characters.',
            'address.province.max' => 'The address province must not be greater than 30 characters.',
            'address.zip_code.max' => 'The address zip code must not be greater than 30 characters.',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('address', '_token');
            if ($request->has('picture')) {
                $profile_picture = md5(now()->format('Y-m-d H:i:s')).'.'.$request->file('picture')->getClientOriginalExtension();
                $profile_picture = Storage::disk('company-profile')
                    ->putFileAs('/', $request->picture, $profile_picture);
                $data['picture'] = $profile_picture;
            } else {
                $data['picture'] = 'default-picture.png';
            }
            $data['user_id'] = (getRole() === 'Developer' ? $request->user_id : session('userLogged')['user']['id']);
            $data['phone_number'] = unFormattedPhoneNumber($data['phone_number']);
            $company = CustomerCompany::create($data);
            $address = $request->only('address')['address'];
            $address['company_id'] = $company->id;
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

    public function profile()
    {
        $types = BusinessType::all();

        return view('man.customer-company-profile', compact('types'));
    }

    public function company()
    {
        $where = [['user_id', '=', session('userLogged')['company']['user_id']]];
        if (getRole() === 'Developer') {
            $where = [['user_id', '<>', null]];
        }
        $data = CustomerCompany::with('address', 'type')->where($where)->get()->map(function ($company) {
            $company->attribute = buatSingkatan($company->name);

            return $company;
        });
        $code = 200;
        $response = ['message' => 'Showing resource successfully', 'data' => $data];
        if (empty($data)) {
            $code = 404;
            $response = ['message' => 'Failed showing resource', 'data' => $data];
        }

        return response()->json($response, $code);
    }

    public function loginCompany(Request $request)
    {
        $data = session('userLogged');
        $where = [['id', '=', $request->id]];
        if (in_array($data['role']['name'], ['Manager'])) {
            $where = [['id', '=', $request->id], ['user_id', '=', $data['user_id']]];
        }
        if (! in_array($data['role']['name'], ['Manager', 'Developer'])) {
            abort(401);
        }
        $data['company'] = CustomerCompany::with('address')->where($where)->first()->toArray();
        session()->flush();
        session(['userLogged' => $data]);
        $code = 200;
        $response = ['message' => 'login to company successfully'];

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
            'phone_number' => ['required', 'min:10', 'max:19', 'unique:companies,phone_number,'.$id],
            'email' => ['required', 'email', 'unique:companies,email,'.$id],
            'bussiness_id' => ['required'],
            'address.place' => ['required', 'min:4', 'max:30'],
            'address.address' => ['required', 'min:4', 'max:30'],
            'address.city' => ['required', 'min:4', 'max:30'],
            'address.province' => ['required', 'min:4', 'max:30'],
            'address.zip_code' => ['required', 'min:4', 'max:30'],
        ], [
            'bussiness_id.required' => 'The business type is required.',
            'address.address.required' => 'The address is required.',
            'address.place.required' => 'The building is required.',
            'address.city.required' => 'The address city is required.',
            'address.province.required' => 'The address province is required.',
            'address.zip_code.required' => 'The address zip code is required.',
            'address.address.min' => 'The address must be at least 4 characters.',
            'address.place.min' => 'The building must be at least 4 characters.',
            'address.city.min' => 'The address city must be at least 4 characters.',
            'address.province.min' => 'The address province must be at least 4 characters.',
            'address.zip_code.min' => 'The address zip code must be at least 4 characters.',
            'address.address.max' => 'The address must not be greater than 30 characters.',
            'address.place.max' => 'The building must not be greater than 30 characters.',
            'address.city.max' => 'The address city must not be greater than 30 characters.',
            'address.province.max' => 'The address province must not be greater than 30 characters.',
            'address.zip_code.max' => 'The address zip code must not be greater than 30 characters.',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('address', '_token');
            if ($request->has('picture')) {
                $company = CustomerCompany::find($id);
                $profile_picture = md5(now()->format('Y-m-d H:i:s')).'.'.$request->file('picture')
                    ->getClientOriginalExtension();
                if ($company->picture != 'default-picture.png') {
                    Storage::disk('company-profile')
                        ->delete($company->picture);
                }
                $profile_picture = Storage::disk('company-profile')
                    ->putFileAs('/', $request->picture, $profile_picture);
                $data['picture'] = $profile_picture;
            }
            $data['user_id'] = (getRole() === 'Developer' ? $request->user_id : session('userLogged')['user']['id']);
            $data['phone_number'] = unFormattedPhoneNumber($data['phone_number']);
            CustomerCompany::find($id)->update($data);
            $address = $request->only('address')['address'];
            CompanyAddress::where('company_id', $id)->update($address);
            $code = 200;
            $response = ['message' => 'Updating resources successfully'];
            DB::commit();
            $user = session('userLogged');
            $user['company'] = CustomerCompany::with('address')->where(['id' => session('userLogged')['company']['id'], 'user_id' => session('userLogged')['user']['id']])->first()->toArray();
            session()->flush();
            session(['userLogged' => collect($user)->toArray()]);
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
            CompanyAddress::where('company_id', $id)->delete();
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
