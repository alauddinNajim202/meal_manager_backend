<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class GoogleMapController extends Controller {

    public function __construct()
    {
        View::share('crud', 'google_map_settings');
    }

    public function index() {
        $settings = [
            'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY', '')
        ];

        return view('backend.layouts.settings.google_map_settings', compact('settings'));
    }

    /**
     * Update mail settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse {
        $request->validate([
            'google_maps_api_key' => 'nullable|string|regex:/^[\S]*$/'
        ]);

        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/GOOGLE_MAPS_API_KEY=(.*)\s*/'
            ], [
                'GOOGLE_MAPS_API_KEY=' . $request->google_maps_api_key . $lineBreak
            ], $envContent);

            File::put(base_path('.env'), $envContent);

            return back()->with('t-success', 'Updated successfully');
        } catch (Exception) {
            return back()->with('t-error', 'Failed to update');
        }
    }
}
