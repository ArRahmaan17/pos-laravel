<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGoodWarehouse extends Model
{
    use HasFactory;

    protected $fillable = ['warehouseId', 'rackId', 'goodId'];
}
