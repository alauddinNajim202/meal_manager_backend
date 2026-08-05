<?php

namespace Modules\Portfolio\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Modules\Portfolio\Models\Type;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Type::orderBy('id', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('icon', function ($data) {
                    $url = asset($data->icon && file_exists(public_path($data->icon)) ? $data->icon : 'default/logo.svg');
                    return '<img src="' . $url . '" alt="image" style="width: 50px; max-height: 100px; margin-left: 20px;">';
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

                                <a href="#" type="button" onclick="goToEdit(' . $data->id . ')" class="btn btn-primary fs-14 text-white delete-icn" title="Delete">
                                    <i class="fe fe-edit"></i>
                                </a>

                                <a href="#" type="button" onclick="goToOpen(' . $data->id . ')" class="btn btn-success fs-14 text-white delete-icn" title="Delete">
                                    <i class="fe fe-eye"></i>
                                </a>

                                <a href="#" type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['icon', 'status', 'action'])
                ->make();
        }
        return view('portfolio::backend.layouts.type.index');
    }

    public function create()
    {
        return view('portfolio::backend.layouts.type.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|max:50',
            'icon'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = $validator->validated();

            $type = new Type();

            if ($request->hasFile('icon')) {
                $data['icon'] = Helper::fileUpload($request->file('icon'), 'type', time() . '_' . getFileName($request->file('icon')));
            }

            $type->slug = Helper::makeSlug(Type::class, $data['name']);

            $type->name = $data['name'];
            $type->icon = $data['icon'];
            $type->save();

            session()->put('t-success', 'Created successfully');

        } catch (Exception $e) {

            session()->put('t-error', $e->getMessage());
        }

        return redirect()->route('admin.type.index')->with('t-success', 'Created successfully');
    }

    public function show($id)
    {
        $type = Type::findOrFail($id);
        return view('portfolio::backend.layouts.type.show', compact('type'));
    }

    public function edit($id)
    {
        $type = Type::findOrFail($id);
        return view('portfolio::backend.layouts.type.edit', compact('type'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|max:50',
            'icon'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = $validator->validated();

            $type = Type::findOrFail($id);

            if ($request->hasFile('icon')) {
                $validate['icon'] = Helper::fileUpload($request->file('icon'), 'type', time() . '_' . getFileName($request->file('icon')));
            }

            $type->name = $data['name'];
            $type->icon = $data['icon'] ?? $type->icon;
            $type->save();

            session()->put('t-success', 'Updated successfully');
        } catch (Exception $e) {

            session()->put('t-error', $e->getMessage());
        }

        return redirect()->route('admin.type.edit', $type->id)->with('t-success', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            $data = Type::findOrFail($id);

            if ($data->icon && file_exists(public_path($data->icon))) {
                Helper::fileDelete(public_path($data->icon));
            }

            $data->delete();
            return response()->json([
                'status' => 't-success',
                'message' => 'Your action was successful!'
            ]);

        } catch (Exception $e) {

            return response()->json([
                'status' => 't-error',
                'message' => 'Your action was successful!'
            ]);

        }
    }

    public function status(int $id): JsonResponse
    {
        $data = Type::findOrFail($id);
        if (!$data) {
            return response()->json([
                'status' => 't-error',
                'message' => 'Item not found.',
            ]);
        }
        $data->status = $data->status === 'active' ? 'inactive' : 'active';
        $data->save();
        return response()->json([
            'status' => 't-success',
            'message' => 'Your action was successful!',
        ]);
    }
}
