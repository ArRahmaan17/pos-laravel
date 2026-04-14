<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryMovementType extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'code',
        'direction',
        'name',
        'description',
        'is_system',
        'company_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
