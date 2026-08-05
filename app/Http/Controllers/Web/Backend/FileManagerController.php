<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{
    public function index()
    {
        $files = Storage::files('public');
        $folders = Storage::directories('public');
        return view('backend.layouts.file_manager.index', compact('files', 'folders'));
    }

    public function upload(Request $request)
    {
        $request->file('file')->store('public');
        return back();
    }

    public function delete($file)
    {
        Storage::delete('public/'.$file);
        return back();
    }
}
