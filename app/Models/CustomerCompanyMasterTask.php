<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerCompanyMasterTask extends Model
{
    protected $fillable = ['companyId', 'name', 'description', 'roleId', 'priority', 'repeateable'];
    use HasFactory;

    public function role(): HasOne
    {
        return $this->hasOne(CustomerRole::class, 'id', 'roleId');
    }
}
