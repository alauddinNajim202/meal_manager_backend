<?php

use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Web\Frontend\AffiliateController;
use App\Http\Controllers\Web\Frontend\ContactController;
use App\Http\Controllers\Web\Frontend\HomeController;
use App\Http\Controllers\Web\Frontend\PageController;
use App\Http\Controllers\Web\Frontend\SubscriberController;
use App\Http\Controllers\Web\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/affiliate/{slug}', [AffiliateController::class, 'store'])->name('store');

Route::get('/post', [HomeController::class, 'index'])->name('post.index');
Route::get('/post/show/{slug}', [HomeController::class, 'post'])->name('post.show');

//Social login test routes
Route::get('social-login/{provider}', [SocialLoginController::class, 'RedirectToProvider'])->name('social.login');
Route::get('social-login/{provider}/callback', [SocialLoginController::class, 'HandleProviderCallback']);

Route::post('subscriber/store', [SubscriberController::class, 'store'])->name('subscriber.data.store');

Route::post('contact/store', [ContactController::class, 'store'])->name('web.contact.store');

Route::controller(NotificationController::class)->prefix('notification')->name('notification.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('read/single/{id}', 'readSingle')->name('read.single');
    Route::POST('read/all', 'readAll')->name('read.all');
})->middleware('auth');

Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy.policy');


use App\Http\Controllers\Web\Frontend\UserController;

Route::prefix('user')->name('user.')->group(function () {
    Route::get('/login', [UserController::class, 'loginPage'])->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.post');
    
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [UserController::class, 'logout'])->name('logout');
        Route::delete('/delete', [UserController::class, 'deleteAccount'])->name('delete');
    });
});

require __DIR__ . '/auth.php';

Route::get('/page/{slug}', [PageController::class, 'index']);


