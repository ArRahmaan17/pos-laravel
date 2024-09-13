<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerWarehouseRackGood extends Model
{
    protected $fillable = ['rackId', 'goodId'];
    use HasFactory;
    public function product(): HasOne
    {
        return $this->hasOne(CustomerCompanyGood::class, 'id', 'goodId');
    }
}
