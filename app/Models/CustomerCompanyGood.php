<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerCompanyGood extends Model
{
    protected $fillable = ['name', 'price', 'stock', 'picture', 'companyId', 'unitId', 'status'];
    use HasFactory;
    public function unit(): HasOne
    {
        return $this->hasOne(AppGoodUnit::class, 'id', 'unitId');
    }
}
