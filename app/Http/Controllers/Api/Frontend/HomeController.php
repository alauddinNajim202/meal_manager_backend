<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Enums\PageEnum;
use App\Enums\SectionEnum;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\CMS;
use App\Models\Favourite;
use App\Models\Property;
use App\Models\Setting;
use App\Traits\CMSData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    use CMSData;
    public function index()
    {
        $data = [];

        $cmsData = CMS::all()->makeHidden(['created_at', 'updated_at']);

        $data['home_example']  = $cmsData->where('page', PageEnum::HOME)->where('section', SectionEnum::EXAMPLE)->first();
        $data['home_examples'] = $cmsData->where('page', PageEnum::HOME)->where('section', SectionEnum::EXAMPLES)->values();
        $data['home_about']    = $cmsData->where('page', PageEnum::HOME)->where('section', SectionEnum::ABOUT)->first();
        $data['common']        = $cmsData->where('page', PageEnum::COMMON);

        $data['settings'] = Setting::first();

        return Helper::jsonResponse(true, 'Home Page', 200, $data);
    }

    public function footer()
    {
        $cmsData = CMS::all()->makeHidden(['created_at', 'updated_at']);

        $footer = $cmsData->where('page', PageEnum::COMMON)->where('section', SectionEnum::FOOTER)->first();

        $data = [
            'description' => $footer->description ?? null,
            'twitter'     => $footer->metadata['twitter'] ?? null,
            'linkedin'    => $footer->metadata['linkedin'] ?? null,
        ];

        return Helper::jsonResponse(true, 'Footer Data', 200, $data);
    }

    public function divisions(Request $request)
    {
        $data = [];

        $type = $request->get('type');

        if ($type === 'division') {
            $data = DB::table('divisions')->get();
        } elseif ($type === 'district') {
            // If division_id provided, return districts for that division (dependent)
            if ($request->has('division_id')) {
                $data = DB::table('districts')->where('division_id', $request->division_id)->get();
            } else {
                $data = DB::table('districts')->get();
            }
        } elseif ($type === 'upazila') {
            // If district_id provided, return upazilas for that district (dependent)
            if ($request->has('district_id')) {
                $data = DB::table('upazilas')->where('district_id', $request->district_id)->get();
            } else {
                $data = DB::table('upazilas')->get();
            }
        }

        return Helper::jsonResponse(true, 'Locations retrieved', 200, $data);
    }

    public function propertyList(Request $request)
    {
        $latitude  = $request->latitude;
        $longitude = $request->longitude;

        // Main Query
        $query = Property::with([
            'category:id,name,slug',
        ])->select(
            'id',
            'category_id',
            'title',
            'slug',
            'thumbnail',
            'rent_amount',
            'beds',
            'baths',
            'size_sqft',
            'address',
            'latitude',
            'longitude',
            'created_at'
        )->latest();

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('month_id')) {
            $query->where('month_id', $request->month_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('upazila_id')) {
            $query->where('upazila_id', $request->upazila_id);
        }

        if ($request->filled('price_min')) {
            $query->where('rent_amount', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('rent_amount', '<=', $request->price_max);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('rent_amount', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->sort_order ?? 'asc');
        }

        $properties = $query->get();

        // Favourite
        $user = auth('api')->user();

        $favoritedPropertyIds = [];

        if ($user) {
            $favoritedPropertyIds = Favourite::where('user_id', $user->id)
                ->pluck('property_id')
                ->toArray();
        }

        $properties->each(function ($property) use ($favoritedPropertyIds) {
            $property->is_favorited = in_array($property->id, $favoritedPropertyIds);
        });

        // Nearest Properties
        $nearestProperties = collect();

        if (! empty($latitude) && ! empty($longitude)) {

            $nearestQuery = Property::with([
                'category:id,name,slug',
            ])
                ->select(
                    'id',
                    'category_id',
                    'title',
                    'slug',
                    'thumbnail',
                    'rent_amount',
                    'beds',
                    'baths',
                    'size_sqft',
                    'address',
                    'latitude',
                    'longitude',
                    'created_at'
                )
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->selectRaw("
                (
                    6371 * acos(
                        cos(radians(?))
                        * cos(radians(latitude))
                        * cos(radians(longitude) - radians(?))
                        + sin(radians(?))
                        * sin(radians(latitude))
                    )
                ) AS distance
            ", [$latitude, $longitude, $latitude]);

            // Same Filters
            if ($request->filled('type')) {
                $nearestQuery->where('type', $request->type);
            }

            if ($request->filled('month_id')) {
                $nearestQuery->where('month_id', $request->month_id);
            }

            if ($request->filled('category_id')) {
                $nearestQuery->where('category_id', $request->category_id);
            }

            if ($request->filled('division_id')) {
                $nearestQuery->where('division_id', $request->division_id);
            }

            if ($request->filled('district_id')) {
                $nearestQuery->where('district_id', $request->district_id);
            }

            if ($request->filled('upazila_id')) {
                $nearestQuery->where('upazila_id', $request->upazila_id);
            }

            if ($request->filled('price_min')) {
                $nearestQuery->where('rent_amount', '>=', $request->price_min);
            }

            if ($request->filled('price_max')) {
                $nearestQuery->where('rent_amount', '<=', $request->price_max);
            }

            if ($request->filled('search')) {
                $search = $request->search;

                $nearestQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('rent_amount', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($qq) use ($search) {
                            $qq->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $nearestProperties = $nearestQuery
                ->having('distance', '<=', 10)
                ->orderBy('distance', 'asc')
                ->limit(5)
                ->get();

            $nearestProperties->each(function ($property) use ($favoritedPropertyIds) {
                $property->is_favorited = in_array($property->id, $favoritedPropertyIds);
            });
        }

        $data = [
            'total_properties'           => $properties->count(),

            'total_favorited_properties' => $properties->where('is_favorited', true)->count(),

            'today_properties'           => $properties->filter(function ($property) {
                return optional($property->created_at)->isToday();
            })->count(),

            'featured_properties'        => $properties
                ->sortByDesc('created_at')
                ->take(5)
                ->values(),

            'nearest_properties'         => $nearestProperties,
        ];

        return Helper::jsonResponse(
            true,
            'Properties retrieved successfully',
            200,
            $data
        );
    }

    public function propertyDetails($id)
    {
        $property = Property::with('category', 'division', 'district', 'upazila', 'images')->find($id);

        if ($property) {
            $user = auth('api')->user();
            if ($user) {
                $property->is_favorited = \App\Models\Favourite::where('user_id', $user->id)->where('property_id', $property->id)->exists();
            } else {
                $property->is_favorited = false;
            }
        }

        return Helper::jsonResponse(true, 'Property details retrieved successfully ', 200, $property);
    }

    public function facilities()
    {
        $facilities = DB::table('facilities')->get();

        return Helper::jsonResponse(true, 'Facilities retrieved successfully', 200, $facilities);
    }

    public function featuredProperties(Request $request)
    {
        $latitude  = $request->latitude;
        $longitude = $request->longitude;

        $user = auth('api')->user();

        $favoritedPropertyIds = [];

        if ($user) {
            $favoritedPropertyIds = Favourite::where('user_id', $user->id)
                ->pluck('property_id')
                ->toArray();
        }

        $query = Property::with([
            'category:id,name,slug',
        ])
            ->select(
                'id',
                'category_id',
                'title',
                'slug',
                'thumbnail',
                'rent_amount',
                'beds',
                'baths',
                'size_sqft',
                'address',
                'latitude',
                'longitude',
                'created_at'
            );

        if (! empty($latitude) && ! empty($longitude)) {
            $query->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->selectRaw("
                (
                    6371 * acos(
                        cos(radians(?))
                        * cos(radians(latitude))
                        * cos(radians(longitude) - radians(?))
                        + sin(radians(?))
                        * sin(radians(latitude))
                    )
                ) AS distance
            ", [$latitude, $longitude, $latitude])
                ->having('distance', '<=', 10)
                ->orderBy('distance', 'asc')
                ->limit(5);
        } else {
            $query->latest();
        }

        $nearestProperties = $query->get();

        $nearestProperties->each(function ($property) use ($favoritedPropertyIds) {
            $property->is_favorited = in_array($property->id, $favoritedPropertyIds);
        });

        $nearestProperties->each(function ($property) use ($favoritedPropertyIds) {

            $property->is_favorited = in_array($property->id, $favoritedPropertyIds);

            if (isset($property->distance)) {

                if ($property->distance < 1) {
                    $property->distance = round($property->distance * 1000) . ' m';
                } else {
                    $property->distance = round($property->distance, 2) . ' km';
                }
            }
        });

        return Helper::jsonResponse(
            true,
            'Properties retrieved successfully',
            200,
            $nearestProperties
        );
    }

}
