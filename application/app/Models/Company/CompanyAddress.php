<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['company_id', 'created_by', 'updated_by', 'deleted_by', 'place', 'address', 'city', 'province', 'zip_code'];
}
