<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
   

    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'active')->select('title',  'content','updated_at')
            ->first();

        if ($page) {
            return Helper::jsonResponse(true, 'Page found', 200, $page);
        }

        return Helper::jsonResponse(false, 'Page not found', 404);
    }


   


   






}
