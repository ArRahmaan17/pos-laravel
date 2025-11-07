<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDetailProductTransaction extends Model
{
    protected $fillable = ['orderCode', 'goodId', 'quantity', 'stock_reference', 'price'];

    use HasFactory;
    use SoftDeletes;

    public function good(): HasOne
    {
        return $this->hasOne(CustomerCompanyGood::class, 'id', 'goodId');
    }
}
