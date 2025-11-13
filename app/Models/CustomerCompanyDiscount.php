<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCompanyDiscount extends Model
{
    protected $fillable = [
        'company_id',
        'code',
        'description',
        'percentage',
        'max_transaction_discount',
        'min_transaction_price',
        'status',
        'maxApply',
    ];

    use HasFactory;
    use SoftDeletes;

    public static function appliedDiscounts($discountCode): int
    {
        return self::join('transactions as cpt', 'cpt.discountId', '=', 'discounts.id')->where([
            'discounts.status' => 'publish',
            'discounts.code' => $discountCode,
        ])->count();
    }
}
