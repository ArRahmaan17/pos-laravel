<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerWarehouseRack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['warehouseId', 'name', 'description'];

    public function products(): HasMany
    {
        return $this->hasMany(
            CustomerWarehouseRackGood::class,
            'rackId',
            'id'
        );
    }
}
