<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryMovement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'transaction_item_id',
        'warehouse_id',
        'product_id',
        'inventory_movement_type_id',
        'quantity_change',
        'remarks',
        'company_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function type(): HasOne
    {
        return self::hasOne(InventoryMovementType::class, 'id', 'inventory_movement_type_id');
    }
}
