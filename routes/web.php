<?php

use App\Http\Controllers\TerminalController;
use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

Route::get('/setup', [SetupController::class, 'index'])->name('setup.index');
Route::post('/setup/run', [SetupController::class, 'run'])->name('setup.run');

Route::get('/', [TerminalController::class, 'dashboard'])->name('dashboard');
Route::get('/markets', [TerminalController::class, 'markets'])->name('markets.index');
Route::get('/markets/{market}', [TerminalController::class, 'market'])->name('markets.show');
Route::get('/opportunities', [TerminalController::class, 'opportunities'])->name('opportunities.index');
Route::get('/portfolio', [TerminalController::class, 'portfolio'])->name('portfolio.index');
Route::get('/history', [TerminalController::class, 'history'])->name('history.index');
Route::get('/settings', [TerminalController::class, 'settings'])->name('settings.index');
Route::post('/settings', [TerminalController::class, 'updateSettings'])->name('settings.update');
Route::post('/settings/reset', [TerminalController::class, 'resetPortfolio'])->name('settings.reset');
