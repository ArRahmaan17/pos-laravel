<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerRoleAccessibility extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['menuId', 'role_id'];

    public function menu(): HasMany
    {
        return $this->hasMany(Permission::class, 'id', 'menuId');
    }
}
