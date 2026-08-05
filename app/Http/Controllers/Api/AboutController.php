<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CMS;
use App\Enums\PageEnum;
use App\Enums\SectionEnum;
use Illuminate\Http\JsonResponse;

class AboutController extends Controller
{
    /**
     * Get all content for the About Page.
     *
     * @return JsonResponse
     */
public function index(): JsonResponse
{
    $about = CMS::where('page', PageEnum::ABOUT)
        ->where('section', SectionEnum::ABOUT)
        ->first();

    $mission = CMS::where('page', PageEnum::ABOUT)
        ->where('section', SectionEnum::MISSION)
        ->first();

    $team = CMS::where('page', PageEnum::ABOUT)
        ->where('section', SectionEnum::TEAM)
        ->where('status', 'active')
        ->select('title', 'sub_title', 'image', 'updated_at')
        ->get();

    $updatedAt = collect([
        $about?->updated_at,
        $mission?->updated_at,
        $team->max('updated_at'),
    ])->filter()->max();

    return response()->json([
        'success' => true,
        'message' => 'About page content retrieved successfully.',
        'data' => [
            'updated_at' => $updatedAt ?? '',
            'about' => $about->description ?? '',
            'mission' => $mission->description ?? '',
            'team' => $team->map(function ($item) {
                return [
                    'name' => $item->title ?? '',
                    'role' => $item->sub_title ?? '',
                    'image' => $item->image ? asset($item->image) : '',
                ];
            }),
        ]
    ]);
}
}
