<?php

namespace App\Models\Product;

use App\Models\Company\BusinessType;
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
        return $this->hasOne(BusinessType::class, 'id', 'business_id');
    }
}
