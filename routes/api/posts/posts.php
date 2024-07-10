<?php
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'v1'], function () {
        Route::post('/posts', [PostController::class, 'CreatePost']);
        Route::get('/posts', [PostController::class, 'listPosts']);
        Route::get('/posts/{post_id}', [PostController::class, 'getPost']);
        Route::put('/posts/{post_id}', [PostController::class, 'updatePost']);
        Route::delete('/posts/{post_id}', [PostController::class, 'deletePost']);
    });
});
