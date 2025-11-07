<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCompanyStocktaking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['goodId', 'expect_stock', 'real_stock', 'userId', 'companyId', 'status'];
}
