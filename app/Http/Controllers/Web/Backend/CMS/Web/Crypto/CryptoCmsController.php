<?php

namespace App\Http\Controllers\Web\Backend\CMS\Web\Crypto;

use App\Http\Controllers\Controller;
use App\Enums\PageEnum;
use App\Enums\SectionEnum;
use App\Helpers\Helper;
use App\Models\CMS;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\CmsRequest;
use App\Services\CmsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CryptoCmsController extends Controller
{
    protected $cmsService;

    public $page;
    public $component;
    public $section;
    
    public $components;
    public $sections;

    public function __construct(CmsService $cmsService)
    {
        $this->cmsService = $cmsService;
        $this->page = PageEnum::CRYPTO_STORE;
        $this->section = SectionEnum::BANNER;
        
        // Fields for the top-level form (often used for page header or single record)
        $this->component = ['title', 'description', 'partners', 'experience', 'image', 'bg'];
        
        // Fields for the CRUD list / Create form
        $this->sections = SectionEnum::BANNER;
        $this->components = ['title', 'description', 'partners', 'experience', 'image', 'bg'];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = CMS::where('page', $this->page)->where('section', $this->sections)->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($data) {
                    if ($data->image) {
                        return '<img src="' . asset($data->image) . '" alt="image" width="50px" height="50px" style="margin-left:20px;">';
                    } else {
                        return '<span>No Image Available</span>';
                    }
                })
                ->addColumn('status', function ($data) {
                    $backgroundColor = $data->status == "active" ? '#4CAF50' : '#ccc';
                    $sliderTranslateX = $data->status == "active" ? '26px' : '2px';
                    $sliderStyles = "position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; background-color: white; border-radius: 50%; transition: transform 0.3s ease; transform: translateX($sliderTranslateX);";

                    $status = '<div class="form-check form-switch" style="margin-left:40px; position: relative; width: 50px; height: 24px; background-color: ' . $backgroundColor . '; border-radius: 12px; transition: background-color 0.3s ease; cursor: pointer;">';
                    $status .= '<input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status" style="position: absolute; width: 100%; height: 100%; opacity: 0; z-index: 2; cursor: pointer;">';
                    $status .= '<span style="' . $sliderStyles . '"></span>';
                    $status .= '<label for="customSwitch' . $data->id . '" class="form-check-label" style="margin-left: 10px;"></label>';
                    $status .= '</div>';

                    return $status;
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <a href="#" onClick="editItem(' . $data->id . ')" type="button" class="btn btn-primary fs-14 text-white edit-icn" title="Edit">
                                    <i class="fe fe-edit"></i>
                                </a>
                                <a href="#" onClick="goToShow(' . $data->id . ')" type="button" class="btn btn-info fs-14 text-white edit-icn" title="View">
                                    <i class="fe fe-eye"></i>
                                </a>
                                <a href="#" type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make();
        }

        $data = CMS::where('page', $this->page)->where('section', $this->section)->latest()->first();
        return view("backend.layouts.cms.index", ["data" => $data, "page" => $this->page->value, "section" => $this->section->value, "component" => $this->component, 'sections' => $this->sections]);
    }

    public function create()
    {
        return view("backend.layouts.cms.create", ["page" => $this->page->value, "section" => $this->section->value, "components" => $this->components]);
    }

    public function store(CmsRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $validatedData['page'] = $this->page;
            $validatedData['section'] = $this->sections;
            $validatedData['metadata'] = $request->input('metadata', []);

            if ($request->hasFile('bg')) {
                $validatedData['bg'] = Helper::fileUpload($request->file('bg'), $this->section->value, time() . '_' . getFileName($request->file('bg')));
            }

            if ($request->hasFile('image')) {
                $validatedData['image'] = Helper::fileUpload($request->file('image'), $this->section->value, time() . '_' . getFileName($request->file('image')));
            }

            do {
                $validatedData['slug'] = 'slug_'.Str::random(8);
            } while (CMS::where('slug', $validatedData['slug'])->exists());

            CMS::create($validatedData);

            if (Cache::has('cms')) {
                Cache::forget('cms');
            }
            Cache::put('cms', CMS::where('status', 'active')->get());

            return redirect()->route("admin.cms.crypto_store.banner.index")->with('t-success', 'Created successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $data = CMS::where('id', $id)->first();
        return view("backend.layouts.cms.show", ["data" => $data, "page" => $this->page->value, "section" => $this->section->value]);
    }

    public function edit($id)
    {
        $data = CMS::findOrFail($id);
        return view("backend.layouts.cms.update", ["data" => $data, "page" => $this->page->value, "section" => $this->section->value, "components" => $this->components]);
    }

    public function update(CmsRequest $request, $id)
    {
        $validatedData = $request->validated();
        try {
            $section = CMS::findOrFail($id);
            $validatedData['page'] = $this->page;
            $validatedData['section'] = $this->sections;
            $validatedData['metadata'] = $request->input('metadata', []);

            if ($request->has('rating')) {
                $validatedData['metadata']['rating'] = $request->input('rating');
                unset($validatedData['rating']);
            }

            if ($request->hasFile('bg')) {
                if ($section->bg && file_exists(public_path($section->bg))) {
                    Helper::fileDelete(public_path($section->bg));
                }
                $validatedData['bg'] = Helper::fileUpload($request->file('bg'), $this->section->value, time() . '_' . getFileName($request->file('bg')));
            }

            if ($request->hasFile('image')) {
                if ($section->image && file_exists(public_path($section->image))) {
                    Helper::fileDelete(public_path($section->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), $this->section->value, time() . '_' . getFileName($request->file('image')));
            }

            $section->update($validatedData);

            if (Cache::has('cms')) {
                Cache::forget('cms');
            }
            Cache::put('cms', CMS::where('status', 'active')->get());

            return redirect()->route("admin.cms.crypto_store.banner.index")->with('t-success', 'Updated successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->cmsService->destroy($id);
            if (Cache::has('cms')) {
                Cache::forget('cms');
            }
            Cache::put('cms', CMS::where('status', 'active')->get());
            return response()->json(['t-success' => true, 'message' => 'Deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['t-success' => false, 'message' => 'Failed to delete.']);
        }
    }

    public function status($id): JsonResponse
    {
        try {
            $this->cmsService->status($id);
            if (Cache::has('cms')) {
                Cache::forget('cms');
            }
            Cache::put('cms', CMS::where('status', 'active')->get());
            return response()->json(['t-success' => true, 'message' => 'Updated successfully.']);
        } catch (Exception $e) {
            return response()->json(['t-success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function content(CmsRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $validatedData['page'] = $this->page;
            $validatedData['section'] = $this->section;
            $validatedData['metadata'] = $request->input('metadata', []);

            if ($request->has('rating')) {
                $validatedData['metadata']['rating'] = $request->input('rating');
                unset($validatedData['rating']);
            }
            
            $section = CMS::where('page', $this->page)->where('section', $this->section)->first();

            if ($request->hasFile('bg')) {
                if ($section && $section->bg && file_exists(public_path($section->bg))) {
                    Helper::fileDelete(public_path($section->bg));
                }
                $validatedData['bg'] = Helper::fileUpload($request->file('bg'), $this->section->value, time() . '_' . getFileName($request->file('bg')));
            }

            if ($request->hasFile('image')) {
                if ($section && $section->image && file_exists(public_path($section->image))) {
                    Helper::fileDelete(public_path($section->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), $this->section->value, time() . '_' . getFileName($request->file('image')));
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

            return redirect()->route("admin.cms.crypto_store.banner.index")->with('t-success', 'Updated successfully');
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
