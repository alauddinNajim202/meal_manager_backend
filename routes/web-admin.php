<?php


use App\Http\Controllers\Web\Backend\Access\PermissionController;
use App\Http\Controllers\Web\Backend\Access\RoleController;
use App\Http\Controllers\Web\Backend\Access\UserController;
use App\Http\Controllers\Web\Backend\AttributeController;

use App\Http\Controllers\Web\Backend\CategoryController;
use App\Http\Controllers\Web\Backend\ChatController;


use App\Http\Controllers\Web\Backend\Settings\ProfileController;
use App\Http\Controllers\Web\Backend\Settings\MailSettingController;
use App\Http\Controllers\Web\Backend\Settings\SettingController;
use App\Http\Controllers\Web\Backend\Settings\SocialController;
use App\Http\Controllers\Web\Backend\Settings\GoogleMapController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\FileManagerController;
use App\Http\Controllers\Web\Backend\ImageController;

use App\Http\Controllers\Web\Backend\PageController;

use App\Http\Controllers\Web\Backend\ProductController;


use App\Http\Controllers\Web\Backend\Settings\LogoController;
use App\Http\Controllers\Web\Backend\Settings\OtherController;

use App\Http\Controllers\Web\Backend\SocialLinkController;
use App\Http\Controllers\Web\Backend\SubcategoryController;
use App\Http\Controllers\Web\Backend\SubscriberController;
use App\Http\Controllers\Web\Backend\TemplateEmailController;
use App\Http\Controllers\Web\Backend\TransactionController;

use App\Http\Controllers\Web\Backend\ReportController;;

use Illuminate\Support\Facades\Artisan;

Route::get("admin/dashboard", [DashboardController::class, 'index'])->name('dashboard')->middleware(['role:admin|staff']);

Route::group(['middleware' => ['web-admin']], function () {



    Route::controller(TemplateEmailController::class)->prefix('template/email')->name('template.email.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/show/{id}', 'show')->name('show');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'destroy')->name('destroy');
        Route::get('/status/{id}', 'status')->name('status');
    });



    Route::controller(CategoryController::class)->prefix('category')->name('category.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/show/{id}', 'show')->name('show');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'destroy')->name('destroy');
        Route::get('/status/{id}', 'status')->name('status');
    });

    Route::controller(SubcategoryController::class)->prefix('subcategory')->name('subcategory.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/show/{id}', 'show')->name('show');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'destroy')->name('destroy');
        Route::get('/status/{id}', 'status')->name('status');
    });

    Route::controller(ProductController::class)->prefix('product')->name('product.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/show/{id}', 'show')->name('show');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'destroy')->name('destroy');
        Route::get('/status/{id}', 'status')->name('status');
    });






    Route::controller(PageController::class)->prefix('page')->name('page.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'destroy')->name('destroy');
        Route::get('/status/{id}', 'status')->name('status');
    });

    Route::controller(SocialLinkController::class)->prefix('social')->name('social.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'destroy')->name('destroy');
        Route::get('/status/{id}', 'status')->name('status');
    });

    Route::controller(FaqController::class)->prefix('faq')->name('faq.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/show/{id}', 'show')->name('show');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'destroy')->name('destroy');
        Route::get('/status/{id}', 'status')->name('status');
    });

    Route::get('subscriber', [SubscriberController::class, 'index'])->name('subscriber.index');



    Route::controller(TransactionController::class)->prefix('transaction')->name('transaction.')->group(function () {
        Route::get('/{user_id?}', 'index')->name('index');
        Route::get('/show/{id}', 'show')->name('show');
    });






    /*
    * CMSsetting/profile
    */

    Route::prefix('cms')->name('cms.')->group(function () {



        //Crypto Store CMS
        Route::prefix('crypto')->name('crypto_store.')->group(function () {
            //Consolidated Crypto Page Content
            Route::prefix('content')->name('banner.')->controller(\App\Http\Controllers\Web\Backend\CMS\Web\Crypto\CryptoCmsController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}/show', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::patch('/{id}', 'update')->name('update');
                Route::delete('/{id}/destroy', 'destroy')->name('destroy');
                Route::get('/{id}/status', 'status')->name('status');

                Route::put('/content', 'content')->name('content');
                Route::get('/display', 'display')->name('display');
            });
        });

        //About Page CMS
        Route::prefix('about')->name('about.')->group(function () {
            //About Section
            Route::prefix('about')->name('about.')->controller(\App\Http\Controllers\Web\Backend\CMS\Web\About\AboutController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}/show', 'show')->name('show');
                Route::put('/content', 'content')->name('content');
                Route::get('/display', 'display')->name('display');
            });

            //Mission Section
            Route::prefix('mission')->name('mission.')->controller(\App\Http\Controllers\Web\Backend\CMS\Web\About\MissionController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}/show', 'show')->name('show');
                Route::put('/content', 'content')->name('content');
                Route::get('/display', 'display')->name('display');
            });

            //Team Section
            Route::prefix('team')->name('team.')->controller(\App\Http\Controllers\Web\Backend\CMS\Web\About\TeamController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}/show', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::patch('/{id}', 'update')->name('update');
                Route::delete('/{id}/destroy', 'destroy')->name('destroy');
                Route::get('/{id}/status', 'status')->name('status');

                Route::put('/content', 'content')->name('content');
                Route::get('/display', 'display')->name('display');
            });
        });

        //Common Page CMS
        Route::prefix('common')->name('common.')->group(function () {
            //Footer Section
            Route::prefix('footer')->name('footer.')->controller(\App\Http\Controllers\Web\Backend\CMS\Web\Footer\FooterController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}/show', 'show')->name('show');
                Route::put('/content', 'content')->name('content');
                Route::get('/display', 'display')->name('display');
            });
        });
    });

    /*
    * Chating Route
    */

    Route::controller(ChatController::class)->prefix('chat')->name('chat.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/list', 'list')->name('list');
        Route::post('/send/{receiver_id}', 'send')->name('send');
        Route::get('/conversation/{receiver_id}', 'conversation')->name('conversation');
        Route::get('/room/{receiver_id}', 'room');
        Route::get('/search', 'search')->name('search');
        Route::get('/seen/all/{receiver_id}', 'seenAll');
        Route::get('/seen/single/{chat_id}', 'seenSingle');
    });


    /*
    * Users Access Route
    */

    Route::resource('users', UserController::class);
    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('/status/{id}', 'status')->name('status');
        Route::get('/new', 'new')->name('new.index');
        Route::get('/ajax/new/count', 'newCount')->name('ajax.new.count');
        Route::get('/card/{slug}', 'card')->name('card');
    });

    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);

    /*
    *settings
    */

    //! Route for Profile Settings
    Route::controller(ProfileController::class)->group(function () {
        Route::get('setting/profile', 'index')->name('setting.profile.index');
        Route::put('setting/profile/update', 'UpdateProfile')->name('setting.profile.update');
        Route::put('setting/profile/update/Password', 'UpdatePassword')->name('setting.profile.update.Password');
        Route::post('setting/profile/update/Picture', 'UpdateProfilePicture')->name('update.profile.picture');
    });



    //! Route for Mail Settings
    Route::controller(MailSettingController::class)->group(function () {
        Route::get('setting/mail', 'index')->name('setting.mail.index');
        Route::patch('setting/mail', 'update')->name('setting.mail.update');

        Route::post('setting/send', 'send')->name('setting.mail.send');
    });




    //! Route for Firebase Settings
    Route::controller(SocialController::class)->prefix('setting/social')->name('setting.social.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::patch('/update', 'update')->name('update');
    });

    //! Route for Stripe Settings
    Route::controller(SettingController::class)->group(function () {
        Route::get('setting/general', 'index')->name('setting.general.index');
        Route::patch('setting/general', 'update')->name('setting.general.update');
    });

    //! Route for Logo Settings
    Route::controller(LogoController::class)->group(function () {
        Route::get('setting/logo', 'index')->name('setting.logo.index');
        Route::patch('setting/logo', 'update')->name('setting.logo.update');
    });

    //! Route for Google Map Settings
    Route::controller(GoogleMapController::class)->group(function () {
        Route::get('setting/google/map', 'index')->name('setting.google.map.index');
        Route::patch('setting/google/map', 'update')->name('setting.google.map.update');
    });



    //Ajax settings
    Route::prefix('setting/other')->name('setting.other')->group(function () {
        Route::get('/', [OtherController::class, 'index'])->name('.index');
        Route::get('/mail', [OtherController::class, 'mail'])->name('.mail');
        Route::get('/sms', [OtherController::class, 'sms'])->name('.sms');
        Route::get('/recaptcha', [OtherController::class, 'recaptcha'])->name('.recaptcha');
        Route::get('/pagination', [OtherController::class, 'pagination'])->name('.pagination');
        Route::get('/reverb', [OtherController::class, 'reverb'])->name('.reverb');
        Route::get('/debug', [OtherController::class, 'debug'])->name('.debug');
        Route::get('/access', [OtherController::class, 'access'])->name('.access');
    });

    // Run artisan commands for optimization and cache clearing
    Route::get('/optimize', function () {
        Artisan::call('system:clear-cache');
        return redirect()->back()->with('t-success', 'Message sent successfully');
    })->name('optimize');

    /*
    * Property Route
    */
    Route::controller(\App\Http\Controllers\Web\Backend\PropertyController::class)->prefix('property')->name('property.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/status/{id}', 'statusUpdate')->name('status');
        Route::delete('/destroy/{id}', 'destroy')->name('destroy');
    });
});

//livewire
Route::get('livewire/crud', function () {
    return view('backend.layouts.livewire.index');
})->name('livewire.crud.index');

//File-Manager
Route::get('file-manager', [FileManagerController::class, 'index'])->name('file-manager');
Route::post('file-upload', [FileManagerController::class, 'upload']);
Route::delete('file-delete/{file}', [FileManagerController::class, 'delete']);
