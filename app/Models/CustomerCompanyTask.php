<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCompanyTask extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['userId', 'companyId', 'name', 'start_at', 'end_at', 'time_limit', 'percentage'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'userId');
    }

    public function details(): HasMany
    {
        return $this->hasMany(CustomerCompanyTaskDetail::class, 'taskId', 'id');
    }
}
