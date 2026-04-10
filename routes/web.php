<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MachinePublicController;
use App\Http\Controllers\RepairTicketController;
use App\Http\Controllers\QrScanController;
use App\Http\Controllers\MachineCsvImportController;
use App\Http\Controllers\MachineMovementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EnvironmentReportController;

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
    Route::get('/notifications/{id}/open', [NotificationController::class, 'open'])->name('notifications.open');
    Route::get('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read_all');

    // Common: Scan QR & View Machine
    Route::get('/scan', [QrScanController::class, 'index']);
    Route::get('/m/{ma_thiet_bi}', [MachinePublicController::class, 'show']);

    Route::middleware(['role_or_permission:admin|warehouse|contractor|repairs.contractor'])->group(function () {
        Route::get('/repairs/contractor/export', [RepairTicketController::class, 'exportContractor']);
        Route::get('/repairs/contractor', [RepairTicketController::class, 'contractorIndex']);
    });

    // REPAIR GROUP: Admin, Warehouse, Repair Tech, Contractor, Team Leader
    Route::middleware(['role_or_permission:admin|warehouse|repair_tech|contractor|team_leader|repairs.manage'])->group(function () {
        Route::get('/repair-requests', [RepairTicketController::class, 'requestsIndex']);
        Route::get('/repairs/create', [RepairTicketController::class, 'create']);
        Route::post('/repairs', [RepairTicketController::class, 'store']);
        Route::get('/repairs/{repair}/edit', [RepairTicketController::class, 'edit']);
        Route::put('/repairs/{repair}', [RepairTicketController::class, 'update']);
        Route::post('/repairs/{repair}/accept', [RepairTicketController::class, 'accept'])->name('repairs.accept');
        Route::delete('/repairs/{repair}', [RepairTicketController::class, 'destroy'])->whereNumber('repair');
    });

    // REPAIR READ-ONLY + EXPORT: also accessible by Audit and 7S
    Route::middleware(['role_or_permission:admin|warehouse|repair_tech|contractor|team_leader|audit|7s|repairs.view'])->group(function () {
        Route::get('/repairs/export', [RepairTicketController::class, 'export']);
        Route::get('/repairs', [RepairTicketController::class, 'index']);
        Route::get('/repairs/{repair}', [RepairTicketController::class, 'show'])->whereNumber('repair');
    });


    // MOVEMENT GROUP: Admin, Warehouse, Team Leader
    Route::middleware(['role_or_permission:admin|warehouse|team_leader|machines.move'])->group(function () {
        Route::post('/machines/{id}/move', [MachineMovementController::class, 'update']);
    });

    // MOVEMENT READ-ONLY + MOVE FORM + EXPORT: also accessible by Audit and 7S
    Route::middleware(['role_or_permission:admin|warehouse|team_leader|audit|7s|movement_history.view'])->group(function () {
        Route::get('/machines/{id}/move', [MachineMovementController::class, 'edit']);
        Route::get('/movement-history', [MachineMovementController::class, 'index']);
        Route::get('/movement-history/export', [MachineMovementController::class, 'export']);
    });


    // ENVIRONMENT REPORT GROUP: Admin, Warehouse
    Route::middleware(['role_or_permission:admin|warehouse|environment_reports.access'])->group(function () {
        Route::get('/environment-reports', [EnvironmentReportController::class, 'index'])->name('environment-reports.index');
        Route::get('/environment-reports/create', [EnvironmentReportController::class, 'create'])->name('environment-reports.create');
        Route::post('/environment-reports', [EnvironmentReportController::class, 'store'])->name('environment-reports.store');
        Route::get('/environment-reports/{environmentReport}/edit', [EnvironmentReportController::class, 'edit'])->name('environment-reports.edit');
        Route::put('/environment-reports/{environmentReport}', [EnvironmentReportController::class, 'update'])->name('environment-reports.update');
        Route::get('/environment-reports/{environmentReport}/print', [EnvironmentReportController::class, 'print'])->name('environment-reports.print');
        Route::get('/environment-reports/{environmentReport}', [EnvironmentReportController::class, 'show'])->name('environment-reports.show');
    });

    // AUDIT GROUP: Admin, Audit
    Route::middleware(['role_or_permission:admin|audit|audits.access'])->group(function () {
        Route::get('/audits/export', [\App\Http\Controllers\AuditController::class, 'export'])->name('audits.export');
        Route::get('/audits/{audit}/export', [\App\Http\Controllers\AuditController::class, 'exportDetail'])->name('audits.export_detail');
        Route::post('/audits/{audit}/improvements', [\App\Http\Controllers\AuditController::class, 'updateImprovements'])->name('audits.improvements');
        Route::post('/audits/{audit}/confirm-completion', [\App\Http\Controllers\AuditController::class, 'confirmCompletion'])->name('audits.confirm_completion');
        Route::post('/audits/{audit}/reject-completion/{result}', [\App\Http\Controllers\AuditController::class, 'rejectCompletion'])->name('audits.reject_completion');
        Route::post('/audits/{audit}/agreements', [\App\Http\Controllers\AuditController::class, 'submitAgreements'])->name('audits.agreements');
        Route::post('/audits/{audit}/review-rejections', [\App\Http\Controllers\AuditController::class, 'reviewRejections'])->name('audits.review_rejections');
        Route::post('/audits/{audit}/reviews', [\App\Http\Controllers\AuditController::class, 'storeReviews'])->name('audits.reviews');
        Route::get('/audits', [\App\Http\Controllers\AuditController::class, 'index'])->name('audits.index');
        Route::get('/audits/create', [\App\Http\Controllers\AuditController::class, 'create'])->name('audits.create');
        Route::post('/audits', [\App\Http\Controllers\AuditController::class, 'store'])->name('audits.store');
        Route::get('/audits/{audit}/edit', [\App\Http\Controllers\AuditController::class, 'editAudit'])->name('audits.edit');
        Route::post('/audits/{audit}/update-results', [\App\Http\Controllers\AuditController::class, 'updateAudit'])->name('audits.update');
        Route::get('/audits/{audit}', [\App\Http\Controllers\AuditController::class, 'show'])->name('audits.show');
    });

    Route::middleware(['role_or_permission:admin|7s|seven_s.access'])->group(function () {
        Route::get('/seven-s/export', [\App\Http\Controllers\SevenSController::class, 'export'])->name('seven-s.export');
        Route::get('/seven-s', [\App\Http\Controllers\SevenSController::class, 'index'])->name('seven-s.index');
        Route::get('/seven-s/create', [\App\Http\Controllers\SevenSController::class, 'create'])->name('seven-s.create');
        Route::post('/seven-s', [\App\Http\Controllers\SevenSController::class, 'store'])->name('seven-s.store');
        Route::get('/seven-s/{id}/edit', [\App\Http\Controllers\SevenSController::class, 'edit'])->name('seven-s.edit')->whereNumber('id');
        Route::put('/seven-s/{id}', [\App\Http\Controllers\SevenSController::class, 'update'])->name('seven-s.update')->whereNumber('id');
        Route::delete('/seven-s/{id}', [\App\Http\Controllers\SevenSController::class, 'destroy'])->name('seven-s.destroy')->whereNumber('id');
        Route::post('/seven-s/{result}/improve', [\App\Http\Controllers\SevenSController::class, 'storeImprovement'])->name('seven-s.improve');
        Route::post('/seven-s/{record}/improvements', [\App\Http\Controllers\SevenSController::class, 'storeImprovements'])->name('seven-s.improvements');
        Route::post('/seven-s/{record}/submit-agreements', [\App\Http\Controllers\SevenSController::class, 'submitAgreements'])->name('seven-s.submit_agreements');
        Route::post('/seven-s/{record}/review-rejections', [\App\Http\Controllers\SevenSController::class, 'reviewRejections'])->name('seven-s.review_rejections');
        Route::post('/seven-s/{record}/review-improvements', [\App\Http\Controllers\SevenSController::class, 'reviewImprovements'])->name('seven-s.review_improvements');
        Route::get('/seven-s/{id}/export', [\App\Http\Controllers\SevenSController::class, 'exportDetail'])->name('seven-s.export_detail')->whereNumber('id');
        Route::get('/seven-s/{id}', [\App\Http\Controllers\SevenSController::class, 'show'])->name('seven-s.show')->whereNumber('id');
    });


    // WAREHOUSE EXTRA: Import CSV (Admin + Warehouse)
    Route::middleware(['role_or_permission:admin|warehouse|machines.import_csv'])->group(function () {
        Route::get('/machines/import-csv', [MachineCsvImportController::class, 'form']);
        Route::post('/machines/import-csv', [MachineCsvImportController::class, 'import']);
    });

    Route::middleware(['role_or_permission:admin|warehouse|machines.manage'])->group(function () {
        // Print QR
        Route::get('/machines/department/{department}/print-qr', [App\Http\Controllers\MachineController::class, 'printDepartmentQr'])->name('machines.print_department_qr');
        Route::get('/machines/{machine}/print-qr', [App\Http\Controllers\MachineController::class, 'printQr'])->name('machines.print_qr');

        // Machine Management List
        Route::resource('machines', App\Http\Controllers\MachineController::class)->except(['show']);
    });

    Route::middleware(['role_or_permission:admin|warehouse|users.view'])->group(function () {
        // Warehouse can VIEW users, but not create (restricted in Controller/Policy ideally, but strictly restricted via routes here)
        Route::get('/users', [UserController::class, 'index']);
    });

    // USER MANAGEMENT: Admin Only (Create, Edit, Delete)
    Route::middleware(['role_or_permission:admin|users.manage'])->group(function () {
        // Repair Ticket Complete Editing
        Route::get('/repairs/{repair}/edit-completed', [RepairTicketController::class, 'editCompleted'])->name('repairs.edit_completed');
        Route::put('/repairs/{repair}/update-completed', [RepairTicketController::class, 'updateCompleted'])->name('repairs.update_completed');

        Route::get('/users/create', [UserController::class, 'create']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}/edit', [UserController::class, 'edit']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle_active');
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::delete('/audits/{audit}', [\App\Http\Controllers\AuditController::class, 'destroy'])->name('audits.destroy');
    });
});

require __DIR__ . '/auth.php';
