<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    protected $table = "customers";
    protected $fillable = [
        'name',
        'slug',
        'phone_number',
        'address'
    ];

    // Event untuk membuat slug sebelum data disimpan
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            $customer->slug = Str::slug($customer->name); // Membuat slug dari nama
        });

        static::updating(function ($customer) {
            $customer->slug = Str::slug($customer->name); // Memperbarui slug jika nama berubah
        });
    }

    public function goodsOuts(): HasMany
    {
        return $this->hasMany(GoodsOut::class);
    }
}
