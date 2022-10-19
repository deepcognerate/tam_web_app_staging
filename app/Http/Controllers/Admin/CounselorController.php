<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCounselorRequest;
use App\Http\Requests\StoreCounselorRequest;
use App\Models\WaitingAssignments;
use App\Http\Requests\UpdateCounselorRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;

use App\Models\FcmToken;

use App\Models\AsyncChat;
use App\Models\CounselorCategoryUser;
use App\Models\CounselorAssignment;
use App\Models\CounselorPastCases;
use App\Models\CounsellorCategories;
use Gate;
// use DB;
use Auth;
use Session;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


// New code 
use App\Models\ChatSessions;
use App\Models\ChatAudit;
use App\Models\ChatMessages;
use App\Models\Feature;
use App\Models\ChatFeature;
use App\Models\FcmCaredentials;


class CounselorController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('counselor_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::with(['roles','category'])->get();
        $sessionCounselorid = Auth::user()->id;

        $categorys = Category::get();      


        if($sessionCounselorid == 1){
            $counselors = User::with(['roles','category'])->where('status','2')->orderBy('created_at','desc')->get();
        } else {
            $counselors = User::with(['roles','category'])->where('id',$sessionCounselorid)->where('status','2')->get();
        }
        $categorys = Category::get();
        $counselorsDataAll = array();

        if(!empty($counselors)){
            foreach ($counselors as $key => $value) {
                $multipalCategorySelected = '';
                $selectdCategory = CounsellorCategories::where('counsellor_id',$value->id)
                                                ->whereNull('deleted_at')
                                                ->get();

                if(!empty($selectdCategory)){
                    $multiCategory = array();
                    foreach ($selectdCategory as $key_ => $value_) {
                        $multiCategory[] = $value_->category_id;
                    }

                   
                    if(!empty($multiCategory)){
                      foreach ($categorys as  $valueCategory) {
                         if(in_array($valueCategory->id , $multiCategory)){
                            $multipalCategorySelected .=$valueCategory->category_name.' ,';
                         }
                      }
                    $value->category_id = $multipalCategorySelected;   
                    }
                }  

              
              $counselorsDataAll[] = $value;  
            }
            
        }
       
        return view('admin.counselors.index', compact('categorys','counselorsDataAll','sessionCounselorid'));
    }

    public function create()
    {
        abort_if(Gate::denies('counselor_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles = Role::all()->pluck('title', 'id');
        $categorys = Category::get();
         $feature = Feature::get();
        return view('admin.counselors.create', compact('feature','roles','categorys'));
    }

    public function store(StoreCounselorRequest $request)
    {
        try{
        DB::beginTransaction();
        $counselorArr = array();
        $counselorArr['name'] = $request->name;
        $counselorArr['email'] = $request->email;
        $counselorArr['phone_no'] = $request->phone_no;
        $counselorArr['password'] = $request->password;
        $counselorArr['status'] = '2';

        $user = User::create($counselorArr);

        if(!empty($user)){
            $multyCategory = $request->category_id;
            if($multyCategory){
                foreach ($multyCategory as $value) {
                  $counselorCategoryArray = array();
                  $counselorCategoryArray['category_id'] = $value;
                  $counselorCategoryArray['counsellor_id'] = $user->id;
                  CounsellorCategories::create($counselorCategoryArray);
                }
            }

            $multyfeature = $request->feature_id;
            if($multyfeature){
                foreach ($multyfeature as $value) {
                  $multyfeature = array();
                  $multyfeature['feature_id'] = $value;
                  $multyfeature['counsellor_id'] = $user->id;
                  ChatFeature::create($multyfeature);
                }
            }
        }
        
        $user->roles()->sync($request->input('roles', ['3']));
        
        Session::flash('message', 'Counsellor Added Successfuly...!');
        DB::commit();
        return redirect()->route('admin.counselors.index');
     } catch(\Exception $e)  {
          Log::error($e);
          DB::rollBack();
          Session::flash('message', 'Counsellor Not Added Successfuly...!');
         return redirect()->route('admin.counselors.index');
     }
    }

    public function edit(User $counselor)
    {
        abort_if(Gate::denies('counselor_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categorys = Category::get();
        $feature = Feature::get();
        $selectdCategory = CounsellorCategories::where('counsellor_id',$counselor->id)->whereNull('deleted_at')->get();

        $selectdFeature = ChatFeature::where('counsellor_id',$counselor->id)->whereNull('deleted_at')->select('feature_id')->get();
        
        $multiFeature = array();
         foreach($selectdFeature as $value){
            $multiFeature[] = $value->feature_id;
        }

        $multiCategory = array();
        foreach($selectdCategory as $value){
            $multiCategory[] = $value->category_id;
        }
        return view('admin.counselors.edit', compact('feature','multiFeature','counselor','categorys','multiCategory'));
    }

    public function update(UpdateCounselorRequest $request, User $counselor)
    {
         try{
        DB::beginTransaction();

        $counselorArr = array();
        $counselorArr['name'] = $request->name;
        // $counselorArr['category_id'] = $request->category_id;
        $counselorArr['email'] = $request->email;
        $counselorArr['phone_no'] = $request->phone_no;
        $counselorArr['password'] = $request->password;  
        $counselorArr['status'] = 2;
        $counselor->update($counselorArr);

        CounsellorCategories::where('counsellor_id',$counselor->id)->delete();
        ChatFeature::where('counsellor_id',$counselor->id)->delete();
        
        $multyCategory = $request->category_id;
        if($multyCategory){
            foreach ($multyCategory as $value) {
                $counselorCategoryArray = array();
                $counselorCategoryArray['category_id'] = $value;
                $counselorCategoryArray['counsellor_id'] = $counselor->id;
                CounsellorCategories::create($counselorCategoryArray);
            }
        }   

        $multyfeature = $request->feature_id;
        if($multyfeature){
            foreach ($multyfeature as $value) {
                $multyfeature = array();
                $multyfeature['feature_id'] = $value;
                $multyfeature['counsellor_id'] = $counselor->id;
                ChatFeature::create($multyfeature);
            }
        }        

        DB::commit();
        Session::flash('message', 'Counsellor Updated Successfuly...!'); 
        return redirect()->route('admin.counselors.index');
        } catch(\Exception $e)  {
          Log::error($e);
          DB::rollBack();
          Session::flash('message', 'Counsellor Not Updated ..!'); 
         return redirect()->route('admin.counselors.index');
        }
    }

    public function show(User $counselor)
    {
        abort_if(Gate::denies('counselor_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $counselor->load('roles');
        $categorys = Category::get();
        $feature = Feature::get();

        $selectdCategory = CounsellorCategories::where('counsellor_id',$counselor->id)->whereNull('deleted_at')->get();

        $selectdFeature = ChatFeature::where('counsellor_id',$counselor->id)->whereNull('deleted_at')->select('feature_id')->get();

        $multiFeature = array();
         foreach($selectdFeature as $value){
            $multiFeature[] = $value->feature_id;
        }

        $multiCategory = array();
        foreach($selectdCategory as $value){
            $multiCategory[] = $value->category_id;
        }

        return view('admin.counselors.show', compact('counselor','categorys','feature','multiFeature','multiCategory'));
    }

    public function destroy(User $counselor)
    {
        abort_if(Gate::denies('counselor_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $counselor->delete();

        return back();
    }

    public function massDestroy(MassDestroyCounselorRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function mychatAdmin()
    {
        $sessionCounselorid = Auth::user()->id;
        $categorys = Category::get();
        $counsellors = User::where('status','2')->whereNull('deleted_at')->get();
        return view('admin.counselors.mychat', compact('counsellors','sessionCounselorid','categorys'));
    }

    public function mychat($id)
    {
        $users = User::with(['roles','category'])->get();
        $sessionCounselorid = Auth::user()->id;
        if($sessionCounselorid == 1){
            $counselors = User::with(['roles','category'])->where('status','4')->get();
        }else{
            $counselors = User::with(['roles','category'])->where('id',$sessionCounselorid)->where('status','4')->get();
        }
        $categorys = Category::get();
        return view('admin.counselors.mychat', compact('counselors','sessionCounselorid','categorys'));
    }


    public function counselorAvailability($status)
    {
        $sessionCounselorid = Auth::user()->id;
        $counselorStatus = $status;
        if($sessionCounselorid != 1)
        {
            if($status==1)
                {
                    $counselorActive = User::where('id', $sessionCounselorid)
                                            ->where('status','2')
                                            ->update(['counselor_availability'=>1]);
                                            
                    Session::flash('message', 'Available for sessions'); 
                    $counselordata = User::where('id', $sessionCounselorid)
                                            ->where('status','2')
                                            ->whereNull('deleted_at')
                                            ->first();

                                            

                    $getCounselors  = User::where('category_id',$counselordata->category_id)
                                  ->where('status','2')
                                  ->where('counselor_availability','1')
                                  ->where('chat_availability','0')
                                  ->first();


                                  if(!empty($getCounselors))
                {
                    $checkCounselorAssignment = CounselorAssignment::where('category_id',$getCounselors->category_id)
                                                                    ->where('counselor_id',$getCounselors->id)
                                                                    ->whereNull('deleted_at')
                                                                    ->whereNull('report')
                                                                    ->first();
                        if(empty($checkCounselorAssignment))
                            {
                                $checkWattingList = WaitingAssignments::where('category_id',$getCounselors->category_id)->first();

                            if(!empty($checkWattingList))
                                {
                                    WaitingAssignments::where('id',$checkWattingList->id)->whereNull('deleted_at')->delete();   
                                    $userAssignment['counselor_id'] = $getCounselors->id;
                                    $userAssignment['user_id'] = $checkWattingList->user_id;
                                    $userAssignment['category_id'] = $checkWattingList->category_id;
                                    $userAssignment['chat_type'] = "Live";
                                    $userAssignment['chat_availability'] = '1';
                                    $checkCounselor = CounselorAssignment::create($userAssignment);

                                    $getCounselors->chat_availability = '1';
                                    $getCounselors->save();

                                    $key_ = "live_counsellor_assign";
                                    $body_ = 'Counsellor is assigned to your Live chat';
                                    FcmCaredentials::sendNotificationToUserChatClose($userId,$categoryId,$key_,$body_);



                                    // $response = ['response' => $checkCounselor,'message'=> 'User  assignment succsesfully..! ','status'=>true];
                                    // return response($response, 200);

                                             $waitingAssignmentList = WaitingAssignments::where('category_id',$checkWattingList->category_id)->whereNull('deleted_at')->get();
                                if(!empty($waitingAssignmentList)){
                                    foreach ($waitingAssignmentList as $waitingKey) {


                                    $key_ = "live_counsellor_assign";
                                    $body_ = 'Counsellor is assigned to your Live chat';
                                    FcmCaredentials::sendNotificationToUserChatClose($userId,$categoryId,$key_,$body_);

                                        
                                    }
                                }
                                }
                              
                        }        
                    }         
            }else
            {
                $counselorActive = User::where('id', $sessionCounselorid)
                                        ->where('status','2')
                                        ->update(['counselor_availability'=>0]);
                Session::flash('message', 'Not available for sessions');                  
            }
            return $counselorActive; 
        }
        
    }

  
    
}
