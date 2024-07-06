<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FarmManager\FarmProgressController;
use App\Http\Controllers\TelephoneFarmer\TelephoneFarmerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('auth/register', [AuthController::class, 'createUser']);
Route::post('auth/login', [AuthController::class, 'login']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('user-auth', [AuthController::class, 'auth']);
    Route::post('tf/farm', [TelephoneFarmerController::class, 'createFarm']);
    Route::get('tf/farm', [TelephoneFarmerController::class, 'viewFarm']);
    Route::get('tf/farm/{id}', [TelephoneFarmerController::class, 'getFarm']);
    Route::post('tf/farm/manager', [TelephoneFarmerController::class, 'assignManager']);


    Route::post('tf/manager', [TelephoneFarmerController::class, 'createManager']);
    Route::get('tf/manager', [TelephoneFarmerController::class, 'viewManagers']);

    Route::post('fm/farm/progress', [FarmProgressController::class, 'AddFarmProgress']);
    Route::get('fm/farm/{id}', [FarmProgressController::class, 'viewFarmProgress']);
    Route::get('fm/farm', [FarmProgressController::class, 'viewFarm']);

    Route::post('chat/{id}', [ChatController::class, 'storeChat']);
    Route::get('chat', [ChatController::class, 'getChats']);

});
