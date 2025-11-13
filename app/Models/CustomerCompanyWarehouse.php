<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCompanyWarehouse extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['company_id', 'name', 'description'];

    public function racks(): HasMany
    {
        return $this->hasMany(CustomerWarehouseRack::class, 'warehouse_id', 'id');
    }

    public function company(): HasOne
    {
        return $this->hasOne(CustomerCompany::class, 'id', 'company_id');
    }
}
