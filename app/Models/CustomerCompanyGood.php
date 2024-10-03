<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class CustomerCompanyGood extends Model
{
    protected $fillable = ['name', 'price', 'stock', 'picture', 'companyId', 'unitId', 'status'];

    use HasFactory;

    public function unit(): HasOne
    {
        return $this->hasOne(AppGoodUnit::class, 'id', 'unitId');
    }

    public static function shelf_less($companyId)
    {
        return self::whereRaw(
            DB::raw('id not in (select goodId from customer_warehouse_rack_goods)')
        )->where('customer_company_goods.companyId', $companyId)
            ->get();
    }
}
