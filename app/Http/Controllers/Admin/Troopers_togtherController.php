<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\create_session_counselor;
use App\Models\session_counselor;
use App\Models\session_category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Troopers_togtherController extends Controller
{
    public function index(){
        $counselor = User::where('status','2')->get();
        $categorys = Category::get();
        return view('admin.Troopers_togther.create_session',compact('counselor','categorys'));
    }

    public function store(Request $request){

        $counselorArr = array();
        $counselorArr['session_title'] = $request->session_title;
        $counselorArr['session_description'] = $request->session_description;
        $counselorArr['start_time'] = $request->start_time;
        $counselorArr['Duration'] = $request->duration;
        $counselorArr['session_type'] = $request->session_type;
        
        $create_session = create_session_counselor::create($counselorArr);

        if(!empty($create_session)){
            $multyCategory = $request->category_id;
            if($multyCategory){
                foreach ($multyCategory as $value) {
                  $counselorCategoryArray = array();
                  $counselorCategoryArray['category_id'] = $value;
                  $counselorCategoryArray['session_id'] = $create_session->id;
                  session_category::create($counselorCategoryArray);
                }
            }

            $multycounselor = $request->counselor_id;
            if($multycounselor){
                foreach ($multycounselor as $value) {
                  $multyfeature = array();
                  $multyfeature['counselor_id'] = $value;
                  $multyfeature['session_id'] = $create_session->id;
                  session_counselor::create($multyfeature);
                }
            }
        }
        return redirect()->back()->with('message', 'session create Successfuly...!');
    }

    public function view(){
        // $data = create_session_counselor::join('tam_session_category','tam_create_counselor_session.id','=','tam_session_category.session_id')->join('tam_session_counselor','tam_create_counselor_session.id','=','tam_session_counselor.session_id')->join('tam_category','tam_session_category.category_id','=','tam_category.id')->join('users','tam_session_counselor.counselor_id','=','users.id')->get();
        $data = create_session_counselor::all();
        return view('admin.Troopers_togther.view_session',compact('data'));
    }

    public function edit($id){
        $data = create_session_counselor::where('id',$id)->first();
        $categorys = Category::get();
        $counselor = User::where('status','2')->get();
        $selectdCategory = session_category::where('session_id',$id)->get();
        $selectdconselor = session_counselor::where('session_id',$id)->get();

        $multiconselor = array();
         foreach($selectdconselor as $value){
            $multiconselor[] = $value->counselor_id;
        }

        $multiCategory = array();
        foreach($selectdCategory as $value){
            $multiCategory[] = $value->category_id;
        }

        return view('admin.Troopers_togther.edit_session',compact('data','multiCategory','multiconselor','categorys','counselor'));
    }

    public function update(Request $request,$id){
        $counselorArr = array();
        $counselorArr['session_title'] = $request->session_title;
        $counselorArr['session_description'] = $request->session_description;
        $counselorArr['start_time'] = $request->start_time;
        $counselorArr['Duration'] = $request->duration;
        $counselorArr['session_type'] = $request->session_type;
        $create_session = create_session_counselor::where('id',$id)->update($counselorArr);

         session_category::where('session_id',$id)->delete();
         session_counselor::where('session_id',$id)->delete();

        if(!empty($create_session)){
            $multyCategory = $request->category_id;
            if($multyCategory){
                foreach ($multyCategory as $value) {
                  $counselorCategoryArray = array();
                  $counselorCategoryArray['category_id'] = $value;
                  $counselorCategoryArray['session_id'] = $id;
                  session_category::create($counselorCategoryArray);
                }
            }

            $multycounselor = $request->counselor_id;
            if($multycounselor){
                foreach ($multycounselor as $value) {
                  $multyfeature = array();
                  $multyfeature['counselor_id'] = $value;
                  $multyfeature['session_id'] = $id;
                  session_counselor::create($multyfeature);
                }
            }
        }
            return redirect()->back()->with('message', 'session update Successfuly...!');
    }

    public function delete($id){
        $data = create_session_counselor::find($id)->delete();
        $data = session_category::where('session_id',$id)->delete();
        $data = session_counselor::where('session_id',$id)->delete();
        return redirect()->back()->with('message', 'delete Successfuly...!');
    }


    public function history(){
        return view('admin.Troopers_togther.session_history');
    }

}
