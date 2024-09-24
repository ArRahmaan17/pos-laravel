<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerProductTransaction extends Model
{
    protected $fillable = ['orderCode', 'userId', 'companyId', 'total', 'discount'];
    use HasFactory;
    public function detail(): HasMany
    {
        return $this->hasMany(CustomerProductTransaction::class, 'orderCode', 'orderCode');
    }
}
