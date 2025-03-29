<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('front');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/remove-master/{masterId}', [\App\Http\Controllers\CommunicatorController::class, 'removeMasterTrader'])->name('remove');
