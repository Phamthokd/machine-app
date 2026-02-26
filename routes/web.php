<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MachinePublicController;
use App\Http\Controllers\RepairTicketController;
use App\Http\Controllers\QrScanController;
use App\Http\Controllers\MachineCsvImportController;
use App\Http\Controllers\MachineMovementController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Role Definitions:
| 1. admin: All access
| 2. warehouse: All access EXCEPT User Creation/Delete
| 3. team_leader: Move Machine, View Move History
| 4. repair_tech: Repair Machine, View Repair History
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/lang/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('lang.switch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Common: Scan QR & View Machine
    Route::get('/scan', [QrScanController::class, 'index']);
    Route::get('/m/{ma_thiet_bi}', [MachinePublicController::class, 'show']);

    // REPAIR GROUP: Admin, Warehouse, Repair Tech, Contractor, Team Leader
    Route::middleware(['role:admin|warehouse|repair_tech|contractor|team_leader'])->group(function () {
        Route::get('/repairs/contractor/export', [RepairTicketController::class, 'exportContractor']);
        Route::get('/repairs/contractor', [RepairTicketController::class, 'contractorIndex']);
        Route::get('/repair-requests', [RepairTicketController::class, 'requestsIndex']);
        Route::get('/repairs', [RepairTicketController::class, 'index']);
        Route::get('/repairs/create', [RepairTicketController::class, 'create']);
        Route::post('/repairs', [RepairTicketController::class, 'store']);
        Route::get('/repairs/{repair}/edit', [RepairTicketController::class, 'edit']);
        Route::put('/repairs/{repair}', [RepairTicketController::class, 'update']);
        Route::get('/repairs/export', [RepairTicketController::class, 'export']);
        Route::get('/repairs/{repair}', [RepairTicketController::class, 'show'])->whereNumber('repair');
        Route::delete('/repairs/{repair}', [RepairTicketController::class, 'destroy'])->whereNumber('repair');
    });

    // MOVEMENT GROUP: Admin, Warehouse, Team Leader
    Route::middleware(['role:admin|warehouse|team_leader'])->group(function () {
        Route::get('/machines/{id}/move', [MachineMovementController::class, 'edit']);
        Route::post('/machines/{id}/move', [MachineMovementController::class, 'update']);
        Route::get('/movement-history', [MachineMovementController::class, 'index']);
        Route::get('/movement-history/export', [MachineMovementController::class, 'export']);
    });

    // AUDIT GROUP: Admin, Audit
    Route::middleware(['role:admin|audit'])->group(function () {
        Route::get('/audits/export', [\App\Http\Controllers\AuditController::class, 'export'])->name('audits.export');
        Route::get('/audits/{audit}/export', [\App\Http\Controllers\AuditController::class, 'exportDetail'])->name('audits.export_detail');
        Route::post('/audits/{audit}/improvements', [\App\Http\Controllers\AuditController::class, 'updateImprovements'])->name('audits.improvements');
        Route::get('/audits', [\App\Http\Controllers\AuditController::class, 'index'])->name('audits.index');
        Route::get('/audits/create', [\App\Http\Controllers\AuditController::class, 'create'])->name('audits.create');
        Route::post('/audits', [\App\Http\Controllers\AuditController::class, 'store'])->name('audits.store');
        Route::get('/audits/{audit}', [\App\Http\Controllers\AuditController::class, 'show'])->name('audits.show');
    });

    // WAREHOUSE EXTRA: Import CSV (Admin + Warehouse)
    Route::middleware(['role:admin|warehouse'])->group(function () {
        Route::get('/machines/import-csv', [MachineCsvImportController::class, 'form']);
        Route::post('/machines/import-csv', [MachineCsvImportController::class, 'import']);
        
        // Print QR
        Route::get('/machines/department/{department}/print-qr', [App\Http\Controllers\MachineController::class, 'printDepartmentQr'])->name('machines.print_department_qr');
        Route::get('/machines/{machine}/print-qr', [App\Http\Controllers\MachineController::class, 'printQr'])->name('machines.print_qr');
        
        // Machine Management List
        Route::resource('machines', App\Http\Controllers\MachineController::class)->except(['show']);

        // Warehouse can VIEW users, but not create (restricted in Controller/Policy ideally, but strictly restricted via routes here)
        Route::get('/users', [UserController::class, 'index']);
    });

    // USER MANAGEMENT: Admin Only (Create, Edit, Delete)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users/create', [UserController::class, 'create']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}/edit', [UserController::class, 'edit']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});

require __DIR__.'/auth.php';
