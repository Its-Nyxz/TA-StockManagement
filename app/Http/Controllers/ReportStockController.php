<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\GoodsIn;
use App\Models\GoodsOut;
use App\Models\Supplier;
use App\Models\GoodsBack;
use Illuminate\View\View;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;

class ReportStockController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::all();
        return view('admin.master.laporan.stok', compact('suppliers'));
    }

    public function list(Request $request): JsonResponse
    {
        $data = Item::with(['goodsOuts', 'goodsIns', 'goodsBacks', 'stockOpnames']);

        if (!empty($request->start_date) || !empty($request->end_date)) {
            $dateFilters = [
                'goodsOuts' => 'date_out',
                'goodsIns' => 'date_received',
                'goodsBacks' => 'date_backs',
                'stockOpnames' => 'date_so',
            ];

            foreach ($dateFilters as $relation => $dateColumn) {
                $data->with([$relation => function ($query) use ($request, $dateColumn) {
                    if (!empty($request->start_date) && !empty($request->end_date)) {
                        $query->whereBetween($dateColumn, [$request->start_date, $request->end_date]);
                    } elseif (!empty($request->start_date)) {
                        $query->where($dateColumn, '>=', $request->start_date);
                    } elseif (!empty($request->end_date)) {
                        $query->where($dateColumn, '<=', $request->end_date);
                    }
                }]);
            }
        }

        if (!empty($request->supplier)) {
            $data->where('supplier_id', $request->supplier);
        }

        $data->latest()->get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('jumlah_masuk', function ($item) {
                    $totalQuantity = $item->goodsIns->sum('quantity');
                    $data = Item::with("unit")->find($item->id);
                    return $totalQuantity . "/" . $data->unit->name;
                })
                ->addColumn("jumlah_keluar", function ($item) {
                    $totalQuantity = $item->goodsOuts->sum('quantity');
                    $data = Item::with("unit")->find($item->id);
                    return $totalQuantity . "/" . $data->unit->name;
                })
                ->addColumn("jumlah_retur", function ($item) {
                    $totalQuantity = $item->goodsBacks->sum('quantity');
                    $data = Item::with("unit")->find($item->id);
                    return $totalQuantity . "/" . $data->unit->name;
                })
                ->addColumn("jumlah_selisih", function ($item) {
                    $totalQuantity = $item->stockOpnames->sum('quantity');
                    $data = Item::with("unit")->find($item->id);
                    // return $totalQuantity . "/" . $data->unit->name;
                    if ($totalQuantity < 0) {
                        $formatted = '<span style="color:red; font-weight:bold">-' . abs($totalQuantity) . ' / ' . $data->unit->name . '</span>';
                    } else if ($totalQuantity > 0) {
                        $formatted = '<span style="color:#44d744; font-weight:bold">+' . $totalQuantity . ' / ' . $data->unit->name . '</span>';
                    } else {
                        $formatted = '<span>' . $totalQuantity . ' / ' . $data->unit->name . '</span>';
                    }

                    return $formatted;
                })
                ->addColumn("kode_barang", function ($item) {
                    return $item->code;
                })
                ->addColumn("stok_awal", function ($item) {
                    $data = Item::with("unit")->find($item->id);
                    return $item->quantity . "/" . $data->unit->name;
                })
                ->addColumn("nama_barang", function ($item) {
                    return $item->name;
                })
                ->addColumn("pemasok", function ($item) {
                    return $item->supplier->name;
                })
                ->addColumn("brand", function ($item) {
                    return $item->brand->name;
                })
                ->addColumn("total", function ($item) {
                    $totalQuantityIn = $item->goodsIns->sum('quantity');
                    $totalQuantityOut = $item->goodsOuts->sum('quantity');
                    $totalQuantityRetur = $item->goodsBacks->sum('quantity');
                    $totalQuantitySO = $item->stockOpnames->sum('quantity');

                    // Hitung total stok
                    $count = ($item->quantity + $totalQuantityIn - $totalQuantityOut - $totalQuantityRetur) + $totalQuantitySO;
                    $count = max(0, $count); // Pastikan stok tidak negatif

                    // Ambil data unit dan stock_limit
                    $data = Item::with("unit")->find($item->id);
                    $unitName = $data->unit->name ?? '';
                    $stockLimit = $data->stock_limit ?? 10; // Default jika stock_limit null

                    // Buat hasil stok dengan unit
                    $result = number_format($count, 2) . " / " . $unitName; // Format dengan 2 desimal

                    // Tentukan status berdasarkan stok
                    if ($count <= 0) {
                        return "<span class='text-red font-weight-bold'>" . $result . "</span> " .
                            "<span class='badge badge-danger'>" . __("Stock Empty") . "</span>";
                    } elseif ($count <= $stockLimit) {
                        return "<span class='text-red font-weight-bold'>" . $result . "</span> " .
                            "<span class='badge badge-warning'>" . __("Stock Running Low") . "</span>";
                    } else {
                        return "<span class='text-success font-weight-bold'>" . $result . "</span>";
                    }
                })
                ->rawColumns(['total', 'jumlah_selisih'])
                ->make(true);
        }
    }

    public function grafik(Request $request): JsonResponse
    {
        if ($request->has('month') && !empty($request->month)) {
            $month = $request->month;
            $currentMonth = preg_split("/[-\s]/", $month)[1];
            $currentYear = preg_split("/[-\s]/", $month)[0];
        } else {
            $currentMonth = date('m');
            $currentYear = date('Y');
        }
        $goodsInThisMonth = GoodsIn::whereMonth('date_received', $currentMonth)
            ->whereYear('date_received', $currentYear)->sum('quantity');
        $goodsOutThisMonth = GoodsOut::whereMonth('date_out', $currentMonth)
            ->whereYear('date_out', $currentYear)->sum('quantity');
        $goodsBackThisMonth = GoodsBack::whereMonth('date_backs', $currentMonth)
            ->whereYear('date_backs', $currentYear)->sum('quantity');
        $goodsSoThisMonth = StockOpname::whereMonth('date_so', $currentMonth)
            ->whereYear('date_so', $currentYear)->sum('quantity');
        $totalStockThisMonth = max(0, $goodsInThisMonth - $goodsOutThisMonth - $goodsBackThisMonth + $goodsSoThisMonth);
        return response()->json([
            'month' => $currentYear . '-' . $currentMonth,
            'goods_in_this_month' => $goodsInThisMonth,
            'goods_out_this_month' => $goodsOutThisMonth,
            'goods_back_this_month' => $goodsBackThisMonth,
            'goods_so_this_month' => $goodsSoThisMonth,
            'total_stock_this_month' => $totalStockThisMonth,
        ]);
    }

    public function pietoday(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today());

        $today = Carbon::parse($date);

        $goodsInToday = GoodsIn::whereDate('date_received', $today)->sum('quantity') ?? 0;
        $goodsOutToday = GoodsOut::whereDate('date_out', $today)->sum('quantity') ?? 0;
        $goodsBackToday = GoodsBack::whereDate('date_backs', $today)->sum('quantity') ?? 0;
        $goodsSoToday = StockOpname::whereDate('date_so', $today)->sum('quantity') ?? 0;
        $totalStockToday = max(0, $goodsInToday - $goodsOutToday - $goodsBackToday + $goodsSoToday);

        return response()->json([
            'goods_in_today' => $goodsInToday,
            'goods_out_today' => $goodsOutToday,
            'goods_back_today' => $goodsBackToday,
            'goods_so_today' => $goodsSoToday,
            'goods_total_today' => $totalStockToday,
        ]);
    }

    public function getDetail(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $id = $request->id;

        $item = Item::with([
            'goodsIns:quantity,item_id',
            'goodsOuts:quantity,item_id',
            'goodsBacks:quantity,item_id',
            'stockOpnames:quantity,item_id',
            'conversions.fromUnit:id,name',
            'conversions.toUnit:id,name',
            'unit:id,name'
        ])->find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $satuan = $item->unit->name ?? '';
        $stokAwal = $item->quantity;
        $jumlahMasuk = $item->goodsIns->sum('quantity');
        $jumlahKeluar = $item->goodsOuts->sum('quantity');
        $jumlahRetur = $item->goodsBacks->sum('quantity');
        $jumlahSelisih = $item->stockOpnames->sum('quantity');

        $totalQuantity = ($stokAwal + $jumlahMasuk - $jumlahKeluar - $jumlahRetur) + $jumlahSelisih;

        // Format semua angka dengan 2 desimal
        $formattedStokAwal = number_format($stokAwal, 2);
        $formattedJumlahMasuk = number_format($jumlahMasuk, 2);
        $formattedJumlahKeluar = number_format($jumlahKeluar, 2);
        $formattedJumlahRetur = number_format($jumlahRetur, 2);
        $formattedJumlahSelisih = number_format($jumlahSelisih, 2);
        $formattedTotalQuantity = number_format($totalQuantity, 2);

        $unitConversions = $item->conversions->map(function ($conversion) {
            return [
                'from_unit' => $conversion->fromUnit->name,
                'to_unit' => $conversion->toUnit->name,
                'factor' => $conversion->conversion_factor,
            ];
        });

        return response()->json([
            'kode_barang' => $item->code,
            'stok_awal' => $formattedStokAwal,
            'stok_awal_unit' => $satuan,
            'jumlah_masuk' => $formattedJumlahMasuk,
            'jumlah_keluar' => $formattedJumlahKeluar,
            'jumlah_retur' => $formattedJumlahRetur,
            'jumlah_selisih' => $formattedJumlahSelisih,
            'total_stock' => $formattedTotalQuantity,
            'total_stock_unit' => $satuan,
            'units' => $unitConversions,
        ]);
    }
}
