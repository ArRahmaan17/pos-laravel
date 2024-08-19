<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserCustomerRole extends Model
{
    use HasFactory;
    protected $fillable = ['userId', 'roleId'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'userId');
    }
    public function role(): HasOne
    {
        return $this->hasOne(CustomerRole::class, 'id', 'customerRoleId');
    }
}
