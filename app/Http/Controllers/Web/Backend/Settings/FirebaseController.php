<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class FirebaseController extends Controller {
    public function __construct()
    {
        View::share('crud', 'firebase_settings');
    }

    public function index() {
        $settings = [
            'firebase_credentials' => env('FIREBASE_CREDENTIALS', '')
        ];

        return view('backend.layouts.settings.firebase_settings', compact('settings'));
    }

    /**
     * Update mail settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse {
        $request->validate([
            'firebase_credentials' => 'nullable|string|regex:/^[\S]*$/'
        ]);

        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/FIREBASE_CREDENTIALS=(.*)\s*/'
            ], [
                'FIREBASE_CREDENTIALS=' . $request->firebase_credentials . $lineBreak
            ], $envContent);

            File::put(base_path('.env'), $envContent);

            return back()->with('t-success', 'Updated successfully');
        } catch (Exception) {
            return back()->with('t-error', 'Failed to update');
        }
    }
}
