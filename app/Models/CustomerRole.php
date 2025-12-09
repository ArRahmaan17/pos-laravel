<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerRole extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id', 'name', 'description'];

    public static function customer_roles($customerId = null, $id = null)
    {
        $where = [];
        if ($customerId === null) {
            $where[] = ['customer_roles.user_id', '<>', $customerId];
        } else {
            $where[] = ['customer_roles.user_id', '=', $customerId];
        }
        if ($id === null) {
            $where[] = ['customer_roles.id', '<>', $id];
        } else {
            $where[] = ['customer_roles.id', '=', $id];
        }

        return self::select('customer_roles.*')
            ->join('users as u', 'customer_roles.user_id', '=', 'u.id')
            ->where($where)->get();
    }

    public function role_users(): HasMany
    {
        return $this->HasMany(
            UserRole::class,
            'role_id',
            'id'
        );
    }

    public function role_menus(): HasManyThrough
    {
        return $this->hasManyThrough(
            Permission::class,
            CustomerRoleAccessibility::class,
            'role_id',
            'id',
            'id',
            'menuId'
        );
    }

    public function userByRole(): HasMany
    {
        return $this->hasMany(UserCustomerRole::class, 'role_id', 'id')->where('company_id', session('userLogged')['company']['id']);
    }
}
