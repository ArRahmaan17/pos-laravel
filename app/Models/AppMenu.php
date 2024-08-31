<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppMenu extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'route', 'icon', 'parent', 'dev_only', 'place'];

    static public function getChildMenu($id)
    {
        return self::where('parent', $id)->get();
    }
    public function child(): HasMany
    {
        return $this->hasMany(AppMenu::class, 'parent', 'id');
    }
}
