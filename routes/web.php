<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\QRScannerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Protected Routes (Only accessible when logged in)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('users', UserController::class);
    Route::resource('user-roles', UserRoleController::class);

    Route::get('/members/{member}/e-id', [MemberController::class, 'showEid'])->name('members.eid');
    Route::resource('members', MemberController::class)->except(['create', 'show', 'edit']);

    Route::resource('teams', TeamController::class)->except(['create', 'show', 'edit']);
    Route::resource('events', EventController::class)->except(['create', 'show', 'edit']);
    
    Route::get('/scanner', [QRScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner/process', [QRScannerController::class, 'process'])->name('scanner.process');

    Route::resource('attendances', AttendanceController::class)->only(['index', 'store', 'destroy']);

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
});