<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Supplier extends Model
{
    use HasFactory;
    protected $table = "suppliers";
    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'email',
        'website',
    ];

    public function goodsIns(): HasMany
    {
        return $this -> hasMany(GoodsIn::class);
    }

    public function item(): HasMany
    {
        return $this -> hasMany(Item::class);
    }

    public function goodsOut(): HasMany
    {
        return $this -> hasMany(GoodsOut::class);
    }

    public function goodsBack(): HasMany
    {
        return $this -> hasMany(GoodsBack::class);
    }

    public function stockOpname(): HasMany
    {
        return $this -> hasMany(StockOpname::class);
    }

    
}
