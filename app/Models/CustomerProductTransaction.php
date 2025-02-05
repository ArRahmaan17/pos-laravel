<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerProductTransaction extends Model
{
<<<<<<< HEAD
    protected $fillable = ['orderCode', 'userId', 'companyId', 'total', 'discount', 'status'];
=======
    protected $fillable = ['orderCode', 'userId', 'companyId', 'discountId', 'total', 'discount'];
>>>>>>> cb3cc10 (feat: product transaction (model), authentication (module), app good unit (module, model), customer company (module), customer company discount (module,model, migration), company good (module,migration), company warehouse (module), customer role (module, model), warehouse rack (module), user customer (module), check authorization page (middleware), customer role (model))

    use HasFactory;

    public function details(): HasMany
    {
        return $this->hasMany(CustomerDetailProductTransaction::class, 'orderCode', 'orderCode');
    }
}
