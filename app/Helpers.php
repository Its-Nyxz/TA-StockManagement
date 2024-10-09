<?php

// namespace App\Helpers;

use App\Models\GoodsIn;
use App\Models\GoodsOut;
use App\Models\GoodsBack;
use App\Models\Item;



class Helpers
{
    public static function getLowStockNotifGet()
    {
        // Mendefinisikan array untuk menyimpan data stok rendah
        $lowStockData = [];
        
        // Mengambil semua ID produk yang unik
        $productIds = GoodsIn::distinct()->pluck('item_id')->toArray();
    
        // Mengambil semua nama item dalam satu query untuk efisiensi
        $items = Item::whereIn('id', $productIds)->get()->keyBy('id');
    
        foreach ($productIds as $productId) {
            // Menghitung total stok untuk setiap ID produk
            $totalStock = GoodsIn::where('item_id', $productId)->sum('quantity')
                - GoodsOut::where('item_id', $productId)->sum('quantity')
                - GoodsBack::where('item_id', $productId)->sum('quantity');
            
            // Mendapatkan nama item dari koleksi
            $item_name = $items->get($productId);
    
            // Memeriksa apakah total stok kurang dari 5
            if ($totalStock <= 5) {
                $lowStockData[] = (object) [
                    'item_id' => $productId,
                    'total_stock' => $totalStock,
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
        $data = [];

        // Mengambil semua ID produk yang unik
        $productIds = GoodsIn::distinct()->pluck('item_id')->toArray();

        foreach ($productIds as $productId) {
            // Menghitung total stok untuk setiap ID produk
            $totalStock = GoodsIn::where('item_id', $productId)->sum('quantity')
                - GoodsOut::where('item_id', $productId)->sum('quantity')
                - GoodsBack::where('item_id', $productId)->sum('quantity');

            // Memeriksa apakah total stok kurang dari 5
            if ($totalStock < 5) {
                $lowStockIds[] = $productId;
                $data = [
                    'item_id' => $productId,
                    'total_stock' => $totalStock
                ];
            }
        }

        // Mengembalikan jumlah notifikasi stok rendah
        return $lowStockIds;
    }

function hello_word()
{
    return "Hello Word";
}
