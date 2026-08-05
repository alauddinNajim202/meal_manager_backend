<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class LogoController extends Controller
{
    public function __construct()
    {
        View::share('crud', 'logo_settings');
    }

    public function index()
    {
        $setting = Setting::latest('id')->first();
        return view('backend.layouts.settings.logo_settings', compact('setting'));
    }

    /**
     * Update the system settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'logo_width'     => 'nullable|numeric',
            'logo_height'    => 'nullable|numeric',
            'logo'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        try {
            $setting = Setting::first();
            if ($request->hasFile('logo')) {
                if ($setting && $setting->logo && file_exists(public_path($setting->logo))) {
                    Helper::fileDelete(public_path($setting->logo));
                }
                $validatedData['logo'] = Helper::fileUpload($request->file('logo'), 'settings', time() . '_' . getFileName($request->file('logo')));
            }

            Setting::updateOrCreate(['id' => 1],
                $validatedData
            );

            return back()->with('t-success', 'Updated successfully');
        } catch (Exception $e) {
            return back()->with('t-error', 'Failed to update' . $e->getMessage());
        }
    }
}
