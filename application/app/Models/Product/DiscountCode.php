<?php

namespace App\Models\Product;

use App\Models\Promo\Discount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscountCode extends Model
{
    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class, 'discount_code_id');
    }
}
