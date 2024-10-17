<?php

// namespace App\Helpers;

use App\Models\GoodsIn;
use App\Models\GoodsOut;
use App\Models\GoodsBack;
use App\Models\Item;



class Notification
{
    public static function getLowStockNotifGet()
    {
        // Mendefinisikan array untuk menyimpan data stok rendah
        $lowStockData = [];
        
        // Mengambil semua ID produk yang unik
        // $productIds = GoodsIn::distinct()->pluck('item_id')->toArray();
        $productIds = Item::pluck('id')->toArray();
    
        // Mengambil semua nama item dalam satu query untuk efisiensi
        $items = Item::whereIn('id', $productIds)->get()->keyBy('id');
    
        foreach ($productIds as $productId) {
            // Menghitung total stok untuk setiap ID produk
            $totalStock = Item::where('id',$productId)->sum('quantity') + GoodsIn::where('item_id', $productId)->sum('quantity')
                - GoodsOut::where('item_id', $productId)->sum('quantity')
                - GoodsBack::where('item_id', $productId)->sum('quantity');
            
            // Mendapatkan nama item dari koleksi
            $item_name = $items->get($productId);
            
            // Memeriksa apakah total stok kurang dari 10
            if ($totalStock <= 10) {
                $lowStockData[] = (object) [
                    'item_id' => $productId,
                    'total_stock' => max(0,$totalStock),
                    'item_name' => $item_name ? $item_name->name : 'Unknown',
                    'item_code' => $item_name ? $item_name->code : ' ',
                    'merk' => $item_name ? $item_name->brand->name : '',
                    // 'created_at' => GoodsIn::where('item_id', $productId)->first()? GoodsIn::where('item_id', $productId)->first()->created_at : null,
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

        // Mengambil semua ID produk yang unik
        // $productIds = GoodsIn::distinct()->pluck('item_id')->toArray();
        $productIds = Item::pluck('id')->toArray();
        
        foreach ($productIds as $productId) {
            // Menghitung total stok untuk setiap ID produk
            $totalStock = Item::where('id',$productId)->sum('quantity') + GoodsIn::where('item_id', $productId)->sum('quantity')
                - GoodsOut::where('item_id', $productId)->sum('quantity')
                - GoodsBack::where('item_id', $productId)->sum('quantity');
            
            // Memeriksa apakah total stok kurang dari 10
            if ($totalStock <= 10 || $totalStock == 0) {
                $lowStockIds[] = $productId;
            }
        }

        // Mengembalikan jumlah notifikasi stok rendah
        return $lowStockIds;
    }

function getGoodsInApproval()
{
    return GoodsIn::where('status',0)->get();
}
function hello_word()
{
    return "Hello Word";
}
