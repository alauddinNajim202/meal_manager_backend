<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class Helper
{
    //! File or Image Upload
    public static function fileUpload($file, string $folder, string $name = ''): ?string
    {
        if (!$file || !is_object($file) || !method_exists($file, 'isValid') || !$file->isValid()) {
            Log::error("Helper::fileUpload - Uploaded file is invalid or null: " . ($file ? $file->getErrorMessage() : 'null'));
            return null;
        }

        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $imageName = time() . '-' . Str::random(10) . '.' . $extension;
        $path      = public_path('uploads/' . trim($folder, '/'));

        if (!file_exists($path)) {
            if (!@mkdir($path, 0777, true) && !is_dir($path)) {
                Log::error("Helper::fileUpload - Could not create directory: {$path}. Check write permissions.");
                throw new Exception("Unable to create directory [{$path}]. Please check write permissions on public/uploads.");
            }
        }

        @chmod($path, 0777);

        $file->move($path, $imageName);

        return 'uploads/' . trim($folder, '/') . '/' . $imageName;
    }

    //! File or Image Delete
    public static function fileDelete(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path = public_path(ltrim($parsed, '/'));
        }

        if (file_exists($path) && is_file($path)) {
            @unlink($path);
        }
    }

    //! Generate Slug
    public static function makeSlug($model, string $title): string
    {
        $slug = Str::slug($title);
        while ($model::where('slug', $slug)->exists()) {
            $randomString = Str::random(5);
            $slug         = Str::slug($title) . '-' . $randomString;
        }
        return $slug;
    }

    //! JSON Response
    public static function jsonResponse(bool $status, string $message, int $code, $data = null, bool $paginate = false, $paginateData = null): JsonResponse
    {
        $response = [
            'status'  => $status,
            'message' => $message,
            'code'    => $code,
        ];
        if ($paginate && !empty($paginateData)) {
            $response['data'] = $data;
            $response['pagination'] = [
                'current_page' => $paginateData->currentPage(),
                'last_page' => $paginateData->lastPage(),
                'per_page' => $paginateData->perPage(),
                'total' => $paginateData->total(),
                'first_page_url' => $paginateData->url(1),
                'last_page_url' => $paginateData->url($paginateData->lastPage()),
                'next_page_url' => $paginateData->nextPageUrl(),
                'prev_page_url' => $paginateData->previousPageUrl(),
                'from' => $paginateData->firstItem(),
                'to' => $paginateData->lastItem(),
                'path' => $paginateData->path(),
            ];
        } elseif ($paginate && !empty($data)) {
            $response['data'] = $data->items();
            $response['pagination'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'first_page_url' => $data->url(1),
                'last_page_url' => $data->url($data->lastPage()),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'path' => $data->path(),
            ];
        } elseif ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public static function jsonErrorResponse($message = null, $code = null, $errors = 'Error'): JsonResponse
    {
        $arr = $message;
        $arryerrors = [];

       if(is_array($message)){
            foreach ($arr  as $key => $value) {
            $arryerrors[] = [
                'field'   => $key,
                'message' => $value[0], // first error only
            ];
        }
       }
       else{


       if($code == 401){
        $errors ='Your session has expired. Please sign in again.';
       }elseif($code == 403){
        $errors = 'Forbidden';
       }elseif($code == 404){
        $errors = 'Not Found';
       }elseif($code == 500){
        $errors = $message ;
       }


       }

        $response = [
            'status'  => false,
            'code'    => $code,
            'message' => $errors,
            'errors' => $arryerrors,
        ];
        return response()->json($response, $code);
    }

    public static function sendNotifyMobile($token, $notifyData): void
    {
        try {
            $factory = (new Factory)->withServiceAccount(storage_path(config('firebase.projects.app.credentials') ?? env('FIREBASE_CREDENTIALS')));
            $messaging = $factory->createMessaging();
            $notification = Notification::create($notifyData['title'], Str::limit($notifyData['body'], 100), $notifyData['icon']);
            $message = CloudMessage::withTarget('token', $token)->withNotification($notification);
            $messaging->send($message);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
        return;
    }

    /* public static function Translate($model, $str){
        $data = $model::where('key', $str)->first();
        if($data){
            return $data->value;
        }
        return $str;
    } */
}
