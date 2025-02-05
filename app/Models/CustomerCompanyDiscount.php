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
        'status',
        'maxApply',
    ];

    use HasFactory;
    public static function appliedDiscounts($discountCode): int
    {
        return self::join('customer_product_transactions as cpt', 'cpt.discountId', '=', 'customer_company_discounts.id')->where([
            'customer_company_discounts.status' => 'publish',
            'customer_company_discounts.code' => $discountCode
        ])->count();
    }
}
