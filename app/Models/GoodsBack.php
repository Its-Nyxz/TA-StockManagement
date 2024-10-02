<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsBack extends Model
{
    use HasFactory;
    protected $table = "goods_back";
    protected $fillable = [
        'item_id',
        'user_id',
        'quantity',
        'date_backs',
        'invoice_number',
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
