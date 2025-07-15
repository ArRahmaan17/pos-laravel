<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerCompanyTaskDetail extends Model
{
    use HasFactory;

    public function master(): HasOne
    {
        return $this->hasOne(CustomerCompanyMasterTask::class, 'id', 'masterId');
    }
}
