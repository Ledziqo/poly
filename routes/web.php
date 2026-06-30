<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

Route::get('/setup', [SetupController::class, 'index'])->name('setup.index');
Route::post('/setup/run', [SetupController::class, 'run'])->name('setup.run');

Route::view('/', 'landing')->name('landing');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/login-health', [AuthController::class, 'health'])->name('login.health');
Route::get('/app-health', [TerminalController::class, 'health'])->name('app.health');
Route::post('/login', [AuthController::class, 'storeLogin'])->name('login.store');
Route::get('/signup', [AuthController::class, 'signup'])->name('signup');
Route::post('/signup', [AuthController::class, 'storeSignup'])->name('signup.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/subscription-required', [AuthController::class, 'subscriptionRequired'])->middleware('auth')->name('subscription.required');

Route::middleware('tool.access')->prefix('app')->group(function () {
    Route::get('/', [TerminalController::class, 'dashboard'])->name('dashboard');
    Route::get('/markets', [TerminalController::class, 'markets'])->name('markets.index');
    Route::get('/markets/{market}', [TerminalController::class, 'market'])->name('markets.show');
    Route::get('/opportunities', [TerminalController::class, 'opportunities'])->name('opportunities.index');
    Route::get('/portfolio', [TerminalController::class, 'portfolio'])->name('portfolio.index');
    Route::get('/history', [TerminalController::class, 'history'])->name('history.index');
    Route::get('/settings', [TerminalController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [TerminalController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/reset', [TerminalController::class, 'resetPortfolio'])->name('settings.reset');
});
