<?php

namespace App\Http\Controllers\Web\Backend\CMS\Web\About;

use App\Http\Controllers\Controller;
use App\Enums\PageEnum;
use App\Enums\SectionEnum;
use App\Models\CMS;
use Exception;
use App\Http\Requests\CmsRequest;
use App\Services\CmsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MissionController extends Controller
{
    protected $cmsService;

    public $page;
    public $component;
    public $section;

    public $sections;
    public $components;

    public function __construct(CmsService $cmsService)
    {
        $this->cmsService = $cmsService;
        $this->page = PageEnum::ABOUT;
        $this->component = ['title', 'description'];
        $this->section = SectionEnum::MISSION;
    }

    public function index()
    {
        $data = CMS::where('page', $this->page)->where('section', $this->section)->latest()->first();
        return view("backend.layouts.cms.index", ["data" => $data, "page" => $this->page->value, "section" => $this->section->value, "component" => $this->component, 'sections' => $this->sections]);
    }

    public function show($id)
    {
        $data = CMS::where('id', $id)->first();
        return view("backend.layouts.cms.show", ["data" => $data, "page" => $this->page->value, "section" => $this->section->value]);
    }

    public function content(CmsRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $validatedData['page'] = $this->page;
            $validatedData['section'] = $this->section;
            $section = CMS::where('page', $this->page)->where('section', $this->section)->first();

            if ($request->has('rating')) {
                $validatedData['metadata']['rating'] = $request->input('rating');
                unset($validatedData['rating']);
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

            return redirect()->route("admin.cms.{$this->page->value}.{$this->section->value}.index")->with('t-success', 'Updated successfully');
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
}
