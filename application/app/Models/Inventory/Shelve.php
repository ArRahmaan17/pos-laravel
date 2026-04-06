<?php

namespace App\Models\Inventory;

use App\Models\CustomerWarehouseRackGood;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shelve extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'warehouse_id',
        'name',
        'description',
        'company_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(
            WarehouseInventory::class,
            'rackId',
            'id'
        );
    }
}
