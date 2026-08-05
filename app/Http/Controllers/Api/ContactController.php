<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:50',
            'email'     => 'nullable|email|max:255',
            'phone'     => 'required|numeric|digits:11',
            'subject'   => 'nullable|string|max:255',
            'message'   => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                //'msg' => $validator->errors()
                'msg' => $validator->errors()->first()
            ]);
        }

        try {
            Contact::create($request->only('name', 'email', 'phone', 'subject', 'message'));
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'msg' => 'Message sent successfully'
        ]);
    }
}
