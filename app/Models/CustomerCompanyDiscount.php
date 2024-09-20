<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCompanyDiscount extends Model
{
    protected $fillable = [
        'companyId',
        'code',
        'description',
        'percentage',
        'maxTransactionDiscount',
        'minTransactionPrice',
        'status'
    ];
    use HasFactory;
}
