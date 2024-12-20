<?php

// namespace App\Helpers;

use App\Models\GoodsIn;
use App\Models\GoodsOut;
use App\Models\GoodsBack;
use App\Models\Item;
use App\Models\StockOpname;
use Carbon\Carbon;

class Notification
{
    public static function getLowStockNotifGet()
    {
        // Mendefinisikan array untuk menyimpan data stok rendah
        $lowStockData = [];

        // Mengambil semua data item beserta informasi relasinya dalam satu query
        $items = Item::with(['brand', 'supplier', 'unit'])
            ->whereNotNull('stock_limit') // Hanya mengambil item dengan stock_limit yang ditentukan
            ->get();

        foreach ($items as $item) {
            // Menghitung total stok untuk setiap item
            $totalStock = $item->quantity
                + GoodsIn::where('item_id', $item->id)->sum('quantity')
                - GoodsOut::where('item_id', $item->id)->sum('quantity')
                - GoodsBack::where('item_id', $item->id)->sum('quantity')
                + StockOpname::where('item_id', $item->id)->sum('quantity');

            // Memeriksa apakah total stok kurang dari stock_limit
            if ($totalStock <= $item->stock_limit) {
                $lowStockData[] = (object) [
                    'item_id' => $item->id,
                    'total_stock' => max(0, $totalStock), // Menghindari nilai negatif
                    'item_name' => $item->name,
                    'item_code' => $item->code,
                    'merk' => $item->brand ? $item->brand->name : '',
                    'supplier' => $item->supplier ? $item->supplier->name : '',
                    'unit' => $item->unit ? $item->unit->name : '',
                    // 'created_at' => GoodsIn::where('item_id', $item->id)->first()?->created_at, // Jika perlu menampilkan waktu
                ];
            }
        }

        // Mengembalikan data stok rendah
        return $lowStockData;
    }
}

function getLowStockNotifCount()
{
    // Mendefinisikan array untuk menyimpan ID stok rendah
    $lowStockIds = [];

    // Mengambil semua produk (termasuk ID dan stock_limit)
    $products = Item::all();

    foreach ($products as $product) {
        // Mendapatkan ID produk dan stock_limit
        $productId = $product->id;
        $stockLimit = $product->stock_limit;

        // Menghitung total stok untuk setiap ID produk
        $totalStock = Item::where('id', $productId)->sum('quantity')
            + GoodsIn::where('item_id', $productId)->sum('quantity')
            - GoodsOut::where('item_id', $productId)->sum('quantity')
            - GoodsBack::where('item_id', $productId)->sum('quantity')
            + StockOpname::where('item_id', $productId)->sum('quantity');

        // Memeriksa apakah total stok kurang dari stock_limit
        if ($totalStock <= $stockLimit) {
            $lowStockIds[] = $productId;
        }
    }

    // Mengembalikan jumlah notifikasi stok rendah
    return $lowStockIds;
}


function getGoodsInApproval()
{
    return GoodsIn::where('status', 0)->get();
}

function getStockOpnames()
{
    $stockOpnames = StockOpname::whereDate('date_so', Carbon::today())->orderBy('id', 'DESC')->get();

    $stockOpnames->transform(function ($item) {
        if ($item->stok_fisik > $item->stok_sistem) {
            $item->icon = '<span class="badge badge-success">Bertambah</span>'; // Stok fisik lebih besar
        } elseif ($item->stok_fisik < $item->stok_sistem) {
            $item->icon = '<span class="badge badge-danger">Berkurang</span>'; // Stok sistem lebih besar
        } else {
            $item->icon = '<span class="badge badge-primary">Sesuai</span>'; // Stok sama
        }
        return $item;
    });

    return $stockOpnames;
}
function hello_word()
{
    return "Hello Word";
}
