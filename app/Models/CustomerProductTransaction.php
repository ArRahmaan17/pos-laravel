<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerProductTransaction extends Model
{
    protected $fillable = ['orderCode', 'userId', 'companyId', 'discountId', 'total', 'discount', 'status'];

    use HasFactory;

    public function details(): HasMany
    {
        return $this->hasMany(CustomerDetailProductTransaction::class, 'orderCode', 'orderCode');
    }
}
