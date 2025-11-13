<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProductType extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function category(): HasOne
    {
        return $this->hasOne(BusinessType::class, 'id', 'bussiness_id');
    }
}
