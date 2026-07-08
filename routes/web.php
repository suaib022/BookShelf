<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

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

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyBooksController;
use App\Http\Controllers\UserProfileController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/users/{username}', [UserProfileController::class, 'show'])->name('profile.show');

Route::resource('books', BookController::class)->only(['index', 'show']);
Route::get('/genres/{genre:name}', [BookController::class, 'index'])->name('genres.show');
Route::middleware(['auth', 'active_user'])->group(function () {
    Route::get('/my-books', [MyBooksController::class, 'index'])->name('my-books.index');
    Route::resource('shelves', ShelfController::class)->except(['index', 'show']);
    Route::resource('ratings', RatingController::class)->except(['index', 'show']);
    Route::resource('reviews', ReviewController::class)->except(['index', 'show']);

    Route::post('/reviews/{review}/comments', [ReviewCommentController::class, 'store'])->name('reviews.comments.store');
    Route::delete('/reviews/comments/{comment}', [ReviewCommentController::class, 'destroy'])->name('reviews.comments.destroy');

    Route::post('/reviews/{review}/toggle-like', [ReviewLikeController::class, 'toggle'])->name('reviews.like.toggle');

    Route::post('/users/{user}/follow', [FollowController::class, 'store'])->name('users.follow');
    Route::delete('/users/{user}/unfollow', [FollowController::class, 'destroy'])->name('users.unfollow');
});

Route::resource('shelves', ShelfController::class)->only(['index', 'show']);
Route::resource('ratings', RatingController::class)->only(['index', 'show']);
Route::resource('reviews', ReviewController::class)->only(['index', 'show']);

use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/toggle-mode', [\App\Http\Controllers\ModeToggleController::class, 'toggle'])->name('toggle-mode');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('books/search-google', [AdminBookController::class, 'searchGoogleBooks'])->name('books.search-google');
    Route::post('books/bulk-destroy', [AdminBookController::class, 'bulkDestroy'])->name('books.bulkDestroy');
    Route::resource('books', AdminBookController::class);
    Route::resource('users', AdminUserController::class);
});

require __DIR__.'/auth.php';
