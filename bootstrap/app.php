<?php

use App\Helpers\Helper;
use App\Http\Middleware\ApiAdminMiddleware;
use App\Http\Middleware\WebAdminMiddleware;
use App\Http\Middleware\WebAuthCheckMiddleware;
use App\Http\Middleware\ApiCustomerMiddleware;
use App\Http\Middleware\WebDeveloperMiddleware;
use App\Http\Middleware\ApiOtpVerifiedMiddleware;
use App\Http\Middleware\WebOtpVerifiedMiddleware;
use App\Http\Middleware\ApiRetailerMiddleware;
use App\Http\Middleware\MessManagerMiddleware;
use App\Http\Middleware\WebStaffMiddleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware(['web'])->prefix('ajax')->name('ajax.')->group(base_path('routes/ajax.php'));
            Route::middleware(['web', 'web-developer'])->prefix('developer')->name('developer.')->group(base_path('routes/web-developer.php'));
            Route::middleware(['web'])->prefix('admin')->name('admin.')->group(base_path('routes/web-admin.php'));
            Route::middleware(['api', 'api-admin'])->prefix('api.admin')->name('api.admin.')->group(base_path('routes/api-admin.php'));
            Route::middleware(['api', 'api-retailer'])->prefix('api/retailer')->name('api.retailer.')->group(base_path('routes/api-retailer.php'));
            Route::middleware(['api', 'otp', 'api-customer'])->prefix('api/customer')->name('api.customer.')->group(base_path('routes/api-customer.php'));
            Route::middleware(['api'])->group(base_path('routes/api-stripe.php'));
            require base_path('routes/cmd.php');
            
        }
    )
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['auth:api']],
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'web-developer'         => WebDeveloperMiddleware::class,
            'web-admin'             => WebAdminMiddleware::class,
            'web-staff'             => WebStaffMiddleware::class,
            'api-admin'             => ApiAdminMiddleware::class,
            'api-customer'          => ApiCustomerMiddleware::class,
            'api-retailer'          => ApiRetailerMiddleware::class,
            'api-otp'               => ApiOtpVerifiedMiddleware::class,
            'web-otp'               => WebOtpVerifiedMiddleware::class,
            'check'                 => WebAuthCheckMiddleware::class,
            'role'                  => RoleMiddleware::class,
            'permission'            => PermissionMiddleware::class,
            'role_or_permission'    => RoleOrPermissionMiddleware::class,
            'mess.manager'          => MessManagerMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'payment/stripe/webhook',
            'graphql',
        ]);
        $middleware->api([
            StartSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return Helper::jsonErrorResponse($e->getMessage(), 422, $e->errors());
                }

                if ($e instanceof ModelNotFoundException) {
                    return Helper::jsonErrorResponse($e->getMessage(), 404);
                }

                if ($e instanceof AuthenticationException) {
                    return Helper::jsonErrorResponse($e->getMessage(), 401);
                }
                if ($e instanceof AuthorizationException) {
                    return Helper::jsonErrorResponse($e->getMessage(), 403);
                }
                // Dynamically determine the status code if available
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                return Helper::jsonErrorResponse($e->getMessage(), $statusCode);
            } else {
                return null;
            }
        });
    })->create();
