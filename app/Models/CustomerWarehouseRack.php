<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerWarehouseRack extends Model
{
    use HasFactory;
    protected $fillable = ['warehouseId', 'name', 'description'];
}
