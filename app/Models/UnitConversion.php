<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitConversion extends Model
{
    use HasFactory;

    protected $table = 'unit_conversions'; // Nama tabel di database

    /**
     * Kolom yang dapat diisi
     */
    protected $fillable = [
        'item_id',          // ID Barang
        'from_unit_id',     // ID Satuan Asal
        'to_unit_id',       // ID Satuan Tujuan
        'conversion_factor' // Faktor Konversi
    ];

    /**
     * Relasi ke Model Item (Barang)
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Relasi ke Satuan Asal (from_unit_id)
     */
    public function fromUnit()
    {
        return $this->belongsTo(Unit::class, 'from_unit_id');
    }

    /**
     * Relasi ke Satuan Tujuan (to_unit_id)
     */
    public function toUnit()
    {
        return $this->belongsTo(Unit::class, 'to_unit_id');
    }
}
