<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerDetailProductTransaction extends Model
{
    protected $fillable = ['orderCode', 'goodId', 'quantity', 'price', 'total'];
    use HasFactory;
    public function good(): HasOne
    {
        return $this->hasOne(CustomerCompanyGood::class, 'id', 'goodId');
    }
}
