<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProductTransaction extends Model
{
    protected $fillable = ['orderCode', 'userId', 'companyId', 'total', 'discount'];
    use HasFactory;
}
