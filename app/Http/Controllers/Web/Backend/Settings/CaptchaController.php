<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class CaptchaController extends Controller {

    public function __construct()
    {
        View::share('crud', 'captcha_settings');
    }

    public function index() {
        $settings = [
            'recaptcha_site_key'    => env('RECAPTCHA_SITE_KEY', ''),
            'recaptcha_secret_key'=> env('RECAPTCHA_SECRET_KEY', '')
        ];

        return view('backend.layouts.settings.captcha_settings', compact('settings'));
    }

    /**
     * Update mail settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse {
        $request->validate([
            'recaptcha_site_key'    => 'nullable|string|regex:/^[\S]*$/',
            'recaptcha_secret_key'  => 'nullable|string|regex:/^[\S]*$/'
        ]);

        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/RECAPTCHA_SITE_KEY=(.*)\s*/',
                '/RECAPTCHA_SECRET_KEY=(.*)\s*/'
            ], [
                'RECAPTCHA_SITE_KEY=' . $request->recaptcha_site_key.$lineBreak,
                'RECAPTCHA_SECRET_KEY=' . $request->recaptcha_secret_key.$lineBreak
            ], $envContent);

            File::put(base_path('.env'), $envContent);

            return back()->with('t-success', 'Updated successfully');
        } catch (Exception) {
            return back()->with('t-error', 'Failed to update');
        }
    }
}
