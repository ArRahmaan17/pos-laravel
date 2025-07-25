<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCompanyStocktaking extends Model
{
    use HasFactory;
    protected $fillable = ['goodId', 'expect_stock', 'real_stock', 'userId', 'companyId', 'status'];
}
