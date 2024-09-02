<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerRoleAccessibility extends Model
{
    use HasFactory;
    protected $fillable = ['menuId', 'roleId'];
    public function menu(): HasMany
    {
        return $this->hasMany(AppMenu::class, 'id', 'menuId');
    }
}
