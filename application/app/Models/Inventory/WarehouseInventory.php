<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseInventory extends Model
{
    protected $fillable = ['rackId', 'goodId'];

    use HasFactory;
    use SoftDeletes;

    public function product(): HasOne
    {
        return $this->hasOne(CustomerCompanyGood::class, 'id', 'goodId');
    }
}
