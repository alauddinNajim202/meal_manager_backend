<?php

namespace App\Http\Controllers\Web\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PluginService;
use Illuminate\Support\Facades\View;

class PluginController extends Controller
{
    protected $pluginService;
    public function __construct(PluginService $pluginService)
    {
        $this->pluginService = $pluginService;
        View::share('crud', 'package');
    }

    public function index()
    {
        return view('backend.layouts.plugins.index');
    }

    public function install(Request $request)
    {
        $path = public_path('uploads/plugins/');
        $plugin = base64_decode($request->plugin);
        $url = $path . $plugin;
        $zip = new \ZipArchive();
        $res = $zip->open($url);
        if ($res === true) {

            $app = null;
            $version = null;
            $filesInZip = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $filesInZip[] = $stat['name'];
            }
            foreach ($filesInZip as $fileInZip) {
                if (strpos($fileInZip, 'info.json') !== false) {
                    $pluginInfoJson = $zip->getFromName($fileInZip);
                    $pluginInfo = json_decode($pluginInfoJson, true, 512, JSON_THROW_ON_ERROR);
                    if ($pluginInfo !== null) {
                        $app = $pluginInfo['app'] ?? null;
                        $version = $pluginInfo['version'] ?? null;
                    }

                    break;
                }
            }

            if (file_exists(base_path('Plugins/'.$app.'v'.$version.'/'))) {
                return back()->with('t-error', 'Plugin already installed.');
            }

            $zip->extractTo(base_path('Plugins/'.$app.'v'.$version.'/'));
            $zip->close();
            return back()->with('t-success', 'Plugin installed successfully.');
        } else {
            return back()->with('t-error', 'Failed to open the zip file.');
        }
    }

    public function uninstall(Request $request)
    {
        $plugin = base64_decode($request->plugin);
        $pluginPath = base_path('Plugins/' . $plugin);

        if (is_dir($pluginPath) && file_exists($pluginPath)) {

            $this->pluginService->deleteDirectory($pluginPath);

            return back()->with('t-success', 'Plugin uninstalled successfully.');
        } else {
            return back()->with('t-error', 'Plugin not found.');
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'plugins'   => 'required|array',
            'plugins.*' => 'file|mimes:zip|max:10120',
        ]);

        $files = $request->file('plugins');

        foreach ($files as $file) {
            $extension = $file->getClientOriginalExtension();
            $fileName = $file->getClientOriginalName();
            $makeName = time() . rand(1000, 9999) . '.' . $extension;
            $file->move(public_path("uploads/plugins/"), $makeName);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Plugin uploaded successfully'
        ]);
    }

    public function download(Request $request)
    {
        $file = base64_decode($request->file);
        $filePath = public_path('uploads/plugins/' . $file);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            return back()->with('t-error', 'File not found.');
        }
    }

    public function delete(Request $request, $file)
    {
        $filePath = public_path('uploads/plugins/' . base64_decode($file));

        if (file_exists($filePath)) {
            unlink($filePath);
            return back()->with('t-success', 'Plugin deleted successfully.');
        } else {
            return back()->with('t-error', 'File not found.');
        }
    }
}
