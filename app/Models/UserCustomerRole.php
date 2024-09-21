<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class UserCustomerRole extends Model
{
    use HasFactory;
    protected $fillable = ['userId', 'roleId', 'companyId'];
    static public function employeeCompany($userId)
    {
        self::join('customer_companies as cp', 'cp.id', '=', 'user_customer_roles')->where('user_customer_roles.user', $userId)->first();
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
