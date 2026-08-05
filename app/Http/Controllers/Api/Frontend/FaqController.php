<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\FAQ;

class FaqController extends Controller{
    public function index(){
        $faq = FAQ::where('status', 'active')->get();
        $data = [
            'faq' => $faq
        ];
        return Helper::jsonResponse(true, 'faq list', 200, $data);
    }
}