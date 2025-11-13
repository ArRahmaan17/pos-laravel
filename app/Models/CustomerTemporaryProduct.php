<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTemporaryProduct extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends = ['status_transaction'];

    protected $fillable = ['orderCode', 'transaction_created',  'user_id', 'company_id', 'customerCompanyGoodId', 'name', 'picture', 'stock', 'stock_reference', 'price', 'buy_price', 'unit_id', 'type_id', 'accepted', 'accepted_by', 'status'];

    public function creater(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(AppGoodUnit::class, 'id', 'unit_id');
    }

    public function reference(): HasOne
    {
        return $this->hasOne(CustomerCompanyGood::class, 'id', 'customerCompanyGoodId');
    }

    public function accepter(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'accepted_by');
    }

    public function changedProduct(): HasMany
    {
        return $this->hasMany(CustomerTemporaryProduct::class, 'transaction_created', 'transaction_created');
    }

    public function getStatusTransactionAttribute()
    {
        return statusTransaction($this->orderCode);
    }
}
