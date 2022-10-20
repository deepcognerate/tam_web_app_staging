<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MassDestroyCounselorAssignmentRequest;
use App\Http\Requests\StoreCounselorAssignmentRequest;
use App\Http\Requests\UpdateCounselorAssignmentRequest;
use App\Models\CounselorAssignment;
use App\Models\WaitingAssignments;
use App\Models\CounselorCurrentCases;
use App\Models\CounselorCategoryUser;
use App\Models\Category;
use App\Models\User;
use App\Models\AsyncChat;
use Carbon\Carbon;
use App\Models\FcmToken;
use Session;
use Gate;
use Auth;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;


// New code 
use App\Models\ChatSessions;
use App\Models\CounsellorCategories;
use App\Models\ChatAudit;
use App\Models\ChatMessages;
use App\Models\Feature;
use App\Models\ChatFeature;
use App\Models\FcmCaredentials;
use Illuminate\Support\Facades\Log;

class CounselorAssignmentController extends Controller
{
    public function FcmServerKey(){
             return 'AAAA0yAqXOY:APA91bFx-9he2tSBX8bwjlnBHik0i-f_NhgsgaElzQQ0xDbefryv9G2dwAj0J-6lBhcMt14PWhIb0AfHXvaaW-V2NkE2rgTeLXDf5pbpAqvmmvvoVpYo73GfPsk4tYQo26s0c6p1pjLY';
    }


    public function index(Request $request)
    {
        abort_if(Gate::denies('counselorassignment_accsess'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $sessionCounselorid = Auth::user()->id;
        if($sessionCounselorid == 1){
            $waitingUsers = WaitingAssignments::with('getUser','getCategory')->get();

            $counselorassignment = CounselorAssignment::with('getUser','getCategory')->get();

            $counsellors = User::where('status','2')->whereNull('deleted_at')->get();

        }else{
            $users = User::with(['roles'])->where('id',$sessionCounselorid)->where('status','2')->get();

            $counselorassignments = User::with(['roles'])->where('id',$sessionCounselorid)->where('status','2')->get();
        }
        $categorys = Category::get();
        return view('admin.counselorassignments.index', compact('waitingUsers','categorys','counselorassignment','counsellors','sessionCounselorid'));
    }

    public function create()
    {
        abort_if(Gate::denies('counselorassignment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $counselors = User::with(['roles'])->where('status',2)->get();
        $users = User::with(['roles'])->get();
        $categorys = Category::get();
        return view('admin.counselorassignments.create',compact('users','categorys','counselors'));
    }

    public function store(StoreCounselorAssignmentRequest $request)
    {
        //dd($request->all());
        $counselorassignment = CounselorAssignment::create($request->all());

        return redirect()->route('admin.counselorassignments.index');
    }

    public function edit(CounselorAssignment $counselorassignment)
    {
        abort_if(Gate::denies('counselorassignment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.counselorassignments.edit', compact('category'));
    }

    public function update(UpdateCounselorAssignmentRequest $request, CounselorAssignment $counselorassignment)
    {
        $counselorassignment->update($request->all());

        return redirect()->route('admin.counselorassignments.index');
    }

    public function show(CounselorAssignment $counselorassignment)
    {
        abort_if(Gate::denies('counselorassignment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.counselorassignments.show', compact('counselorassignment'));
    }

    public function destroy(CounselorAssignment $counselorassignment)
    {
        abort_if(Gate::denies('counselorassignment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $counselorassignment->delete();

        return back();
    }

    public function massDestroy(MassDestroyCounselorAssignmentRequest $request)
    {
        CounselorAssignment::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }


    public function adminUserAssignToCounselor(Request $request)
    {
       
        $counselor_id = request()->get('counselor_id');
        $user_id = request()->get('user_id');
        $watting_id = request()->get('watting_id');
        $category_id = request()->get('category_id');

     
        $getCounselors = User::where('id',$counselor_id)
                            ->where('status','2')
                            ->where('counselor_availability','1')
                            ->where('chat_availability','0')
                            ->whereNull('deleted_at')
                            ->first();
       
       

        if(empty($getCounselors))
        {
            $output = ['success' => false,'data' => '','msg' => __("Counsellor is not available for Live chat...!")];
            return $output;
        }

        $getCounselorCategoryUsers = CounselorAssignment::where('user_id',$user_id)
                                                        ->where('counselor_id',$counselor_id)
                                                        ->where('category_id',$category_id)
                                                        ->where('chat_type','Live')
                                                        ->whereNull('deleted_at')
                                                        ->first();
       
        if(empty($getCounselorCategoryUsers))
        {
            $waitingAssignments = WaitingAssignments::where('id',$watting_id)
                                                ->where('user_id',$user_id)
                                                ->where('category_id',$category_id)
                                                ->whereNull('deleted_at')
                                                ->first();

            if(!empty($waitingAssignments))
            {
                    $userAssignment  = array();
                    $userAssignment['counselor_id'] = $getCounselors->id;
                    $userAssignment['user_id'] = $waitingAssignments->user_id;
                    $userAssignment['category_id'] = $waitingAssignments->category_id;
                    $userAssignment['chat_type'] = "Live";
                    $userAssignment['assign_by'] = "1";
                    $userAssignment['chat_availability'] = '1';
                    $checkCounselor = CounselorAssignment::create($userAssignment);

                    if(!empty($checkCounselor)){

                        // $key = "live_counsellor_assign_by_admin";
                        // $body = 'Your chat with The Able Mind is now active';
                        // FcmCaredentials::sendNotificationtoUser($waitingAssignments->user_id,$getCounselors->id,$key,$body);

                        // $this->sendNotificationLiveCounsellorToUserAssignToadmin($waitingAssignments->user_id,$getCounselors->id,'live_counsellor_assign_by_admin');


                        WaitingAssignments::where('id',$waitingAssignments->id)
                                            ->whereNull('deleted_at')
                                            ->delete();
                    
                        $date = Carbon::now();
                        $currentTime  = strtotime(date('H:i:s'));
                        $endTime = strtotime('09:00:59');
                        $interval = $currentTime + $endTime;
                        $actualTime = gmdate("H:i:s", $interval);

                        $userCurrentChat = array();
                        $userCurrentChat['category_id'] = $category_id;
                        $userCurrentChat['user_id'] = $user_id;
                        $userCurrentChat['message'] = "Live Chat";
                        $userCurrentChat['chat_type'] = '1';
                        $counselorCurrentChat = CounselorCurrentCases::create($userCurrentChat);
                    }

                    $output = ['success' => true,'data' => '','msg' => __("User assign to counsellor succsesfully...!")];
                    return $output;
            } 
            else 
            {
                $getCounselors = User::where('id',$counselor_id)->where('status','2')->first();
                $getCounselors->chat_availability = '1';
                $getCounselors->save();
            }

        } else {
           
            $output = ['success' => false,'data' => '','msg' => __("This counselor already assign.Please choose other counsellors")];
            return $output;
           
        }
    }






    public function userAssignToAdmin(Request $request)
    {  
        $current_time = Carbon::now();
        // $addHr = $current->addHours(10);
         $time = $current_time->toTimeString();

        $getUsers = CounselorCurrentCases::where('chat_type','0')
                                         ->where('admin_assign','0')
                                         ->where('time_left','>=',$time)
                                         ->whereNotNull('time_left')
                                         ->whereNull('deleted_at')
                                         ->get();
        if(!empty($getUsers))
        {
            $checkData = array();
            $current_time = Carbon::now();
            $start_time = strtotime($current_time);

            foreach($getUsers as $getUser)
            {
                $checkData['user_id'] = $getUser->user_id;
                $checkData['chat_type'] = $getUser->chat_type;
                $actual_time =  $getUser->time_left;
                $end_time = strtotime($actual_time);
                
                echo $actual_time;
                echo "<br>";
                echo $time;
                
                if($end_time <= $start_time)
                {   

                    $currentCase = array();
                     CounselorCurrentCases::where('user_id',$getUser->user_id)
                                                            ->where('category_id',$getUser->category_id)
                                                            ->update(['admin_assign' => 1]);

                    $getCurrentCase = CounselorCurrentCases::where('user_id',$getUser->user_id)
                                                            ->where('category_id',$getUser->category_id)
                                                            ->where('chat_type',0)
                                                            ->whereNull('deleted_at')
                                                            ->first();

                    $CounselorCategoryUser = CounselorCategoryUser::where('user_id',$getUser->user_id)
                                                            ->where('category_id',$getUser->category_id)
                                                            ->where('chat_type',0)
                                                            ->whereNull('deleted_at')
                                                            ->first();

                    $counselorAssigntoUser = array();

                    if(!empty($CounselorCategoryUser)){
                        $counselorAssigntoUser['assign_by'] = $CounselorCategoryUser->id;

                        CounselorCategoryUser::where('id',$CounselorCategoryUser->id)
                                                ->whereNull('deleted_at')
                                                ->delete();

                        
                    }

                    
                    $counselorAssigntoUser['counselor_id'] = 1;
                    $counselorAssigntoUser['user_id'] = $getUser->user_id;
                    $counselorAssigntoUser['category_id'] = $getUser->category_id;
                    $counselorAssigntoUser['activate_chat'] = 1;
                   
                    $counselorAssigntoUser['report'] = "Escalate";
                    $counselorAssignToUsers = CounselorCategoryUser::create($counselorAssigntoUser);

                    if(empty($CounselorCategoryUser) AND !empty($getCurrentCase)){

                        $chat = array();
                        $chat['counselor_category_by_user_id'] = $counselorAssignToUsers->id;
                        $chat['category_id'] = $getCurrentCase->category_id;
                        $chat['sender_id'] = $getCurrentCase->user_id;
                        $chat['reciver_id'] = '1';
                        $chat['message'] = $getCurrentCase->message;
                        $chat['status'] = 1;
                        $chat['read_status'] = 1;
                        $chat['date'] = date("Y-m-d");
                        $chat['time'] = date("H:i:s");
                        $chat['labels'] = "user sender";
                        $chat['created_at'] = date("Y-m-d H:i:s");
                        $chat['updated_at'] = date("Y-m-d H:i:s");
                        $chats = AsyncChat::create($chat);

                        CounselorCurrentCases::where('id',$getCurrentCase->id)
                                                ->whereNull('deleted_at')
                                                ->delete();
                    }

                    $output = ['success' => true,'data' => '','msg' => __("user assign to admin.")];
                    return $output;
                }
                else
                {
                    $output = ['success' => false,'data' => '','msg' => __("user not assign admin.")];
                    return $output;
                }

            }
        }
        $output = ['success' => false,'data' => '','msg' => __("user not found.")];
        return $output;

    }


    public function escalatedAssignTo(Request $request)
    {
       
        $counsellor = request()->get('counsellor');
        $sessionId = request()->get('sessionId');
        $sessionCounselorid = Auth::user()->id;

        $getCounselors = array();
        if(!empty($counsellor)){

            $getCounselors = User::where('id',$counsellor)
                            ->where('status','2')
                            ->where('counselor_availability','1')
                            ->where('chat_availability','0')
                            ->whereNull('deleted_at')
                            ->first();

            if(empty($getCounselors)){
                $output = ['success' => false,'data' => '','msg' => __("Counsellor is not available for Live chat...!")];
                return $output;
            }            
        }

        $liveWaitingEscalated = ChatSessions::where('session_id',$sessionId) 
                                        ->where('chat_type','1')
                                        ->where('chat_current_status','5')
                                        ->first();
                                                                                     
      


        if(!empty($liveWaitingEscalated))
        {
            if(!empty($getCounselors) AND !empty($counsellor)){

                ChatSessions::where('session_id', $sessionId)->update(['assign_by' => $counsellor,'timer_status' => '1']);

                $getCounselors->chat_availability = '1';
                $getCounselors->save();

                $output = ['success' => true,'data' => '','msg' => __("User assign to counsellor successfully...!")];
            } else {

                ChatSessions::where('session_id', $sessionId)->update(['assign_by' => $sessionCounselorid,'timer_status' => '1']);
                $output = ['success' => true,'data' => '','msg' => __("User assign to admin successfully...!")];
            }

        // $key = "live_counsellor_assign";
        // $body = 'Your chat with The Able Mind is now active';
        //         FcmCaredentials::sendNotificationtoUser($liveWaitingEscalated->user_id,$liveWaitingEscalated->counsellor_id,$key,$body);
        } else {           
            $output = ['success' => false,'data' => '','msg' => __("This chat id not found")];
        }

        return $output;
    }

    public function waitingAssignTo(Request $request){
       
        $counsellor = request()->get('counsellor');
        $sessionId = request()->get('sessionId');
        $sessionCounselorid = Auth::user()->id;

        $getCounselors = array();
        if(!empty($counsellor)){

            $getCounselors = User::where('id',$counsellor)
                            ->where('status','2')
                            ->where('counselor_availability','1')
                            ->where('chat_availability','0')
                            ->whereNull('deleted_at')
                            ->first();

            if(empty($getCounselors)){
                $output = ['success' => false,'data' => '','msg' => __("Counsellor is not available for Live chat...!")];
                return $output;
            }            
        }

        $liveWaitingEscalated = ChatSessions::where('session_id',$sessionId) 
                                            ->where('chat_current_status','1')
                                            ->first();                
       
        if(!empty($liveWaitingEscalated))
        {
            if(!empty($getCounselors) AND !empty($counsellor)){

                ChatSessions::where('session_id', $sessionId)->update(['counsellor_id' => $counsellor,'chat_current_status'=>'2']);

                $getCounselors->chat_availability = '1';
                $getCounselors->save();      

                $key = "live_counsellor_assign";
                $body = 'Your chat with The Able Mind is now active';
                FcmCaredentials::sendNotificationtoUser($liveWaitingEscalated->user_id,$liveWaitingEscalated->counsellor_id,$key,$body);

                $output = ['success' => true,'data' => '','msg' => __("User assign to counsellor successfully...!")];
            } else {
                 ChatSessions::where('session_id', $sessionId)->update(['counsellor_id' => $sessionCounselorid,'chat_current_status'=>'2']);               

                $output = ['success' => true,'data' => '','msg' => __("User assign to admin successfully...!")];
            }

            $ChatAudit['session_id'] = $sessionId;
            $ChatAudit['chat_status_old'] = "1";
            $ChatAudit['chat_status_new'] = "2";
            $ChatAudit['changed_by'] = $sessionCounselorid;
            ChatAudit::create($ChatAudit);

        } else {           
            $output = ['success' => false,'data' => '','msg' => __("This chat id not found")];
        }

        return $output;
    }


    
}
