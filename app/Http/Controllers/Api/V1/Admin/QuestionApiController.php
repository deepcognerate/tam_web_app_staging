<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class QuestionApiController extends Controller
{
    public function send(Request $r){
        // $user_id = Auth::user()->id;
        $datas = new Question();
        $datas->question=$r->question;
        $datas->user_id=$r->user_id;
        $datas->save();
        $data = Question::leftjoin('users','questions.user_id','=','users.id')->where('user_id',$r->user_id)->orderBy('questions.id','desc')->first();
        // dd($data);
        Mail::to('ankushkushwah2000@gmail.com')->send(new \App\Mail\Mymail($data));
        return response()->json([
            'success'=>true,
            'messege'=>'question_send',
            'data'=>$data
        ]);
    }
}
