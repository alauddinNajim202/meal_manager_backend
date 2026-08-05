<?php
namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavouriteController extends Controller
{
    /**
     * Toggle favorite status of a property
     */
    public function toggleFavorite(Request $request)
    {
        $user = auth('api')->user();
        if (! $user) {
            return Helper::jsonErrorResponse('Unauthorized', 401);
        }

        $validator = Validator::make($request->all(), [
            'property_id' => 'required|integer|exists:properties,id',
        ]);

        if ($validator->fails()) {
            return Helper::jsonErrorResponse('Validation error', 422, $validator->errors()->toArray());
        }

        $propertyId = $request->property_id;

        $favorite = Favourite::where('user_id', $user->id)
            ->where('property_id', $propertyId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return Helper::jsonResponse(true, 'Property removed from favorites', 200, ['is_favorited' => false]);
        } else {
            Favourite::create([
                'user_id'     => $user->id,
                'property_id' => $propertyId,
            ]);
            return Helper::jsonResponse(true, 'Property added to favorites', 200, ['is_favorited' => true]);
        }
    }

    /**
     * Get list of favorited properties
     */
    public function favoritesList()
    {
        $user = auth('api')->user();
        if (! $user) {
            return Helper::jsonErrorResponse('Unauthorized', 401);
        }

        $favorites  = Favourite::where('user_id', $user->id)->pluck('property_id');
        $properties = Property::with([
            'category:id,name,slug',
        ])->whereIn('id', $favorites)
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
                'address'
            )
            ->get();

        $properties->each(function ($property) {
            $property->is_favorited = true;
        });

        return Helper::jsonResponse(true, 'Favorites retrieved successfully', 200, $properties);
    }
}
