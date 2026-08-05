<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Enums\CacheEnum;
use App\Enums\PageEnum;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\CMS;
use App\Models\Post;
use App\Models\Product;
use App\Models\SocialLink;
use Modules\Portfolio\Models\Project;
use Modules\Portfolio\Models\Type;
use App\Traits\CMSData;

class HomeController extends Controller
{
    use CMSData;

    public $theme;

    public function __construct()
    {
        $this->theme = env('THEME');
    }

    public function index()
    {
        //CMS Data
        $cmsData = CMSData::all()->makeHidden(['created_at', 'updated_at']);



        $cms = [
            'home' => $cmsData->where('page', PageEnum::HOME),
            'common' => $cmsData->where('page', PageEnum::COMMON),
        ];

        //social links
        $socials = Cache::rememberForever(CacheEnum::CMS_SOCIAL_LINKS, function () {
            return SocialLink::where('status', 'active')->get();
        });

        $posts = Post::with(['category', 'subcategory', 'user', 'images'])->where('status', 'active')->latest()->limit(3)->get();

        $types = Type::where('status', 'active')->get();


        // return view("frontend.{$this->theme}.layouts.home.index", compact('cms', 'posts', 'types', 'projects', 'products', 'socials'));

        return view('welcome');
    }

    public function post($slug)
    {
        //CMS Data
        $cmsData = CMSData::all()->makeHidden(['created_at', 'updated_at']);
        $cms = [
            'home' => $cmsData->where('page', PageEnum::HOME)->where('status', 'active')->get(),
            'common' => $cmsData->where('page', PageEnum::COMMON)->where('status', 'active')->get(),
        ];

        $post = Post::where('slug', base64_decode($slug))->where('status', 'active')->firstOrFail();
        return view('frontend.default.layouts.post', compact('cms', 'post'));
    }
}
