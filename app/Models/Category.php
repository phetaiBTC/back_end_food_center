<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function menus()
    {
        return $this->hasMany(Menu::class); // ความสัมพันธ์ 1 : หลาย (1 category มีหลายเมนู)
    }
}
