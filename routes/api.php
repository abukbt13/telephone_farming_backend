<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\FarmManager\FarmProgressController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\Media\CommentController;
use App\Http\Controllers\Media\LikeController;
use App\Http\Controllers\Media\PostsController;
use App\Http\Controllers\NewPostController;
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
    Route::post('auth/user/{id}', [AuthController::class, 'UpdateProfile']);
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
    Route::get('chats/chat/{id}', [ChatController::class, 'getChat']);
    Route::get('chat', [ChatController::class, 'myChats']);
    Route::get('chat/users', [ChatController::class, 'getUsers']);


    Route::get('v1/posts', [NewPostController::class, 'listPosts']);
    Route::post('v1/post', [NewPostController::class, 'CreatePost']);
    Route::get('/v1/post/{post_id}', [NewPostController::class, 'getPost']);
    Route::get('/v1/posts/{post_id}/likes', [NewPostController::class, 'addLike']);

//comments
    Route::post('/v1/posts/{post_id}/comments', [NewPostController::class, 'storeComment']);
    Route::get('/v1/posts/{post_id}/comments/{comment_id}', [NewPostController::class, 'getComment']);


//    groups
    Route::post('v1/groups', [GroupController::class, 'createGroup']);
    Route::get('v1/groups', [GroupController::class, 'listGroup']);



});



