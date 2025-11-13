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

    protected $fillable = ['name', 'user_id', 'picture', 'phone_number', 'email', 'bussiness_id', 'affiliate_code'];

    public function address(): HasOne
    {
        return $this->hasOne(CompanyAddress::class, 'company_id', 'id');
    }

    public function type(): HasOne
    {
        return $this->hasOne(BusinessType::class, 'id', 'bussiness_id');
    }

    public function manager(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function racks(): HasMany
    {
        return $this->hasMany(CustomerWarehouseRack::class, 'id', 'warehouse_id');
    }
}
