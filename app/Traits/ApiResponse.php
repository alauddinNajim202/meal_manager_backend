<?php

namespace App\Traits;

trait ApiResponse
{
    public function success($data , $message = null , $code = 200)
    {
        return response()->json([
            'success' => true ,
            'message' => $message ,
            'data' => $data ,
            'code' => $code ,

        ], $code);
    }


    public function error($data , $message=null , $code =500)
    {
        $statusCode = (is_numeric($code) && (int)$code >= 100 && (int)$code <= 599) ? (int)$code : 500;

        return response()->json([
            'status' => false ,
            'message' => $message ,
            'data' => $data ,
            'code' => $statusCode
        ], $statusCode);
    }
}
