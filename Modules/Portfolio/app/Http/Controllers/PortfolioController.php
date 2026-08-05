<?php
namespace Modules\Portfolio\Http\Controllers;

use App\Http\Controllers\Controller;

class PortfolioController extends Controller
{
    public function index()
    {
        return response()->json([
            'code' => 200,
            'message' => 'Portfolio list retrieved successfully',
            'data' => []
        ]);
    }  
}