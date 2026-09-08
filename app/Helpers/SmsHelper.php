<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsHelper
{
    /**
     * Send SMS via BulkSMSBD API.
     *
     * @param string $number A single phone number or comma-separated numbers (e.g. 88017XXXXXXXX,88019XXXXXXXX).
     * @param string $message The SMS body.
     * @return bool True if successful, false otherwise.
     */
    public static function send($number, $message)
    {
        $url = env('BULKSMSBD_URL', 'http://bulksmsbd.net/api/smsapi');
        $apiKey = env('BULKSMSBD_API_KEY');
        $senderId = env('BULKSMSBD_SENDER_ID');

        if (empty($apiKey) || empty($senderId)) {
            Log::error('BulkSMSBD API Key or Sender ID is missing in .env');
            return false;
        }

        try {
            $response = Http::asForm()->post($url, [
                'api_key' => $apiKey,
                'senderid' => $senderId,
                'number' => $number,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $body = $response->body();
                Log::info('BulkSMSBD SMS API response: ' . $body);
                // We will return true for now, but we should inspect the body in the logs
                return true;
            } else {
                Log::error('BulkSMSBD SMS failed with HTTP error. Response: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('BulkSMSBD Exception: ' . $e->getMessage());
            return false;
        }
    }
}
