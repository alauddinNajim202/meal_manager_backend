<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SignatureController extends Controller {
    public function __construct()
    {
        View::share('crud', 'signature_settings');
    }

    public function index() 
    {
        $signature = Setting::latest('id')->first()->signature;
        return view('backend.layouts.settings.signature_settings', compact('signature'));
    }

    /**
     * Update mail settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse {

        $request->validate([
            'signature' => 'required|string'
        ]);

        try {
            $base64Image = $request->signature;
            $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);

            $settings = Setting::latest('id')->first();
            $settings->signature = $base64Image;
            $settings->save();

            return back()->with('t-success', 'Updated successfully');
        } catch (Exception) {
            return back()->with('t-error', 'Failed to update');
        }

    }
}
