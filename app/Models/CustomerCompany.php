<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCompany extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'userId', 'picture', 'phone_number', 'email', 'businessId', 'affiliate_code'];

    public function address(): HasOne
    {
        return $this->hasOne(CompanyAddress::class, 'companyId', 'id');
    }

    public function type(): HasOne
    {
        return $this->hasOne(BusinessType::class, 'id', 'businessId');
    }

    public function manager(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'userId');
    }

    public function racks(): HasMany
    {
        return $this->hasMany(CustomerWarehouseRack::class, 'id', 'warehouseId');
    }
}
