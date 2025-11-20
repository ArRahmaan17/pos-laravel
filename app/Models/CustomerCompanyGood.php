<?php

namespace App\Models;

use App\Traits\HasDefaultSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CustomerCompanyGood extends Model
{
    use HasDefaultSearch;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'price', 'buy_price', 'stock', 'picture', 'company_id', 'unit_id', 'category_id', 'status'];

    protected $defaultSearchColumns = ['name', 'price', 'buy_price', 'stock', 'status'];

    protected $defaultSearchRelations = ['unit' => ['name', 'description'], 'type' => ['name', 'description']];

    protected $defaultCategoryColumn = 'category_id';

    protected $defaultCategoryId = 'all';

    public function unit(): HasOne
    {
        return $this->hasOne(AppGoodUnit::class, 'id', 'unit_id');
    }

    public function type(): HasOne
    {
        return $this->hasOne(CustomerProductType::class, 'id', 'category_id');
    }

    public static function shelf_less($company_id)
    {
        return self::whereRaw(
            DB::raw('id not in (select goodId from customer_warehouse_rack_goods)')
        )->where('products.company_id', $company_id)
            ->get();
    }
}
