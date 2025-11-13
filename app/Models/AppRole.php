<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppRole extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'description'];

    public function role_users(): HasMany
    {
        return $this->HasMany(
            UserRole::class,
            'role_id',
            'id'
        );
    }
}
