<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\QRScannerController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SettingController;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});


Route::middleware(['auth'])->group(function () {
    
    // Core Access
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==========================================
    // ADMINISTRATION MODULE
    // ==========================================
    Route::middleware('can:manage_users')->resource('users', UserController::class);
    Route::middleware('can:manage_roles')->resource('user-roles', UserRoleController::class);
    
    Route::middleware('can:manage_settings')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    });

    // ==========================================
    // MEMBERS DIRECTORY
    // ==========================================
    Route::middleware('can:view_members')->group(function () {
        Route::resource('members', MemberController::class)->only(['index']);
        Route::get('/members/{member}/e-id', [MemberController::class, 'showEid'])->name('members.eid');
    });
    
    Route::middleware('can:manage_members')->resource('members', MemberController::class)->only(['store', 'update', 'destroy']);

    // ==========================================
    // TEAMS MODULE
    // ==========================================
    Route::middleware('can:view_teams')->resource('teams', TeamController::class)->only(['index']);
    Route::middleware('can:manage_teams')->resource('teams', TeamController::class)->only(['store', 'update', 'destroy']);

    // ==========================================
    // EVENTS MODULE
    // ==========================================
    Route::middleware('can:view_events')->resource('events', EventController::class)->only(['index']);
    Route::middleware('can:manage_events')->resource('events', EventController::class)->only(['store', 'update', 'destroy']);
    
    // ==========================================
    // QR SCANNER
    // ==========================================
    Route::middleware('can:scan_qr')->group(function () {
        Route::get('/scanner', [QRScannerController::class, 'index'])->name('scanner.index');
        Route::post('/scanner/process', [QRScannerController::class, 'process'])->name('scanner.process');
    });

    // ==========================================
    // ATTENDANCE LOGS
    // ==========================================
    Route::middleware('can:view_attendances')->resource('attendances', AttendanceController::class)->only(['index']);
    Route::middleware('can:manage_attendances')->resource('attendances', AttendanceController::class)->only(['store', 'destroy']);

});