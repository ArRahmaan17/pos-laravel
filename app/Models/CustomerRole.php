<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CustomerRole extends Model
{
    use HasFactory;

    protected $fillable = ['userId', 'name', 'description'];

    public static function customer_roles($customerId = null, $id = null)
    {
        $where = [];
        if ($customerId == null) {
            $where[] = ['customer_roles.userId', '<>', $customerId];
        } else {
            $where[] = ['customer_roles.userId', '=', $customerId];
        }
        if ($id == null) {
            $where[] = ['customer_roles.id', '<>', $id];
        } else {
            $where[] = ['customer_roles.id', '=', $id];
        }

        return self::select('customer_roles.*')
            ->join('users as u', 'customer_roles.userId', '=', 'u.id')
            ->where($where)->get();
    }

    public static function exists_role($where)
    {
        return self::where($where)->get();
    }

    public function role_users(): HasMany
    {
        return $this->HasMany(
            UserRole::class,
            'roleId',
            'id'
        );
    }

    public function role_menus(): HasManyThrough
    {
        return $this->hasManyThrough(
            AppMenu::class,
            CustomerRoleAccessibility::class,
            'roleId',
            'id',
            'id',
            'menuId'
        );
    }
}
