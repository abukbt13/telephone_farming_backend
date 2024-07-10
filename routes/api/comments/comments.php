<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'v1'], function () {
        Route::post('/posts/{post_id}/comments', [CommentController::class, 'storeComment']);
        Route::get('/posts/{post_id}/comments', [CommentController::class, 'getComments']);
        Route::get('/posts/{post_id}/comments/{comment_id}', [CommentController::class, 'getComment']);
        Route::put('/posts/{post_id}/comments/{comment_id}', [CommentController::class, 'updateComment']);
        Route::delete('/posts/{post_id}/comments/{comment_id}', [CommentController::class, 'deleteComment']);
    });
});


