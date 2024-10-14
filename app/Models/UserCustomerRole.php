<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserCustomerRole extends Model
{
    use HasFactory;

    protected $fillable = ['userId', 'roleId', 'companyId'];

    public static function employeeCompany($userId)
    {
        return self::join('customer_companies as cp', 'cp.id', '=', 'user_customer_roles.companyId')->where('user_customer_roles.userId', $userId)->first();
    }

    public static function employeeMenu($userId)
    {
        return self::join('customer_role_accessibilities as cra', 'cra.roleId', '=', 'user_customer_roles.roleId')->where('user_customer_roles.userId', $userId)->count();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'userId');
    }

    public function role(): HasOne
    {
        return $this->hasOne(CustomerRole::class, 'id', 'roleId');
    }
}
