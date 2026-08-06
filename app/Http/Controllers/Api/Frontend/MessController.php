<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class MessController extends Controller
{
    use ApiResponse;



    public function index() {
        try{
            $mess = Mess::all();
            return $this->success($mess, 'Mess list', 200);
        }catch(Exception $e){
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }




    public function create(Request $request) {
        
        try{

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors(), 'Validation failed', 422);
            }
            $mess = Mess::create($request->all());
            return $this->success($mess, 'Mess created successfully', 200);
        }catch(Exception $e){
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }





}
