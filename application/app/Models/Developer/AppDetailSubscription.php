<?php

namespace App\Models\Developer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppDetailSubscription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['subscriptionId', 'planFeature'];
}
