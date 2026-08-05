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
        $clientId       = config('services.apple.client_id');
        $teamId         = config('services.apple.team_id');
        $keyId          = config('services.apple.key_id');
        $privateKeyPath = base_path(config('services.apple.private_key_path'));

        if (! $clientId || ! $teamId || ! $keyId || ! file_exists($privateKeyPath)) {
            \Log::warning("Apple login config missing. Skipping Apple JWT setup.");
            return;
        }

        $privateKey = trim(file_get_contents($privateKeyPath));

        if (! $privateKey) {
            \Log::warning("Apple private key empty. Skipping.");
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
