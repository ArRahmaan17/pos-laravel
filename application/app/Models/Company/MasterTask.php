<?php

namespace App\Models\Company;

use App\Models\UserManagement\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterTask extends Model
{
    protected $fillable = ['company_id', 'name', 'description', 'role_id', 'priority', 'repeateable'];

    use HasFactory;
    use SoftDeletes;

    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
