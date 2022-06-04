<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\BookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/articles', [ArticleController::class, 'index'])->name('articles');
// Route::post('/filter', [ArticleController::class, 'filter'])->name('filter');
Route::get('/filter', [ArticleController::class, 'filter'])->name('filter');
Route::get('/article/{id}/show', [ArticleController::class, 'show'])->name('show');
Route::get('/article/{id}/endpoint', [ArticleController::class, 'endpoint'])->name('endpoint');
// Route::apiResource('/articles', ArticleController::class);

Route::middleware('auth:sanctum')
    ->group(function () {
        // profile
        Route::get('/mypage', [UserController::class, 'mypage'])->name('mypage');
        Route::get('/posts', [UserController::class, 'posts'])->name('posts');
        Route::get('/likes', [UserController::class, 'likes'])->name('likes');
        Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');   
        Route::put('/editName', [UserController::class, 'editName'])->name('editName');
        Route::put('/editEmail', [UserController::class, 'editEmail'])->name('editEmail');
        Route::put('/editIcon', [UserController::class, 'editIcon'])->name('editIcon');
        Route::put('/editPassword', [UserController::class, 'editPassword'])->name('editPassword');
        Route::get('/showPosts', [UserController::class, 'showPosts'])->name('posts');
        Route::get('/showLikes',  [UserController::class, 'showLikes'])->name('likes');
        // article
        Route::get('/article/create', [ArticleController::class, 'create'])->name('create');
        Route::post('/article/store', [ArticleController::class, 'store'])->name('store');
        Route::get('/article/{id}/edit', [ArticleController::class, 'edit'])->name('edit');
        Route::put('/article/{id}/update', [ArticleController::class, 'update'])->name('update');
        Route::delete('/article/{id}/delete',  [ArticleController::class, 'destroy'])->name('delete');

        // like
        Route::put('article/{article}/like', [ArticleController::class, 'like'])->name('like');
        Route::delete('article/{article}/like', [ArticleController::class, 'unlike'])->name('unlike');
    });