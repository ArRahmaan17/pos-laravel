<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stocktaking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['goodId', 'expect_stock', 'real_stock', 'user_id', 'company_id', 'status'];
}
