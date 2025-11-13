<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppMenu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'route', 'icon', 'parent', 'dev_only', 'place'];

    protected $hidden = ['dev_only', 'place', 'created_at', 'updated_at'];

    public static function getChildMenu($id)
    {
        return self::where('parent', $id)->get();
    }

    public static function customer_menu()
    {
        return self::where('dev_only', 0)->orderBy('created_at')->get()->makeVisible(['dev_only', 'place'])->toArray();
    }

    public function child(): HasMany
    {
        return $this->hasMany(AppMenu::class, 'parent', 'id');
    }
}
