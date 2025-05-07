<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerTemporaryProduct extends Model
{
    use HasFactory;
    protected $fillable = ['orderCode', 'userId', 'companyId', 'customerCompanyGoodId', 'name', 'picture', 'stock', 'price', 'buyPrice', 'unitId', 'accepted', 'status'];
    public function unit(): HasOne
    {
        return $this->hasOne(AppGoodUnit::class, 'id', 'unitId');
    }
    public function product(): HasOne
    {
        return $this->hasOne(CustomerCompanyGood::class, 'id', 'customerCompanyGoodId');
    }
}
