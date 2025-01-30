<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dristric extends Model
{
    use HasFactory;
    protected $table = 'dristric'; // กำหนดชื่อของตารางให้ถูกต้อง
    protected $primaryKey = 'dr_id'; // กำหนดคีย์หลักให้ตรงกับที่ใช้ในตาราง
    protected $fillable = ['dr_name', 'dr_name_en', 'pr_id'];
    public function province()
    {
        return $this->belongsTo(Province::class, 'pr_id', 'pr_id');
    }
}
