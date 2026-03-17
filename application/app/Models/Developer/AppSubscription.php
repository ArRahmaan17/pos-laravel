<?php

namespace App\Models\Developer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppSubscription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'price'];

    public function planFeature(): HasMany
    {
        return $this->hasMany(AppDetailSubscription::class, 'subscriptionId', 'id');
    }
}
