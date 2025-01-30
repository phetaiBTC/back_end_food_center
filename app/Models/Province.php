<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $table = 'province'; // กำหนดชื่อของตารางให้ถูกต้อง
    protected $primaryKey = 'pr_id'; // กำหนดคีย์หลักให้ตรงกับที่ใช้ในตาราง
    protected $fillable = ['pr_name', 'pr_name_en']; // กำหนดฟิลด์ที่สามารถถูกกรอกได้

    public function districts()
    {
        return $this->hasMany(Dristric::class, 'pr_id', 'pr_id');
    }
}
