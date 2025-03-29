<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\WebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('post-trade',[\App\Http\Controllers\CommunicatorController::class,'postTrade']);

Route::get('export',[\App\Http\Controllers\CommunicatorController::class,'exportMasterTrades']);

Route::post('orders',[\App\Http\Controllers\CommunicatorController::class,'recordOrder']);

Route::post('positions',[\App\Http\Controllers\CommunicatorController::class,'recordPosition']);

Route::post('ping',[\App\Http\Controllers\CommunicatorController::class,'ping']);

Route::post('close',[\App\Http\Controllers\CommunicatorController::class,'closeMasterStatus']);

Route::get('trader',[\App\Http\Controllers\CommunicatorController::class,'createTrader']);

Route::post('getpositions',[\App\Http\Controllers\CommunicatorController::class,'getRecentPositions']);

Route::post('pingmaster',[\App\Http\Controllers\CommunicatorController::class,'checkMasterStatus']);

Route::post('zapier/webhook', [WebhookController::class, 'handle']);
