<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerWarehouseRack extends Model
{
    use HasFactory;

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
