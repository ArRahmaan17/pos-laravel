<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    use HasFactory;
    use SoftDeletes;
}
