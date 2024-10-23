<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasFactory;
    protected $table = "stock_opnames";
    protected $fillable = [
        'item_id',
        'user_id',
        'stok_sistem',
        'stok_fisik',
        'quantity',
        'date_so',
        'invoice_number',
        'supplier_id',
        'description',
        'status',
    ];

    public function item(): BelongsTo
    {
        return $this -> belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this -> belongsTo(User::class);
    }

    public function supplier(): BelongsTo
    {
        return $this -> belongsTo(Supplier::class);
    }
}
