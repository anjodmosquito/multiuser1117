<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'name',
        'lprice',
        'mprice',
        'hprice',
        'quantity_added',
        'total_quantity',
        'dosage',
        'expdate',
        'action_type'
    ];

    protected $casts = [
        'expdate' => 'date',
        'lprice' => 'decimal:2',
        'mprice' => 'decimal:2',
        'hprice' => 'decimal:2',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
} 