<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public $theme;

    public function __construct()
    {
        $this->theme = env('THEME');
    }
    
    public function index($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        return view("frontend.{$this->theme}.layouts.page", compact('page'));
    }

     public function privacyPolicy()
    {
        $privacy = Page::where('slug', 'privacy-policy')
            ->where('status', 'active')
            ->first();
  

        return view('frontend.default.privacy_policy', compact('privacy'));
    }





}
