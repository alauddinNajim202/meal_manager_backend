<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\AdminCategoryOverride;
use Illuminate\Http\Request;
use Exception;

class AdminCategoryOverrideController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = AdminCategoryOverride::query()->orderByDesc('created_at');
            return datatables()->eloquent($data)
                ->addIndexColumn()
                ->editColumn('enabled', function ($row) {
                    $backgroundColor = $row->enabled ? '#4CAF50' : '#ccc';
                    $sliderTranslateX = $row->enabled ? '26px' : '2px';
                    
                    $status = '<div class="d-flex justify-content-center align-items-center">';
                    $status .= '<div class="form-check form-switch" style="position: relative; width: 50px; height: 24px; background-color: ' . $backgroundColor . '; border-radius: 12px; transition: background-color 0.3s ease; cursor: pointer;">';
                    $status .= '<input onclick="showStatusChangeAlert(\'' . $row->id . '\')" type="checkbox" class="form-check-input" id="customSwitch' . $row->id . '" style="position: absolute; width: 100%; height: 100%; opacity: 0; z-index: 2; cursor: pointer;">';
                    $status .= '<span style="position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; background-color: white; border-radius: 50%; transition: transform 0.3s ease; transform: translateX('.$sliderTranslateX.');"></span>';
                    $status .= '<label for="customSwitch' . $row->id . '" class="form-check-label" style="margin-left: 10px;"></label>';
                    $status .= '</div>';
                    $status .= '</div>';
                
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    return '<div class="btn-group btn-group-sm" role="group">
                                <a href="' . route('admin.admin-category-overrides.edit', $row->id) . '" class="btn btn-primary fs-14 text-white" title="Edit">
                                    <i class="fe fe-edit"></i>
                                </a>
                                <button type="button" onclick="showDeleteConfirm(\'' . $row->id . '\')" class="btn btn-danger fs-14 text-white" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </button>
                            </div>';
                })
                ->rawColumns(['enabled', 'action'])
                ->toJson();
        }
        return view('backend.layouts.admin_category_overrides.index');
    }

    public function create()
    {
        return view('backend.layouts.admin_category_overrides.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_pattern' => 'required|string|max:255|unique:admin_category_overrides,question_pattern',
            'force_category'   => 'required|string|max:255',
        ]);

        try {
            AdminCategoryOverride::create([
                'question_pattern' => $request->question_pattern,
                'force_category'   => $request->force_category,
                'enabled'          => true,
            ]);
            return redirect()->route('admin.admin-category-overrides.index')->with('t-success', 'Category Override created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('t-error', 'Something went wrong! ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $override = AdminCategoryOverride::findOrFail($id);
        return view('backend.layouts.admin_category_overrides.edit', compact('override'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question_pattern' => 'required|string|max:255|unique:admin_category_overrides,question_pattern,' . $id,
            'force_category'   => 'required|string|max:255',
        ]);

        try {
            $override = AdminCategoryOverride::findOrFail($id);
            $override->update([
                'question_pattern' => $request->question_pattern,
                'force_category'   => $request->force_category,
            ]);
            return redirect()->route('admin.admin-category-overrides.index')->with('t-success', 'Category Override updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('t-error', 'Something went wrong! ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            AdminCategoryOverride::findOrFail($id)->delete();
            return response()->json([
                'status'  => 't-success',
                'message' => 'Record deleted successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 't-error',
                'message' => 'Something went wrong! ' . $e->getMessage()
            ], 500);
        }
    }

    public function status($id)
    {
        try {
            $override = AdminCategoryOverride::findOrFail($id);
            $override->enabled = !$override->enabled;
            $override->save();
            return response()->json([
                'status'  => 't-success',
                'message' => 'Status updated successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 't-error',
                'message' => 'Something went wrong! ' . $e->getMessage()
            ], 500);
        }
    }
}
