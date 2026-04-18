<?php

namespace App\Models\Promo;

use App\Models\Company\Company;
use App\Models\Inventory\KitProduct;
use App\Models\Inventory\Product;
use App\Models\Product\DiscountCode;
use App\Models\Product\DiscountType;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'kit_product_id',
        'discount_code_id',
        'discount_type_id',
        'discount_value',
        'percentage',
        'max_usage',
        'max_amount_discount',
        'min_amount_price',
        'expirated_at',
        'status',
        'company_id',
        'created_by',
        'deleted_by',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_amount_discount' => 'decimal:2',
        'min_amount_price' => 'decimal:2',
        'expirated_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function kitProduct(): BelongsTo
    {
        return $this->belongsTo(KitProduct::class, 'kit_product_id');
    }

    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class, 'discount_code_id');
    }

    public function discountType(): BelongsTo
    {
        return $this->belongsTo(DiscountType::class, 'discount_type_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
