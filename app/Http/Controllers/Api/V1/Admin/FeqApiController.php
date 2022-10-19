<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feq;
use Illuminate\Http\Request;

class FeqApiController extends Controller
{
    public function feq_get(){
        $data = Feq::all();
        // dd($data);
        return response()->json([
            'success'=>true,
            'messege'=>'Feq_show',
            'data'=>$data
        ]);
    }
}