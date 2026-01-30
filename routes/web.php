<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MachinePublicController;
use App\Http\Controllers\RepairTicketController;
use App\Http\Controllers\QrScanController;
use App\Http\Controllers\MachineCsvImportController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/repairs/create', [RepairTicketController::class, 'create']);
    Route::post('/repairs', [RepairTicketController::class, 'store']);
});
Route::middleware(['auth'])->group(function () {
    Route::get('/repairs', [RepairTicketController::class, 'index']);
    Route::get('/repairs/create', [RepairTicketController::class, 'create']);
    Route::post('/repairs', [RepairTicketController::class, 'store']);
    Route::get('/repairs/{repair}', [RepairTicketController::class, 'show']);
});

Route::middleware(['auth'])->group(function () {

    // Ai cũng được xem trang máy sau khi login
    Route::get('/m/{ma_thiet_bi}', [MachinePublicController::class, 'show']);

    // Chỉ người sửa máy (hoặc admin) được tạo phiếu sửa
    Route::middleware(['role:admin|repair_tech'])->group(function () {
        Route::get('/repairs/create', [RepairTicketController::class, 'create']);
        Route::post('/repairs', [RepairTicketController::class, 'store']);
    });

    // Ai được xem danh sách? (admin + QC/QA + repair)
    Route::middleware(['role:admin|repair_tech|endline_qc|inline_qc_triumph|qa_supervisor_triumph'])->group(function () {
        Route::get('/repairs', [RepairTicketController::class, 'index']);
        Route::get('/repairs/{repair}', [RepairTicketController::class, 'show']);
    });
});
Route::middleware(['auth'])->group(function () {
    Route::get('/scan', [QrScanController::class, 'index']);
});
Route::middleware(['auth'])->group(function () {
    Route::get('/machines/import-csv', [MachineCsvImportController::class, 'form']);
    Route::post('/machines/import-csv', [MachineCsvImportController::class, 'import']);
});




Route::get('/m/{ma_thiet_bi}', [MachinePublicController::class, 'show']);

require __DIR__.'/auth.php';
