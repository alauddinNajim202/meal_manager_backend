<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PlatformFeedback;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PlatformFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PlatformFeedback::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user_info', function ($row) {
                    if ($row->is_anonymous) {
                        return '<span class="badge bg-secondary">Anonymous</span>';
                    }
                    return $row->email ?? '<span class="badge bg-light text-dark">No Email Provided</span>';
                })
                ->addColumn('feedback_content', function ($row) {
                    return \Str::limit($row->feedback, 100);
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('M d, Y h:i A');
                })
                ->addColumn('action', function ($row) {
                    $viewBtn = '<button type="button" class="btn btn-sm btn-info text-white view-feedback" data-id="' . $row->id . '" data-feedback="' . htmlspecialchars($row->feedback) . '" data-email="' . htmlspecialchars($row->email ?? 'Anonymous') . '"><i class="fa fa-eye"></i> View</button>';
                    $deleteBtn = '<a href="javascript:void(0)" class="btn btn-sm btn-danger delete-feedback" data-id="' . $row->id . '"><i class="fa fa-trash"></i> Delete</a>';
                    return $viewBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action', 'user_info'])
                ->make(true);
        }

        return view('backend.layouts.feedback.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $feedback = PlatformFeedback::findOrFail($id);
        $feedback->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feedback deleted successfully.'
        ]);
    }
}
