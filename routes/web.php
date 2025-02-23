<?php

use App\Models\StockOpname;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AdminatorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebSettingController;
use App\Http\Controllers\ReportStockController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\ReportGoodsInController;
use App\Http\Controllers\TransactionInController;
use App\Http\Controllers\ReportGoodsOutController;
use App\Http\Controllers\TransactionOutController;
use App\Http\Controllers\ReportFinancialController;
use App\Http\Controllers\ReportGoodsBackController;
use App\Http\Controllers\TentangController;
use App\Http\Controllers\TransactionBackController;

Route::middleware(["localization"])->group(function () {
    Route::get('/', [LoginController::class, 'index'])->name('login');
    Route::post('/', [LoginController::class, 'auth'])->name('login.auth');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


Route::middleware(['auth', "localization"])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // barang
    Route::controller(ItemController::class)->prefix("barang")->group(function () {
        Route::get('/', 'index')->name('barang.goods');
        Route::post('/kode', 'detailByCode')->name('barang.code');
        Route::get('/daftar-barang', 'list')->name('barang.list');
        Route::post('/info', 'detail')->name('barang.detail');
        Route::middleware(['employee.middleware'])->group(function () {
            Route::post('/simpan', 'save')->name('barang.save');
            Route::post('/ubah', 'update')->name('barang.update');
            Route::delete('/hapus', 'delete')->name('barang.delete');
            Route::post('/import', 'import')->name('barang.import');
            Route::get('/template', 'template')->name('barang.template');
        });
    });

    // jenis barang
    Route::controller(CategoryController::class)->prefix("barang/jenis")->group(function () {
        Route::get('/', 'index')->name('barang.jenis');
        Route::get('/daftar', 'list')->name('barang.jenis.list');
        Route::middleware(['employee.middleware'])->group(function () {
            Route::post('/simpan', 'save')->name('barang.jenis.save');
            Route::post('/info', 'detail')->name('barang.jenis.detail');
            Route::put('/ubah', 'update')->name('barang.jenis.update');
            Route::delete('/hapus', 'delete')->name('barang.jenis.delete');
            Route::post('/import', 'import')->name('barang.jenis.import');
            Route::get('/template', 'template')->name('barang.jenis.template');
            Route::post('/store',  'store')->name('barang.jenis.store');
        });
    });

    // satuan barang
    Route::controller(UnitController::class)->prefix('/barang/satuan')->group(function () {
        Route::get('/', 'index')->name('barang.satuan');
        Route::get('/daftar', 'list')->name('barang.satuan.list');
        Route::middleware(['employee.middleware'])->group(function () {
            Route::post('/simpan', 'save')->name('barang.satuan.save');
            Route::post('/info', 'detail')->name('barang.satuan.detail');
            Route::put('/ubah', 'update')->name('barang.satuan.update');
            Route::delete('/hapus', 'delete')->name('barang.satuan.delete');
            Route::post('/import', 'import')->name('barang.satuan.import');
            Route::get('/template', 'template')->name('barang.satuan.template');
            Route::post('/store', 'store')->name('barang.satuan.store');
        });
    });

    // merk barang
    Route::controller(BrandController::class)->prefix("/barang/merk")->group(function () {
        Route::get('/', 'index')->name('barang.merk');
        Route::get('/daftar', 'list')->name('barang.merk.list');
        Route::middleware(['employee.middleware'])->group(function () {
            Route::post('/simpan', 'save')->name('barang.merk.save');
            Route::post('/info', 'detail')->name('barang.merk.detail');
            Route::put('/ubah', 'update')->name('barang.merk.update');
            Route::delete('/hapus', 'delete')->name('barang.merk.delete');
            Route::post('/import', 'import')->name('barang.merk.import');
            Route::get('/template', 'template')->name('barang.merk.template');
            Route::post('/store', 'store')->name('barang.merk.store');
        });
    });


    // customer (izin untuk staff hanya read)
    Route::controller(CustomerController::class)->prefix('/customer')->middleware(['false.detect'])->group(function () {
        Route::get('/', 'index')->name('customer');
        Route::get('/daftar', 'list')->name('customer.list');
        Route::middleware(['employee.middleware'])->group(function () {
            Route::post('/simpan', 'save')->name('customer.save');
            Route::post('/info', 'detail')->name('customer.detail');
            Route::put('/ubah', 'update')->name('customer.update');
            Route::delete('/hapus', 'delete')->name('customer.delete');
        });
    });


    // supplier (izin untuk staff hanya read)
    Route::controller(SupplierController::class)->prefix('/supplier')->group(function () {
        Route::get('/', 'index')->name('supplier');
        Route::get('/daftar', 'list')->name('supplier.list');
        Route::middleware(['employee.middleware'])->group(function () {
            Route::post('/simpan', 'save')->name('supplier.save');
            Route::post('/info', 'detail')->name('supplier.detail');
            Route::put('/ubah', 'update')->name('supplier.update');
            Route::delete('/hapus', 'delete')->name('supplier.delete');
            Route::post('/import', 'import')->name('suppliers.import');
            Route::get('/template', 'template')->name('suppliers.template');
            Route::post('/store', 'store')->name('suppliers.store');
        });
    });

    // Transaksi  masuk
    Route::controller(TransactionInController::class)->prefix('/transaksi/masuk')->group(function () {
        Route::get('/', 'index')->name('transaksi.masuk');
        Route::get('/list', 'list')->name('transaksi.masuk.list');
        Route::post('/save', 'save')->name('transaksi.masuk.save');
        Route::post('/detail', 'detail')->name('transaksi.masuk.detail');
        Route::put('/update', 'update')->name('transaksi.masuk.update');
        Route::delete('/delete', 'delete')->name('transaksi.masuk.delete');
        Route::get('/barang/list/in', 'listIn')->name('barang.list.in');
        Route::middleware(['employee.middleware'])->group(function () {
            Route::get('/modal', 'modal')->name('transaksi.masuk.approval');
            Route::post('/approve/{id}', 'approve')->name('transaksi.masuk.approve');
            Route::post('/cancel/{id}', 'cancel')->name('transaksi.masuk.cancel');
        });
    });



    // Transaksi keluar
    Route::controller(TransactionOutController::class)->prefix('/transaksi/keluar')->group(function () {
        Route::get('/', 'index')->name('transaksi.keluar');
        Route::get('/list', 'list')->name('transaksi.keluar.list');
        Route::get('/barang/list/out', 'listOut')->name('barang.list.out');
        Route::post('/simpan', 'save')->name('transaksi.keluar.save');
        Route::post('/info', 'detail')->name('transaksi.keluar.detail');
        Route::put('/ubah', 'update')->name('transaksi.keluar.update');
        Route::delete('/hapus', 'delete')->name('transaksi.keluar.delete');
    });

    // Transaksi kembali
    Route::controller(TransactionBackController::class)->prefix('/transaksi/kembali')->middleware('employee.middleware')->group(function () {
        Route::get('/', 'index')->name('transaksi.kembali');
        Route::get('/list', 'list')->name('transaksi.kembali.list');
        Route::get('/barang/list/back', 'listBack')->name('barang.list.back');
        Route::post('/simpan', 'save')->name('transaksi.kembali.save');
        Route::post('/info', 'detail')->name('transaksi.kembali.detail');
        Route::put('/ubah', 'update')->name('transaksi.kembali.update');
        Route::delete('/hapus', 'delete')->name('transaksi.kembali.delete');
    });

    // laporan barang masuk
    Route::controller(ReportGoodsInController::class)->prefix('/laporan/masuk')->middleware('employee.middleware')->group(function () {
        Route::get('/', 'index')->name('laporan.masuk');
        Route::get('/list', 'list')->name('laporan.masuk.list');
    });

    // laporan barang keluar
    Route::controller(ReportGoodsOutController::class)->prefix('/laporan/keluar')->middleware('employee.middleware')->group(function () {
        Route::get('/', 'index')->name('laporan.keluar');
        Route::get('/list', 'list')->name('laporan.keluar.list');
    });

    // laporan barang kembali
    Route::controller(ReportGoodsBackController::class)->prefix('/laporan/kembali')->middleware('employee.middleware')->group(function () {
        Route::get('/', 'index')->name('laporan.kembali');
        Route::get('/list', 'list')->name('laporan.kembali.list');
    });

    // laporan stok opname barang
    Route::controller(StockOpnameController::class)->prefix('/laporan/so')->middleware('employee.middleware')->group(function () {
        Route::get('/', 'index')->name('laporan.so');
        Route::get('/list', 'list')->name('laporan.so.list');
        Route::post('/simpan', 'save')->name('laporan.so.save');
        Route::post('/info', 'detail')->name('laporan.so.detail');
        Route::put('/ubah', 'update')->name('laporan.so.update');
        Route::delete('/hapus', 'delete')->name('laporan.so.delete');
    });

    // laporan stok barang
    Route::controller(ReportStockController::class)->prefix('/laporan/stok')->group(function () {
        Route::get('/', 'index')->name('laporan.stok');
        Route::get('/list', 'list')->name('laporan.stok.list');
        Route::get('/grafik', 'grafik')->name('laporan.stok.grafik');
        Route::get('/pietoday', 'pietoday')->name('laporan.stok.pietoday');
        Route::get('/detail',  'getDetail')->name('laporan.stok.detail');
    });

    // laporan penghasilan
    Route::get('/report/income', [ReportFinancialController::class, 'income'])->name('laporan.pendapatan');

    // pengaturan pengguna
    Route::middleware(['employee.middleware'])->group(function () {
        Route::controller(EmployeeController::class)->prefix('/settings/employee')->group(function () {
            Route::get('/', 'index')->name('settings.employee');
            Route::get('/list', 'list')->name('settings.employee.list');
            Route::post('/save', 'save')->name('settings.employee.save');
            Route::post('/detail', 'detail')->name('settings.employee.detail');
            Route::put('/update', 'update')->name('settings.employee.update');
            Route::delete('/delete', 'delete')->name('settings.employee.delete');
        });
    });

    // Route::get('/pengaturan/web',[WebSettingController::class,'index'])->name('settings.web');
    // Route::get('/pengaturan/web/detail',[WebSettingController::class,'detail'])->name('settings.web.detail');
    // Route::post('/pengaturan/web/detail/role',[WebSettingController::class,'detailRole'])->name('settings.web.detail.role');
    // Route::put('/pengaturan/web/update',[WebSettingController::class,'update'])->name('settings.web.update');

    // pengaturan profile
    Route::get('/settings/profile', [ProfileController::class, 'index'])->name('settings.profile');
    Route::post('/settings/profile', [ProfileController::class, 'update'])->name('settings.profile.update');
    Route::get('/settings/tentang', [TentangController::class, 'index'])->name('settings.tentang');
    Route::post('/settings/tentang', [TentangController::class, 'update'])->name('settings.tentang.update');

    // logout
    Route::get('/logout', [LoginController::class, 'logout'])->name('login.delete');
});
