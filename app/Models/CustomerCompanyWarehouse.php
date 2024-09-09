<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerCompanyWarehouse extends Model
{
    use HasFactory;
    protected $fillable = ['companyId', 'name', 'description'];

    public function racks(): HasMany
    {
        return $this->hasMany(CustomerWarehouseRack::class, 'warehouseId', 'id');
    }
    public function company(): HasOne
    {
        return $this->hasOne(CustomerCompany::class, 'id', 'companyId');
    }
}
