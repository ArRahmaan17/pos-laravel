<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Traits\HasDefaultSearch;

class CustomerCompanyGood extends Model
{
    use HasFactory;
    use HasDefaultSearch;
    use SoftDeletes;
    protected $fillable = ['name', 'price', 'buyPrice', 'stock', 'picture', 'companyId', 'unitId', 'typeId', 'status'];
    protected $defaultSearchColumns = ['name', 'price', 'buyPrice', 'stock', 'status'];
    protected $defaultSearchRelations = ['unit' => ['name', 'description'], 'type' => ['name', 'description']];
    protected $defaultCategoryColumn = 'typeId';
    protected $defaultCategoryId = 'all';

    public function unit(): HasOne
    {
        return $this->hasOne(AppGoodUnit::class, 'id', 'unitId');
    }
    public function type(): HasOne
    {
        return $this->hasOne(CustomerProductType::class, 'id', 'typeId');
    }

    public static function shelf_less($companyId)
    {
        return self::whereRaw(
            DB::raw('id not in (select goodId from customer_warehouse_rack_goods)')
        )->where('customer_company_goods.companyId', $companyId)
            ->get();
    }
}
