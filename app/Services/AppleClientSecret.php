<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Config;

class AppleClientSecret
{
    public function generate(): string
    {
        $clientId       = Config::get('services.apple.client_id');
        $teamId         = Config::get('services.apple.team_id');
        $keyId          = Config::get('services.apple.key_id');
        $privateKeyPath = base_path(Config::get('services.apple.private_key_path'));

        if (empty($clientId) || empty($teamId) || empty($keyId) || empty($privateKeyPath)) {
            throw new \Exception('Apple Sign-In credentials are not properly configured in services.apple');
        }

        if (!file_exists($privateKeyPath)) {
            throw new \Exception("Apple private key file not found at: " . $privateKeyPath);
        }

        $privateKey = trim(file_get_contents($privateKeyPath));

        $payload = [
            'iss' => $teamId,
            'iat' => time(),
            'exp' => time() + (86400 * 180), // 180 days (6 months)
            'aud' => 'https://appleid.apple.com',
            'sub' => $clientId,
        ];

        return JWT::encode($payload, $privateKey, 'ES256', $keyId);
    }
}
