<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NewsController extends Controller
{
    /* ===============================
     * INDEX (DATATABLE)
     * =============================== */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('news')->latest('id')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('thumbnail', fn($row) => '<img src="' . asset($row->thumbnail) . '" width="60">')

                ->addColumn('status', function ($data) {
                    $isPublish = $data->status === 'publish';

                    $backgroundColor = $isPublish ? '#4CAF50' : '#ccc';
                    $sliderTranslateX = $isPublish ? '26px' : '2px';
                    $checked = $isPublish ? 'checked' : '';

                    $status = '<div class="d-flex justify-content-center align-items-center">';
                    $status .= '<div class="form-check form-switch" style="position: relative; width: 50px; height: 24px; background-color: ' . $backgroundColor . '; border-radius: 12px; transition: background-color 0.3s ease; cursor: pointer;">';
                    $status .= '<input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status" style="position: absolute; width: 100%; height: 100%; opacity: 0; z-index: 2; cursor: pointer;">';
                    $status .= '<span style="position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; background-color: white; border-radius: 50%; transition: transform 0.3s ease; transform: translateX(' . $sliderTranslateX . ');"></span>';
                    $status .= '<label for="customSwitch' . $data->id . '" class="form-check-label" style="margin-left: 10px;"></label>';
                    $status .= '</div>';
                    $status .= '</div>';

                    return $status;
                })
                ->addColumn('action', function ($row) {
                    return '
        <button onclick="goToView(' . $row->id . ')" class="btn btn-sm btn-info">View</button>
        <button onclick="goToEdit(' . $row->id . ')" class="btn btn-sm btn-primary">Edit</button>
        <button onclick="showDeleteConfirm(' . $row->id . ')" class="btn btn-sm btn-danger delete">Delete</button>
    ';
                })

                ->rawColumns(['thumbnail', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.news.index');
    }

    /* ===============================
     * =============================== */
    public function create()
    {
        return view('backend.layouts.news.create');
    }

    public function show($id)
    {
        try {
            $news = News::with('details.images')->findOrFail($id);

            // Return JSON
            return response()->json([
                'status' => true,
                'data' => $news
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }



    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'news_title' => 'required|string|max:255',
                'short_description' => 'required',
                'thumbnail' => 'required|image',
                'status' => 'in:publish,unpublish',
                'type' => 'required|string|max:255',
                'details' => 'required|array',
                'details.*.title' => 'required|string|max:255',
                'details.*.description' => 'required|string',
                'details.*.images.*' => 'nullable|image',
            ]);

            // 1️⃣ Save News
            $news = new News();
            $news->title = $request->news_title;
            $news->slug = Str::slug($request->news_title);
            $news->short_description = $request->short_description;
            $news->status = $request->status ? 'unpublish' : 'publish';
            $news->type = $request->type;

            if ($request->hasFile('thumbnail')) {
                $image = $request->file('thumbnail');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $news->thumbnail = Helper::fileUpload($image, 'news', $imageName);
            }

            $news->save();

            // 2️⃣ Save all NewsDetails and images
            foreach ($request->details as $detail) {
                $newsDetail = new NewsDetails();
                $newsDetail->news_id = $news->id;
                $newsDetail->title = $detail['title'];
                $newsDetail->description = $detail['description'];
                $newsDetail->save();

                if (isset($detail['images'])) {
                    foreach ($detail['images'] as $image) {
                        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $path = Helper::fileUpload($image, 'news_details', $imageName);

                        $newsDetail->images()->create(['image' => $path]);
                    }
                }
            }
            DB::commit();
            return redirect()->route('admin.news.index')->with('t-success', 'News, details, and images created successfully');
        } catch (ValidationException $e) {
            DB::rollBack();

            $errors = implode(', ', array_map(function ($messages) {
                return implode(', ', $messages);
            }, $e->errors()));

            return redirect()->back()->withInput()->with('t-error', 'Something went wrong: ' . $errors);
        }
    }

    /* ===============================
     * EDIT FORM
     * =============================== */
    public function edit($id)
    {
        try {
            $news = News::with('details.images')->findOrFail($id);

            // Decode Summernote content if needed
            $news->short_description = html_entity_decode($news->short_description);

            return view('backend.layouts.news.edit', compact('news'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }



    /* ===============================
     * UPDATE NEWS + MULTIPLE DETAILS + IMAGES
     * =============================== */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'news_title' => 'required|string|max:255',
                'short_description' => 'required',
                'thumbnail' => 'nullable|image',
                'status' => 'in:publish,unpublish',
                'type' => 'required|string|max:255',
                'details' => 'required|array',
                'details.*.title' => 'required|string|max:255',
                'details.*.description' => 'required|string',
                'details.*.images.*' => 'nullable|image',
            ]);

            // 1️⃣ Update News
            $news = News::findOrFail($id);
            $news->title = $request->news_title;
            $news->slug = Str::slug($request->news_title) ?? $news->slug;
            $news->short_description = $request->short_description;
            $news->status = $request->status ?? $news->status;
            $news->type = $request->type;

            if ($request->hasFile('thumbnail')) {
                if ($news->thumbnail && file_exists(public_path($news->thumbnail))) {
                    Helper::fileDelete(public_path($news->thumbnail));
                }
                $image = $request->file('thumbnail');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $news->thumbnail = Helper::fileUpload($image, 'news', $imageName);
            }


            $news->save();

            // 2️⃣ Update or add NewsDetails
            foreach ($request->details as $detail) {
                $newsDetail = isset($detail['id'])
                    ? NewsDetails::find($detail['id']) ?? new NewsDetails()
                    : new NewsDetails();

                $newsDetail->news_id = $news->id;
                $newsDetail->title = $detail['title'];
                $newsDetail->description = $detail['description'];
                $newsDetail->save();

                $paths = $newsDetail->images()
                    ->whereIn('id', $request->image_id ?? [])
                    ->pluck('image'); // one SELECT query

                foreach ($paths as $path) {
                    Helper::fileDelete(public_path($path));
                }

                $newsDetail->images()
                    ->whereIn('id', $request->image_id ?? [])
                    ->delete(); // one


                if (isset($detail['images'])) {
                    foreach ($detail['images'] as $image) {
                        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $path = Helper::fileUpload($image, 'news_details', $imageName);


                        // 3️⃣ Delete old images

                        $newsDetail->images()->create(['image' => $path]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.news.index')->with('t-success', 'News, details, and images updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('t-error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /* ===============================
     * DELETE NEWS
     * =============================== */
    public function destroy($id)
    {
        try {
            $news = News::findOrFail($id);

            if ($news->thumbnail && file_exists(public_path($news->thumbnail))) {
                Helper::fileDelete(public_path($news->thumbnail));
            }

            $news->delete();

            return response()->json([
                'status' => true,
                'message' => 'News deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }

    /* ===============================
     * TOGGLE STATUS
     * =============================== */
    public function status($id)
    {
        try {
            $news = News::findOrFail($id);
            $news->status = $news->status === 'publish' ? 'unpublish' : 'publish';
            $news->save();

            return response()->json([
                'status' => true,
                'message' => 'Status updated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
}
