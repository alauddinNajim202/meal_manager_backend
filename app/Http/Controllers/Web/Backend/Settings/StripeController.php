<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class StripeController extends Controller {
    public function __construct()
    {
        View::share('crud', 'stripe_settings');
    }

    public function index() {
        $settings = [
            'stripe_key'            => env('STRIPE_KEY', ''),
            'stripe_secret'         => env('STRIPE_SECRET', ''),
            'stripe_webhook_secret' => env('STRIPE_WEBHOOK_SECRET', '')
        ];

        return view('backend.layouts.settings.stripe_settings', compact('settings'));
    }

    /**
     * Update mail settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse {
        $request->validate([
            'stripe_key'            => 'nullable|string|regex:/^[\S]*$/',
            'stripe_secret'         => 'nullable|string|regex:/^[\S]*$/',
            'stripe_webhook_secret' => 'nullable|string|regex:/^[\S]*$/'
        ]);

        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/STRIPE_KEY=(.*)\s*/',
                '/STRIPE_SECRET=(.*)\s*/',
                '/STRIPE_WEBHOOK_SECRET=(.*)\s*/'
            ], [
                'STRIPE_KEY=' . $request->stripe_key . $lineBreak,
                'STRIPE_SECRET=' . $request->stripe_secret . $lineBreak,
                'STRIPE_WEBHOOK_SECRET=' . $request->stripe_webhook_secret . $lineBreak
            ], $envContent);

            File::put(base_path('.env'), $envContent);

            return back()->with('t-success', 'Updated successfully');
        } catch (Exception) {
            return back()->with('t-error', 'Failed to update');
        }
    }
}
