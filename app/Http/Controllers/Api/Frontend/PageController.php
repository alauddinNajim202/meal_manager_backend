<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Database\Seeders\PageSeeder;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::where('status', 'active')
            ->select('id', 'name', 'slug', 'title', 'icon', 'updated_at')
            ->get();

        if ($pages->isEmpty()) {
            (new PageSeeder)->run();
            $pages = Page::where('status', 'active')
                ->select('id', 'name', 'slug', 'title', 'icon', 'updated_at')
                ->get();
        }

        return Helper::jsonResponse(true, 'Pages fetched successfully', 200, $pages);
    }

    public function show($slug)
    {
        $normalizedSlug = str_replace(['_', ' '], '-', strtolower(trim($slug)));

        $slugMap = [
            'privacy'               => 'privacy-policy',
            'privacy-policy'        => 'privacy-policy',
            'privacy-and-policy'    => 'privacy-policy',
            'terms'                 => 'terms-and-conditions',
            'terms-conditions'      => 'terms-and-conditions',
            'terms-and-conditions'  => 'terms-and-conditions',
            'terms-and-condition'   => 'terms-and-conditions',
            'terms-of-service'      => 'terms-and-conditions',
            'about'                 => 'about-us',
            'about-us'              => 'about-us',
            'faq'                   => 'faq',
        ];

        $targetSlug = $slugMap[$normalizedSlug] ?? $normalizedSlug;

        $page = Page::where(function ($query) use ($slug, $normalizedSlug, $targetSlug) {
                $query->where('slug', $targetSlug)
                      ->orWhere('slug', $slug)
                      ->orWhere('slug', $normalizedSlug);
            })
            ->where('status', 'active')
            ->select('id', 'name', 'slug', 'title', 'icon', 'content', 'updated_at')
            ->first();

        // If not found in DB, auto-seed default pages on demand to prevent 404
        if (!$page && in_array($targetSlug, ['privacy-policy', 'terms-and-conditions', 'terms-of-service', 'about-us', 'faq'])) {
            (new PageSeeder)->run();

            $page = Page::where('slug', $targetSlug)
                ->where('status', 'active')
                ->select('id', 'name', 'slug', 'title', 'icon', 'content', 'updated_at')
                ->first();
        }

        if ($page) {
            return Helper::jsonResponse(true, 'Page found', 200, $page);
        }

        return Helper::jsonResponse(false, 'Page not found', 404);
    }
}
