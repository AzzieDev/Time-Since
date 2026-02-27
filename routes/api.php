<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TrackerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('trackers')->group(function () {
    Route::get('/', [TrackerController::class, 'index']);
    Route::post('/{tracker}/reset', [TrackerController::class, 'reset']);
    Route::post('/{tracker}/undo', [TrackerController::class, 'undo']);
});
