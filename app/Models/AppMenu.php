<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppMenu extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'route', 'icon', 'parent', 'dev_only', 'place'];
    protected $hidden = ['dev_only', 'id', 'place', 'parent'];
    static public function getChildMenu($id)
    {
        return self::where('parent', $id)->get();
    }
    static public function customer_menu()
    {
        return self::where('dev_only', 0)->get();
    }
    public function child(): HasMany
    {
        return $this->hasMany(AppMenu::class, 'parent', 'id');
    }
}
