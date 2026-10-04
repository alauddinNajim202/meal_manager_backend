<?php
namespace App\Providers;

use Firebase\JWT\JWT;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') !== 'local') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        $clientId       = config('services.apple.client_id');
        $teamId         = config('services.apple.team_id');
        $keyId          = config('services.apple.key_id');
        $privateKeyPath = config('services.apple.private_key_path') ? base_path(config('services.apple.private_key_path')) : null;

        if (! $clientId || ! $teamId || ! $keyId || ! $privateKeyPath || ! file_exists($privateKeyPath)) {
            return;
        }

        $privateKey = trim(file_get_contents($privateKeyPath));

        if (! $privateKey) {
            return;
        }

        $payload = [
            'iss' => $teamId,
            'iat' => time(),
            'exp' => time() + (86400 * 180),
            'aud' => 'https://appleid.apple.com',
            'sub' => $clientId,
        ];

        $clientSecret = JWT::encode($payload, $privateKey, 'ES256', $keyId);

        config()->set('services.apple.client_secret', $clientSecret);

        $this->app['events']->listen(SocialiteWasCalled::class, function ($event) {
            $event->extendSocialite('apple', \SocialiteProviders\Apple\Provider::class);
        });
    }
}
