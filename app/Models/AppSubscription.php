<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppSubscription extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price'];

    public function plans(): HasMany
    {
        return $this->hasMany(AppDetailSubscription::class, 'subscriptionId', 'id');
    }
}
