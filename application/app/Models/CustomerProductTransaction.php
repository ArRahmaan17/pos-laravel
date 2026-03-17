<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProductTransaction extends Model
{
    protected $fillable = ['orderCode', 'user_id', 'company_id', 'discountId', 'total', 'discount', 'status'];

    use HasFactory;
    use SoftDeletes;

    public function details(): HasMany
    {
        return $this->hasMany(CustomerDetailProductTransaction::class, 'orderCode', 'orderCode');
    }
}
