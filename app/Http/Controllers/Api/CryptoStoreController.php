<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CryptoStore;
use App\Models\CryptoStoreRating;
use App\Helpers\Helper;
use App\Models\CMS;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Support\Facades\DB;

class CryptoStoreController extends Controller
{
    public function index(Request $request)
    {
        try {
            $page    = $request->input('current_page', 1);
            $perPage = $request->input('per_page', 10);
            $type    = $request->type;

            if (is_string($type)) {
                $type = array_filter(explode(',', $type));
            }
            $type = (array) $type;

            $query = CryptoStore::withCount('ratings')
                ->withAvg('ratings as avg_rating', 'rating'); // ✅ add this

            if (!empty($type)) {
                $query->whereIn('type', $type);
            }

            $stores = $query->orderByDesc('id')
                ->paginate($perPage, ['*'], 'page', $page);

            $data = $stores->getCollection()->map(function ($store) {
                return [
                    'id'             => $store->id,
                    'name'           => $store->name,
                    'slug'           => $store->slug,
                    'type'           => $store->type,
                    'short_description'    => $store->short_description,
                    'image'          => $store->image ? asset($store->image) : null,
                    'avg_rating'     => number_format($store->avg_rating ?? 0, 1), // ✅ fixed
                    'ratings_count'  => $store->ratings_count,
                ];
            });

            return response()->json([
                'status'  => true,
                'code'    => 200,
                'message' => 'Crypto Stores fetched successfully',
                'data'    => $data,
                'pagination' => [
                    'total_page'   => $stores->lastPage(),
                    'per_page'     => $stores->perPage(),
                    'total_item'   => $stores->total(),
                    'current_page' => $stores->currentPage(),
                ]
            ]);
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }
public function show($identifier)
{
    try {
        $store = CryptoStore::with([
                'expertises:id,crypto_store_id,name',
                'verification_audits:id,crypto_store_id,name',
                'supported_ecosystems:id,crypto_store_id,name'
            ])
            ->withCount('ratings')
            ->withAvg('ratings as avg_rating', 'rating')
            ->where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->first();

        if (!$store) {
            return Helper::jsonErrorResponse('Crypto Store not found', 404);
        }

        // ✅ Safe relation handling (correct names)
        if ($store->expertises) {
            $store->expertises->makeHidden(['crypto_store_id', 'created_at', 'updated_at']);
        }

        if ($store->verification_audits) {
            $store->verification_audits->makeHidden(['crypto_store_id', 'created_at', 'updated_at']);
        }

        if ($store->supported_ecosystems) {
            $store->supported_ecosystems->makeHidden(['crypto_store_id', 'created_at', 'updated_at']);
        }

        // ✅ Image URL fix
        $store->image = $store->image ? asset($store->image) : null;

        // ✅ Avg rating fix
        $store->avg_rating = number_format((float) ($store->avg_rating ?? 0), 1);

        // ✅ Optional cleanup
        $store->makeHidden(['ratings']);

        return response()->json([
            'status'  => true,
            'code'    => 200,
            'message' => 'Crypto Store fetched successfully',
            'data'    => $store
        ]);

    } catch (\Throwable $e) {
        return Helper::jsonErrorResponse(
            config('app.debug') ? $e->getMessage() : 'Internal server error',
            500
        );
    }
}

   public function rate(Request $request)
{
    try {
        DB::beginTransaction();

        $request->validate([
            'crypto_store_slug' => 'required|exists:crypto_stores,slug',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $user = auth()->user();

        // Find store by slug
        $store = CryptoStore::where('slug', $request->crypto_store_slug)->firstOrFail();

        $rating = CryptoStoreRating::updateOrCreate(
            [
                'user_id' => $user->id,
                'crypto_store_id' => $store->id
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment
            ]
        );

        DB::commit();

        return response()->json([
            'status'  => true,
            'code'    => 200,
            'message' => 'Rating submitted successfully',
            'data'    => $rating->load('user')
        ]);
    } catch (ValidationException $e) {
        DB::rollBack();
        return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
    } catch (Throwable $e) {
        DB::rollBack();
        return Helper::jsonErrorResponse(
            config('app.debug') ? $e->getMessage() : 'Internal server error',
            500
        );
    }
}

    public function ratings(Request $request, $identifier)
    {
        try {
            $store = CryptoStore::where('id', $identifier)
                ->orWhere('slug', $identifier)
                ->first();

            if (!$store) {
                return Helper::jsonErrorResponse('Crypto Store not found', 404);
            }

            $page    = $request->input('current_page', 1);
            $perPage = $request->input('per_page', 10);

            $ratings = CryptoStoreRating::with('user')->where('crypto_store_id', $store->id)->latest()->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'status'     => true,
                'code'       => 200,
                'message'    => 'Ratings fetched successfully',
                'data'       => $ratings->items(),
                'pagination' => [
                    'total_page'   => $ratings->lastPage(),
                    'per_page'     => $ratings->perPage(),
                    'total_item'   => $ratings->total(),
                    'current_page' => $ratings->currentPage(),
                ]
            ]);
        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function types()
    {
        try {
            $types = CryptoStore::select('type')
                ->whereNotNull('type')
                ->distinct()
                ->pluck('type');

            return response()->json([
                'status'  => true,
                'code'    => 200,
                'message' => 'Crypto Store types fetched successfully',
                'data'    => $types
            ]);
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function cms()
    {
        try {
            $data = CMS::where('page', 'crypto_store')->where('section', 'banner')->first();

            $data = [
                'id'          => $data->id,
                'title'       => $data->title,
                'description' => strip_tags($data->description),
                'image'       => $data->image ? asset($data->image) : null,
                'partners'    => CryptoStore::count() ?? null,
                'experience'  => (int) $data->metadata['experience'] ?? null,
            ];

            return response()->json([
                'status' => true,
                'message' => 'CMS data fetched successfully',
                'data' => $data,
                'code' => 200,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'No records found',
                'code' => 418,
            ], 418);
        }
    }
}
