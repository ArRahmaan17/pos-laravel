<?php

namespace App\Models\UserManagement;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id', 'role_id'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function companies(): HasManyThrough
    {
        return $this->hasManyThrough(Company::class, UserCompany::class, 'user_id', 'user_id', 'id', 'user_id');
    }

    public static function employeeCompany($user_id)
    {
        return self::join('companies as cp', 'cp.id', '=', 'user_roles.company_id')->where('user_roles.user_id', $user_id)->first();
    }

    public static function employeeMenu($user_id)
    {
        return self::join('customer_role_accessibilities as cra', 'cra.role_id', '=', 'user_roles.role_id')->where('user_roles.user_id', $user_id)->count();
    }
}
