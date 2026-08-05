<?php

namespace App\Http\Controllers\Web\Backend\CMS\Web\Footer;

use App\Http\Controllers\Controller;
use App\Enums\PageEnum;
use App\Enums\SectionEnum;
use App\Helpers\Helper;
use App\Models\CMS;
use Exception;
use App\Http\Requests\CmsRequest;
use App\Services\CmsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FooterController extends Controller
{
    protected $cmsService;

    public $page;
    public $component;
    public $section;

    public function __construct(CmsService $cmsService)
    {
        $this->cmsService = $cmsService;
        $this->page = PageEnum::COMMON;
        $this->section = SectionEnum::FOOTER;
        $this->component = ['title', 'description', 'image', 'twitter', 'linkedin']; // added twitter and linkedin
    }

    public function index()
    {
        $data = CMS::where('page', $this->page)->where('section', $this->section)->latest()->first();
        return view("backend.layouts.cms.index", [
            "data" => $data,
            "page" => $this->page->value,
            "section" => $this->section->value,
            "component" => $this->component
        ]);
    }

    public function content(CmsRequest $request)
    {
        $validatedData = $request->validated();
        // dd($validatedData);
        try {
            $validatedData['page'] = $this->page;
            $validatedData['section'] = $this->section;
            $section = CMS::where('page', $this->page)->where('section', $this->section)->first();

            if ($request->hasFile('image')) {
                if ($section && $section->image && file_exists(public_path($section->image))) {
                    Helper::fileDelete(public_path($section->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), $this->section->value, time() . '_' . getFileName($request->file('image')));
            }

            if ($request->has('twitter')) {
                $validatedData['metadata']['twitter'] = $request->input('twitter');
                unset($validatedData['twitter']);
            }
            if ($request->has('linkedin')) {
                $validatedData['metadata']['linkedin'] = $request->input('linkedin');
                unset($validatedData['linkedin']);
            }

            if ($section) {
                $section->update($validatedData);
            } else {
                do {
                    $validatedData['slug'] = 'slug_'.Str::random(8);
                } while (CMS::where('slug', $validatedData['slug'])->exists());

                CMS::create($validatedData);
            }

            if (Cache::has('cms')) {
                Cache::forget('cms');
            }
            Cache::put('cms', CMS::where('status', 'active')->get());

            return redirect()->back()->with('t-success', 'Footer Updated successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', $e->getMessage());
        }
    }

    public function display()
    {
        try {
            $pages = CMS::where('page', $this->page)->where('section', $this->section)->get();
            foreach ($pages as $page) {
                $page->update(['is_display' => !$page->is_display]);
            }
            return back()->with('t-success', 'Display status updated successfully.');
        } catch (Exception $e) {
            return back()->with('t-error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $data = CMS::where('id', $id)->first();
        return view("backend.layouts.cms.show", ["data" => $data, "page" => $this->page->value, "section" => $this->section->value]);
    }
}
