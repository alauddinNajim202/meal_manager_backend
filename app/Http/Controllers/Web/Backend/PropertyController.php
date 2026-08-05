<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller
{
    public function __construct()
    {
        View::share('crud', 'property');
    }

    public function index(Request $request)
    {
        $totalIncome = Property::sum('rent_amount');
        $totalProperties = Property::count();
        $unitSold = Property::where('status', 'active')->count();
        $unitRent = Property::where('status', 'pending')->count();

        if ($request->ajax()) {
            $data = Property::with(['user', 'category', 'division', 'district'])->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('title', function ($data) {
                    $thumb = $data->thumbnail ? asset($data->thumbnail) : asset('default/logo.svg');
                    return '<div class="d-flex align-items-center">
                                <img src="'.$thumb.'" alt="thumbnail" width="40" height="40" class="me-2 rounded">
                                <div>
                                    <h6 class="mb-0">'.\Illuminate\Support\Str::limit($data->title, 30).'</h6>
                                    <small class="text-muted">Type: '.($data->rent_type ?? 'monthly').'</small>
                                </div>
                            </div>';
                })
                ->addColumn('user', function ($data) {
                    return $data->user ? $data->user->name : 'N/A';
                })
                ->addColumn('category', function ($data) {
                    return $data->category ? $data->category->name : 'N/A';
                })
                ->addColumn('location', function ($data) {
                    $dist = $data->district ? $data->district->name_en : '';
                    $div = $data->division ? $data->division->name_en : '';
                    return $dist . ($dist && $div ? ', ' : '') . $div;
                })
                ->addColumn('for_whom', function ($data) {
                    return ucfirst($data->for_whom ?? 'Any');
                })
                ->addColumn('beds_baths', function ($data) {
                    return '<div class="d-flex align-items-center gap-3">
                                <span><i class="fa-solid fa-bed text-muted me-1"></i> '.($data->beds ?? 0).'</span>
                                <span><i class="fa-solid fa-bath text-muted me-1"></i> '.($data->baths ?? 0).'</span>
                            </div>';
                })
                ->addColumn('rent_amount', function ($data) {
                    return '$' . number_format($data->rent_amount, 2);
                })
                ->addColumn('status', function ($data) {
                    $badgeClass = 'bg-warning-transparent text-warning';
                    if ($data->status == 'active') $badgeClass = 'bg-success-transparent text-success';
                    if ($data->status == 'inactive') $badgeClass = 'bg-danger-transparent text-danger';
                    return '<span class="badge rounded-pill ' . $badgeClass . '">' . ucfirst($data->status) . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $approveBtn = '';
                    if ($data->status == 'pending') {
                        $approveBtn = '<a href="javascript:void(0)" onclick="updateStatus('.$data->id.', \'active\')" class="btn btn-sm btn-outline-success mx-1" title="Approve"><i class="fe fe-check"></i></a>';
                    }
                    return '<div class="btn-group btn-group-sm" role="group">
                                '.$approveBtn.'
                                <a href="javascript:void(0)" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-outline-danger" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['status', 'action', 'title', 'beds_baths'])
                ->make(true);
        }
        return view('backend.layouts.property.index', compact('totalIncome', 'totalProperties', 'unitSold', 'unitRent'));
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $property = Property::findOrFail($id);
            $property->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Property deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete property.'
            ]);
        }
    }

    public function statusUpdate(Request $request, $id): JsonResponse
    {
        try {
            $property = Property::findOrFail($id);
            $property->status = $request->status;
            $property->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update status.'
            ]);
        }
    }
}
