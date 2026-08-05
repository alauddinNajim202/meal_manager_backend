<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PrayerTimesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrayerTimesController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat'      => 'required|numeric|between:-90,90',
            'lng'      => 'required|numeric|between:-180,180',
            'timezone' => 'required|numeric|between:-12,14',
            'date'     => 'nullable|date_format:Y-m-d',
            'method'   => 'nullable|integer|between:0,5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $lat      = $request->input('lat');
        $lng      = $request->input('lng');
        $timezone = $request->input('timezone');
        $date     = $request->input('date', date('Y-m-d'));
        $method   = $request->input('method', PrayerTimesService::MWL);

        $service = new PrayerTimesService($lat, $lng, $timezone, $date, $method);
        $times   = $service->getPrayerTimes();

        return response()->json([
            'success' => true,
            'data'    => [
                'date'               => $date,
                'coordinates'        => [
                    'latitude'  => floatval($lat),
                    'longitude' => floatval($lng),
                ],
                'timezone'           => floatval($timezone),
                'calculation_method' => $this->getMethodName($method),
                'prayer_times'       => $times,
            ],
        ]);
    }

    public function today(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat'      => 'required|numeric|between:-90,90',
            'lng'      => 'required|numeric|between:-180,180',
            'timezone' => 'required|numeric|between:-12,14',
            'method'   => 'nullable|integer|between:0,5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $request->merge(['date' => date('Y-m-d')]);
        return $this->index($request);
    }

    public function methods()
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'methods' => [
                    ['id' => 0, 'name' => 'Jafari', 'description' => 'Ithna Ashari'],
                    ['id' => 1, 'name' => 'Karachi', 'description' => 'University of Islamic Sciences, Karachi'],
                    ['id' => 2, 'name' => 'ISNA', 'description' => 'Islamic Society of North America'],
                    ['id' => 3, 'name' => 'MWL', 'description' => 'Muslim World League (Default)'],
                    ['id' => 4, 'name' => 'Makkah', 'description' => 'Umm al-Qura, Makkah'],
                    ['id' => 5, 'name' => 'Egypt', 'description' => 'Egyptian General Authority of Survey'],
                ],
            ],
        ]);
    }

    private function getMethodName($method)
    {
        $methods = [
            0 => 'Jafari',
            1 => 'Karachi',
            2 => 'ISNA',
            3 => 'MWL',
            4 => 'Makkah',
            5 => 'Egypt',
        ];

        return $methods[$method] ?? 'MWL';
    }
}
