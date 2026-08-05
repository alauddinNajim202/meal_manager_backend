<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class EnvController extends Controller {

    public function __construct()
    {
        View::share('crud', 'env_settings');
    }

    public function index() {
        $settings = [
            'app_name'        => env('APP_NAME', ''),
            'app_url'         => env('APP_URL', '')
        ];

        return view('backend.layouts.settings.env_settings', compact('settings'));
    }

    /**
     * Update mail settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse {
        $request->validate([
            'app_name' => 'nullable|string|regex:/^[\S]*$/',
            'app_url'  => 'nullable|string|regex:/^[\S]*$/',
        ]);

        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/APP_NAME=(.*)\s*/',
                '/APP_URL=(.*)\s*/'
            ], [
                'APP_NAME=' . $request->app_name . $lineBreak,
                'APP_URL=' . $request->app_url . $lineBreak
            ], $envContent);

            File::put(base_path('.env'), $envContent);

            return back()->with('t-success', 'Updated successfully');
        } catch (Exception) {
            return back()->with('t-error', 'Failed to update');
        }
    }
}
