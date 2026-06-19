<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewCommentController;
use App\Http\Controllers\ReviewLikeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('books', BookController::class);
Route::resource('shelves', ShelfController::class);
Route::resource('ratings', RatingController::class);
Route::resource('reviews', ReviewController::class);

Route::post('/reviews/{review}/comments', [ReviewCommentController::class, 'store'])->name('reviews.comments.store');
Route::delete('/reviews/comments/{comment}', [ReviewCommentController::class, 'destroy'])->name('reviews.comments.destroy');

Route::post('/reviews/{review}/like', [ReviewLikeController::class, 'store'])->name('reviews.like');
Route::delete('/reviews/{review}/unlike', [ReviewLikeController::class, 'destroy'])->name('reviews.unlike');

Route::post('/users/{user}/follow', [FollowController::class, 'store'])->name('users.follow');
Route::delete('/users/{user}/unfollow', [FollowController::class, 'destroy'])->name('users.unfollow');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('books', AdminBookController::class);
    Route::resource('users', AdminUserController::class);
});
