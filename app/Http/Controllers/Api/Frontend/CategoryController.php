<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::where('status','active')->get();

        return Helper::jsonResponse(true, 'Category', 200, $category);

    }
}
