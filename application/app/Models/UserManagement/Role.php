<?php

namespace App\Models\UserManagement;

use App\Models\Company\Company;
use App\Models\Developer\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'parent_role_id',
        'scope_id',
        'is_system',
        'company_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function role_users(): HasMany
    {
        return $this->HasMany(
            UserRole::class,
            'role_id',
            'id'
        );
    }

    public function scope(): HasOne
    {
        return $this->hasOne(Scope::class, 'id', 'scope_id');
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'id');
    }
}
