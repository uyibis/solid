<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('newfront');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::put('/home/toggle-status/{id}', [App\Http\Controllers\HomeController::class, 'toggleStatus'])->name('home.toggleStatus');
});

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index']);

Route::get('/remove-master/{masterId}', [\App\Http\Controllers\CommunicatorController::class, 'removeMasterTrader'])->name('remove');

Route::prefix('home')->middleware(['auth'])->group(function () {
    Route::prefix('master')->group(function (){
        Route::get('create',[\App\Http\Controllers\Master\MasterController::class,'create'])->name('master.create');
        Route::post('process-create',[\App\Http\Controllers\Master\MasterController::class,'process_create'])->name('master.store');
    });

    Route::prefix('customer')->group(function(){
        Route::get('view',[\App\Http\Controllers\Customer\CustomerController::class,'index'])->name('customers.view');
        Route::post('slave/{id}/toggle', [\App\Http\Controllers\Customer\CustomerController::class, 'toggleStatus'])->name('slave.toggle');
    });

});
