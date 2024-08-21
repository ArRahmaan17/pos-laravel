<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppRole extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public function role_users(): HasMany
    {
        return $this->HasMany(
            UserRole::class,
            'roleId',
            'id'
        );
    }
}
