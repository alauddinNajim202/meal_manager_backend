<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\CategoryService;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CategoriesController extends Controller
{
public function index(Request $request)
{
    if ($request->ajax()) {
        $categories = CategoryService::with('parent')
            ->select(['id', 'name', 'parent_id', 'image']);

        return DataTables::of($categories)
            ->addColumn('parent', function ($row) {
                return $row->parent ? $row->parent->name : '-';
            })
            ->addColumn('image', function ($row) {
                if ($row->image) {
                    return '<img src="' . url($row->image) . '" width="50">';
                }
                return '';
            })
            ->addColumn('action', function ($row) {
                $route = 'category-service';
                $edit = '<a href="' . route($route . '.edit', $row->id) . '" class="btn btn-warning btn-sm">Edit</a>';
                $delete = '
                    <form action="' . route($route . '.destroy', $row->id) . '" method="POST" style="display:inline-block;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button class="btn btn-danger btn-sm" onclick="return confirm(\'Delete this category?\')">Delete</button>
                    </form>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['image', 'action'])
            ->make(true);
    }

    $route = 'category-service';
    return view('categories.index', compact('route'));
}

    public function create()
    {
        try {
            $categories = CategoryService::all();
            $route = 'category-service';
            return view('categories.create', compact('categories', 'route'));
        } catch (\Exception $e) {
            Log::error('Category Create Error: '.$e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while opening create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'required|image',
                'parent_id' => 'nullable|exists:categories_service,id',
            ]);

            $category = new CategoryService();
            $category->name = $request->name;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = getFileName($file);
                $category->image = Helper::fileUpload($file, 'category_service', $fileName);
            }


            $category->parent_id = $request->parent_id;
            $category->save();

            return redirect()->route('category-service.index')->with('success', 'Category created successfully');
        } catch (\Exception $e) {
            Log::error('Category Store Error: '.$e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create category.'.$e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $category = CategoryService::with('parent')->findOrFail($id);
            return view('categories.show', compact('category'));
        } catch (\Exception $e) {
            Log::error('Category Show Error: '.$e->getMessage());
            return redirect()->back()->with('error', 'Category not found.');
        }
    }

    public function edit($id)
    {
        try {
            $category = CategoryService::findOrFail($id);
            $parents = CategoryService::where('id', '!=', $id)->get();
            $route = 'category-service';
            return view('categories.edit', compact('category', 'parents','route'));
        } catch (\Exception $e) {
            Log::error('Category Edit Error: '.$e->getMessage());
            return redirect()->back()->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = CategoryService::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image',
                'parent_id' => 'nullable|exists:categories_service,id',
            ]);

            $category->name = $request->name;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = getFileName($file);
                $category->image = Helper::fileUpload($file, 'category_service', $fileName);
            }

            $category->parent_id = $request->parent_id;
            $category->save();

            return redirect()->route('category-service.index')->with('success', 'Category updated successfully');
        } catch (\Exception $e) {
            Log::error('Category Update Error: '.$e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update category.');
        }
    }

    public function destroy($id)
    {
        try {
            $category = CategoryService::findOrFail($id);
            $category->delete();

            return redirect()->route('category-service.index')->with('success', 'Category deleted successfully');
        } catch (\Exception $e) {
            Log::error('Category Delete Error: '.$e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete category.');
        }
    }
}
