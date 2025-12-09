<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['task_id', 'masterId', 'start_at', 'end_at', 'status', 'evidance'];

    public function master(): HasOne
    {
        return $this->hasOne(MasterTask::class, 'id', 'masterId');
    }
}
