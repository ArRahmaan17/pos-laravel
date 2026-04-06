<?php

namespace App\Models\Company;

use App\Models\UserManagement\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterTask extends Model
{
    protected $fillable = [
        'name',
        'description',
        'recurrence_type',
        'recurrence_interval',
        'next_run_at',
        'last_run_at',
        'is_active',
        'role_id',
        'scope_id',
        'company_id',
        'created_by',
        'deleted_by',
    ];

    use HasFactory;
    use SoftDeletes;

    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
