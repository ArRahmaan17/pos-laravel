<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerCompanyTask extends Model
{
    use HasFactory;
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'userId');
    }
    public function details(): HasMany
    {
        return $this->hasMany(CustomerCompanyTaskDetail::class, 'taskId', 'id');
    }
}
