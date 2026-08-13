<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FirebaseTokenController;
use App\Http\Controllers\Api\Frontend\CategoryController;
use App\Http\Controllers\Api\Frontend\FaqController;
use App\Http\Controllers\Api\Frontend\HomeController;
use App\Http\Controllers\Api\Frontend\PageController;
use App\Http\Controllers\Api\Frontend\SettingsController;
use App\Http\Controllers\Api\Frontend\SocialLinksController;
use App\Http\Controllers\Api\Frontend\SubcategoryController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\FavouriteController;
use App\Http\Controllers\Api\Frontend\MessController;
use App\Http\Controllers\Api\Frontend\TransactionController;

//page
Route::get('/page/home', [HomeController::class, 'index']);

Route::get('/category', [CategoryController::class, 'index']);
Route::get('/subcategory', [SubcategoryController::class, 'index']);

Route::get('/social/links', [SocialLinksController::class, 'index']);
Route::get('/settings', [SettingsController::class, 'index']);
Route::get('/faq', [FaqController::class, 'index']);



Route::get('dynamic/page', [PageController::class, 'index']);
Route::get('dynamic/page/show/{slug}', [PageController::class, 'show']);

Route::get('/property-form-data', [PropertyController::class, 'getFormData']);

Route::get('/location/list', [HomeController::class, 'divisions']);




Route::middleware(['auth:api'])->controller(FavouriteController::class)->prefix('auth/property')->group(function () {
    Route::post('/favorite', 'toggleFavorite');
    Route::get('/favorite/list', 'favoritesList');
});







Route::group(['middleware' => 'guest:api'], function ($router) {
    //register
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/verify/phone', [RegisterController::class, 'VerifyPhone'])->name('verify.phone');
    Route::post('/resend-otp', [RegisterController::class, 'ResendOtp']);
    Route::post('/verify-otp', [RegisterController::class, 'VerifyEmail']);
    //login
    Route::post('login', [LoginController::class, 'login'])->name('api.login');
    //forgot password
    Route::post('/forget-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('/otp-token', [ResetPasswordController::class, 'MakeOtpToken']);
    Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);
    //social login
    Route::post('/social-login', [SocialLoginController::class, 'SocialLogin']);
});



Route::group(['middleware' => ['auth:api', 'api-otp']], function ($router) {


    Route::get('/refresh-token', [LoginController::class, 'refreshToken']);
    Route::post('/logout', [LogoutController::class, 'logout']);



    Route::get('/profile/information', [UserController::class, 'me']);
    Route::post('/update-profile', [UserController::class, 'updateProfile']);



    Route::post('/update-avatar', [UserController::class, 'updateAvatar']);
    Route::delete('/delete-profile', [UserController::class, 'destroy']);
    Route::POST('/update-password', [UserController::class, 'password_update']);
    Route::post('/update-cover', [UserController::class, 'update_cover_image']);
    Route::post('/update-link', [UserController::class, 'update_link']);
});

/*
# Firebase Notification Route
*/

Route::middleware(['auth:api'])->controller(FirebaseTokenController::class)->prefix('firebase')->group(function () {
    Route::get("test", "test");
    Route::post("token/add", "store");
    Route::post("token/get", "getToken");
    Route::post("token/delete", "deleteToken");
});

/*
# In App Notification Route
*/

Route::middleware(['auth:api'])->controller(NotificationController::class)->prefix('notify')->group(function () {
    Route::get('test', 'test');
    Route::get('/', 'index');
    Route::get('status/read/all', 'readAll');
    Route::get('status/read/{id}', 'readSingle');
});

Route::get('/verify-email-link', [ResetPasswordController::class, 'verifyEmailLink'])
    ->name('verify.email.link')
    ->middleware('signed');
/*
# Chat Route
*/

Route::middleware(['auth:api'])->controller(ChatController::class)->prefix('auth/chat')->group(function () {
    Route::get('/list', 'list');
    Route::post('/send/{receiver_id}', 'send');
    Route::get('/conversation/{receiver_id}', 'conversation');
    Route::get('/room/{receiver_id}', 'room');
    Route::get('/search', 'search');
    Route::get('/seen/all/{receiver_id}', 'seenAll');
    Route::get('/seen/single/{chat_id}', 'seenSingle');
});

/*
# CMS
*/

Route::prefix('cms')->name('cms.')->group(function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('about', [\App\Http\Controllers\Api\AboutController::class, 'index'])->name('about');
    Route::get('footer', [HomeController::class, 'footer'])->name('common');
});


// =====================
// Mess Management Routes
// =====================
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\BazarScheduleController;

Route::middleware(['auth:api'])->prefix('mess')->group(function () {
    Route::get('/list', [MessController::class, 'index']);          // GET  /api/mess/list
    Route::post('/create', [MessController::class, 'create']);      // POST /api/mess/create
    Route::post('/switch', [MessController::class, 'switchMess']);  // POST /api/mess/switch
    Route::post('/leave', [MessController::class, 'leave']);        // POST /api/mess/leave

    // Member Management
    Route::get('/members/list', [MemberController::class, 'index']);
    Route::post('/members/store', [MemberController::class, 'store']);
    Route::delete('/members/{id}', [MemberController::class, 'destroy']);
    Route::patch('/members/{id}/role', [MemberController::class, 'changeRole']);

    // Deposit (Add Money)
    Route::get('/deposits/list', [DepositController::class, 'index']);
    Route::post('/deposits/store', [DepositController::class, 'store']);
    Route::delete('/deposits/{id}', [DepositController::class, 'destroy']);

    // Expense (Bazar / Khoroch)
    Route::get('/expenses/list', [ExpenseController::class, 'index']);
    Route::post('/expenses/store', [ExpenseController::class, 'store']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);

    // Meals
    Route::get('/meals/list', [MealController::class, 'index']);
    Route::post('/meals/store', [MealController::class, 'store']);
    Route::delete('/meals/{id}', [MealController::class, 'destroy']);


    // Bazar Schedule
    Route::get('/bazar-schedule/list', [BazarScheduleController::class, 'index']);
    Route::post('/bazar-schedule/bulk/store', [BazarScheduleController::class, 'store']);
    Route::delete('/bazar-schedule/{id}', [BazarScheduleController::class, 'destroy']);








    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/my-bill', [ReportController::class, 'myBill']);
    Route::post('/reports/generate', [ReportController::class, 'generate']);


    Route::get('/transactions', [TransactionController::class, 'index']);


});
