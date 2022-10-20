<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CounselorAssignmentResource;
use App\Models\CounselorAssignment;
use App\Models\WaitingAssignments;
use App\Models\User;
use App\Models\AsyncChat;
use App\Models\FcmToken;
use App\Models\LiveChat;
use App\Models\CounselorCurrentCases;
use App\Models\CounselorCategoryUser;
use App\Models\CurrentUserMessage;
use App\Models\CounselorPastCases;
use App\Models\ChatHistory;
use App\Models\ResumeChat;
use App\Models\Feedback;
use Carbon\Carbon;
use Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// New code 
use App\Models\ChatSessions;
use App\Models\CounsellorCategories;
use App\Models\ChatAudit;
use App\Models\ChatMessages;
use App\Models\Feature;
use App\Models\ChatFeature;
use App\Models\FcmCaredentials;

class CounselorLiveChatApiController extends Controller
{


        /*=============================================================================*/
    /*======================== User Request Live chat ======================================*/
        /*=============================================================================*/

        public function liveChatAssign(Request $request){
            
            try {
                 
                DB::beginTransaction();
                /* User Session check already assign or not  */ 
                    $chatAssign = ChatSessions::where('user_id',$request->user_id)
                                                ->where('chat_current_status','!=','7')
                                                ->where('chat_current_status','!=','8')
                                                ->where('chat_current_status','!=','6') 
                                                ->where('chat_type','1')
                                                ->first();
                    if(!empty($chatAssign)){
                        if($chatAssign->chat_current_status == '1'){
                            $waitingAssignmentList = ChatSessions::
                                                        where('category_id',$request->category_id)
                                                        ->where('chat_current_status',1)
                                                        ->get();

                            $queueNo = '1';
                            if($waitingAssignmentList){
                                if(!empty($waitingAssignmentList)){
                                    foreach ($waitingAssignmentList as  $key => $waitingKey) {
                                        if($chatSessions->id == $waitingKey->id){
                                            $queueNo = ++$key;
                                        }
                                    }
                                }  
                            }

                            $response = ['queueNo'=>$queueNo,'response' => $chatAssign,'message'=> 'This user already has Live chat Waiting','status'=>true,'activity'=>'1'];
                            return response($response, 200);
                        }

                        if($chatAssign->timer_status == '2'){
                           $timeleft = $this->timeleft($chatAssign->timer_start_time); 
                        } else {
                           $timeleft = 900;
                        }
                        

                        $response = ['response' => $chatAssign,'message'=> 'This user already has Live chat In Progress','status'=>true,'activity'=>'3','leftTime'=>$timeleft];
                                return response($response, 200);
                    }
                /* End User Session check already assign or not  */

                /*Check counsellor available or not */
                    $getCounselors = CounsellorCategories::leftjoin('tam_counsellor_categories as TCC', function ($join) {
                        $join->on('TCC.counsellor_id', '=', 'TCC.counsellor_id');
                        $join->whereNull('TCC.deleted_at');
                    })
                    ->leftjoin('users as U','TCC.counsellor_id','=','U.id')
                    ->leftjoin('tam_counsellor_feature as TCF','TCC.counsellor_id','=','TCF.counsellor_id')
                    ->where('TCF.feature_id', '1')
                    ->whereNull('TCF.deleted_at')
                    ->where('TCC.category_id',  $request->category_id)
                    ->where('U.status', '2')
                    ->where('U.counselor_availability', '1')
                    ->where('U.chat_availability', '0')
                    ->select('U.*')
                    ->orderBy('updated_at','asc')
                    ->first();            

                    if(!empty($getCounselors)) {
                        
                            $userAssignment['counsellor_id'] = $getCounselors->id;
                            $userAssignment['user_id'] = $request->user_id;
                            $userAssignment['category_id'] = $request->category_id;
                            $userAssignment['chat_type'] = "1";
                            $userAssignment['chat_current_status'] = '2';
                            $ChatSessions = ChatSessions::create($userAssignment);

                            $ChatAudit['session_id'] = $ChatSessions->id;
                            $ChatAudit['chat_status_old'] = "2";
                            $ChatAudit['chat_status_new'] = "2";
                            ChatAudit::create($ChatAudit);

                            $counselor = User::where('id',$getCounselors->id)->first();
                            $counselor->chat_availability = '1';
                            $counselor->save();

                            // Send Notification to Counsellor
                            $counsellor_id = $getCounselors->id;
                            $key = "user_assign_to_consellor";
                            $body = 'User assign to tam for Live chat';

                            FcmCaredentials::sendNotificationtoCounsellor($counsellor_id,$key,$body);
                            

                            $response = ['response' => $ChatSessions,'message'=> 'User  assignment successfully..! ','status'=>true,'activity'=>'2','leftTime'=>900];
                            // return response($response, 200);
                            
                    } else {  

                            $userAssignment['user_id'] = $request->user_id;
                            $userAssignment['category_id'] = $request->category_id;
                            $userAssignment['chat_type'] = "1";
                            $userAssignment['chat_current_status'] = 1;
                            $chatSessions = ChatSessions::create($userAssignment);

                            $ChatAudit['session_id'] = $chatSessions->id;
                            $ChatAudit['chat_status_old'] = "1";
                            $ChatAudit['chat_status_new'] = "1";
                            ChatAudit::create($ChatAudit);

                            $waitingAssignmentList = ChatSessions::
                                                        where('category_id',$request->category_id)
                                                        ->where('chat_current_status',1)
                                                        ->get();

                            $queueNo = '1';
                            if($waitingAssignmentList){
                                if(!empty($waitingAssignmentList)){
                                    foreach ($waitingAssignmentList as  $key => $waitingKey) {
                                        if($chatSessions->id == $waitingKey->id){
                                            $queueNo = ++$key;
                                        }
                                    }
                                }  
                            }
                                   
                            $response = ['queueNo'=>$queueNo,'response' => $chatSessions,'message'=> 'User Waiting assignment successfully..! ','status'=>true,'activity'=>'1'];
                            // return response($response, 200);
                    }
                    
                DB::commit();
                return response($response, 200);
                
            } catch(\Exception $e)  {
                    Log::error($e);
                    DB::rollBack();
                    $response = ['response' => array(),'message'=> 'Some Internal error','status'=>false,'error'=>$e];
                    return response($response, 400);
            }
            /*================End Check counsellor availabil or not================*/
        }

        /*=============================================================================*/
    /*======================== End User Request Live chat ==================================*/
        /*=============================================================================*/

        function timeleft($timer_start_time){
            $dateTimeObject1 = date_create($timer_start_time); 
            $dateTimeObject2 = date_create(date("H:i:s")); 
            $difference = date_diff($dateTimeObject1, $dateTimeObject2); 
            $seconds = $difference->days * 24 * 60;
            $seconds += $difference->h * 60;
            $seconds += $difference->i * 60;
            $seconds += $difference->s;
            $timeleft = 900-$seconds;
            return $timeleft;
        }

        /*=============================================================================*/
    /*======================== Live chat Assignment check ===================================*/
        /*=============================================================================*/

        public function liveChatAssignGet(Request $request){
            try {                
                $chatAssign = ChatSessions::where('user_id',$request->user_id)
                                            ->where(function($qq){
                                                    $qq->orWhere('chat_current_status','2');
                                                    $qq->orWhere('chat_current_status','3');
                                                    $qq->orWhere('chat_current_status','4');
                                                    $qq->orWhere('chat_current_status','5');

                                            })
                                            ->first();

                if(!empty($chatAssign)){
                    if($chatAssign->timer_status == '2'){
                        $timeleft = $this->timeleft($chatAssign->timer_start_time); 
                    } else {
                        $timeleft = 900;
                    }

                     $response = ['timeleft'=>$timeleft,'response' => $chatAssign,'message'=> 'success','status'=>true,'activity'=>'3'];
                            return response($response, 200);
                } else {

                    $response = ['response' => array(),'message'=> 'failed','status'=>false];
                    return response($response, 200);
                }       
            } catch(\Exception $e)  {
                    Log::error($e);
                    DB::rollBack();
                    $response = ['message'=> 'Some Internal error','status'=>false,'error'=>$e];
                    return response($response, 400);
            }     
        }

        /*=============================================================================*/
    /*======================== End Live chat Assignment check ==============================*/
        /*=============================================================================*/

        /*=============================================================================*/
    /*======================== User Live chat to Counsellor ================================*/
        /*=============================================================================*/

        public function liveChat(Request $request){ 

            try {
                DB::beginTransaction();
                 $checkCounselorAssignment = ChatSessions::where('session_id',$request->session_id)
                                                ->where(function($qq){
                                                    $qq->orWhere('chat_current_status','2');
                                                    $qq->orWhere('chat_current_status','3');
                                                    $qq->orWhere('chat_current_status','4');
                                                    $qq->orWhere('chat_current_status','5');
                                                })
                                                ->first();
                       
                if(!empty($checkCounselorAssignment)) {   
                    
                    if(isset($request->message) AND !empty($request->message)){

                        $chat = array();
                        $chat['session_id'] = $checkCounselorAssignment->session_id;
                        $chat['sender_id'] = $checkCounselorAssignment->user_id;
                        $chat['reciver_id'] = $checkCounselorAssignment->counsellor_id;
                        $chat['message'] = $request->message;
                        $chat['status'] = 1;
                        $chat['created_at'] = date("Y-m-d H:i:s");
                        $chat['updated_at'] = date("Y-m-d H:i:s");
                        ChatMessages::create($chat);
                    }
                    $categoryId = $checkCounselorAssignment->category_id;

                    $key = "live_user_message";
                    $body = 'You have received a message from the user';

                    if(!empty($checkCounselorAssignment->assign_by)){                       
                        $counsellor_id = $checkCounselorAssignment->assign_by;                        
                    } else {
                        $counsellor_id = $checkCounselorAssignment->counsellor_id;                        
                    }

                    $data = FcmCaredentials::sendNotificationtoCounsellor($counsellor_id,$key,$body);
                                      
                    $chats = ChatMessages::where('session_id',$checkCounselorAssignment->session_id)->get();

                    $response = ['response' => $chats,'notification' => $data,'message'=> 'message send successfully.....!','imgUrl'=>url('/public/chatAttachment/').'/', 'status'=>true];
                } else {
                    $chats = [];
                    $data = [];
                    $response = ['response' => $chats,'notification' => $data,'message'=> 'failed','status'=>false,'errors'=>'Live chat session not create','imgUrl'=>url('/public/chatAttachment/').'/'];
                }
                DB::commit();
                return response($response, 200);

            } catch(\Exception $e)  {
                Log::error($e);
                DB::rollBack();
                $response = ['message'=> 'Some Internal error','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

        /*=============================================================================*/
    /*======================== End User Live chat to Counsellor ============================*/
        /*=============================================================================*/


        /*=============================================================================*/
    /*======================== User Live chat msg get ======================================*/
        /*=============================================================================*/

        public function getLiveChat(Request $request){         
            try { 
                DB::beginTransaction();
                $liveChatAssignment = ChatSessions::where('user_id',$request->user_id)
                                                ->where('category_id',$request->category_id)
                                                ->where('chat_type','1')
                                                ->where(function($qq){
                                                    $qq->orWhere('chat_current_status','2');
                                                    $qq->orWhere('chat_current_status','3');
                                                    $qq->orWhere('chat_current_status','4');
                                                    $qq->orWhere('chat_current_status','5');
                                                })
                                                ->first();

                if(!empty($liveChatAssignment))
                {   
                    $response = ChatMessages::where('session_id',$liveChatAssignment->session_id)->get();

                    $response = ['message'=>"success",'status'=>true,'imgUrl'=>url('/public/chatAttachment/').'/','response' => $response];
                } else {
                  $response = ['response' => [], 'imgUrl'=>url('/public/chatAttachment/').'/','message'=>"Chat not found",'status'=>true];
                }
                   
                return response($response, 200);

            } catch(\Exception $e)  {
                Log::error($e);
                DB::rollBack();
                $response = ['message'=> 'Some Internal error','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

        /*=============================================================================*/
    /*======================== End User Live chat msg get =================================*/
        /*=============================================================================*/

        /*=============================================================================*/
    /*============================ User Feedback ===========================================*/
        /*=============================================================================*/

        public function liveChatFeedback(Request $request){  

            try {  
                 DB::beginTransaction();
                    $session_id = $request->session_id;
                    $getChatSessions = ChatSessions::where('session_id',$session_id)->first();
                    if(!empty($getChatSessions)){
                        $feedback['feedback_comment'] = $request->comment;
                        $feedback['feedback_star_reviews'] = $request->star_reviews;
                        ChatSessions::where('session_id', $session_id)->update($feedback); 
                        $response = ['message'=>"success",'status'=>true];

                    } else {
                        $response = ['message'=>"Close chat id not found",'status'=>true];
                    }
                 DB::commit();
                return response($response, 200);
            } catch(\Exception $e)  {
                Log::error($e);
                DB::rollBack();
                $response = ['message'=> 'Some Internal error','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

        /*=============================================================================*/
    /*============================ End User Feedback ======================================*/
        /*=============================================================================*/

        /*=============================================================================*/
    /*============================ Chat History Filter =====================================*/
        /*=============================================================================*/

        public function getChatHistory(Request $request){  

            try {  
                DB::beginTransaction();  

                $userId = $request->user_id;
                $chatType =  $request->type;
                $startDate =  $request->startDate;
                $endDate =  $request->endDate;

                if(empty($userId)){
                    $response = ['response' => [],'message'=>"User id required",'status'=>true];
                    return response($response, 200);
                }

                if(empty($chatType) AND $chatType  != '0'){
                    $response = ['response' => [],'message'=>"Type required",'status'=>true];
                    return response($response, 200);
                }
                   
            
                if(!empty($chatType) AND $chatType != 'all' AND !empty($userId)){

                    if(!empty($startDate) AND !empty($endDate)){

                        $chats = ChatSessions::with('getCategory')
                                                ->where('user_id',$userId)
                                                ->where('chat_type',$chatType)
                                                ->whereDate('created_at','>=',$startDate)
                                                ->whereDate('created_at','<=',$endDate)
                                                ->where('chat_current_status','6')
                                                ->orderBy('created_at','desc')
                                                ->get();

                    } else {

                        $chats = ChatSessions::with('getCategory')
                                            ->where('user_id',$userId)
                                            ->where('chat_type',$chatType)
                                            ->where('chat_current_status','6')
                                            ->orderBy('created_at','desc')
                                            ->get();
                        }             
                    }            

                    if($chatType == 'all' AND !empty($userId)){

                        if(!empty($startDate) AND !empty($endDate)){

                            $chats = ChatSessions::with('getCategory')
                                                    ->where('user_id',$userId)
                                                    ->where(function($qq){
                                                        $qq->orWhere('chat_type','1');
                                                        $qq->orWhere('chat_type','2');
                                                      })
                                                    ->whereDate('created_at','>=',$startDate)
                                                    ->whereDate('created_at','<=',$endDate)
                                                    ->where('chat_current_status','6')
                                                    ->orderBy('created_at','desc')
                                                    ->get(); 

                        } else {
                            $chats = ChatSessions::with('getCategory')
                                                    ->where('user_id',$userId)
                                                    ->where('chat_current_status','6')
                                                    ->orderBy('created_at','desc')
                                                    ->get(); 
                        }               
                    }

                    $chatH = array();
                    if(!empty($chats)){
                        foreach ($chats as $keya => $value) {
                            $time = $this->startTimeEndTimeGet($value->session_id);
                            $chat = array();
                            $date = date_create($value->created_at);
                            $created_at =  date_format($date,"Y-m-d");

                            $chat['chat_history_id'] = $value->session_id;
                            $chat['category_name'] = !empty($value->getCategory->category_name) ? $value->getCategory->category_name:$value->getCategory->id;
                            $chat['category_id'] = $value->getCategory->id;
                            $chat['chat_type'] = $value->chat_type;
                            $chat['date'] = $created_at.$time;
                            $chatH[] = $chat;
                        }
                    }


                    if(isset($chats) AND !empty($chats)){               
                        $response = ['message'=>"success",'status'=>true,'response' => $chatH];
                    } else {
                        $response = ['response' => [],'message'=>"Chat not found",'status'=>false];
                    }               
                    return response($response, 200);
                DB::commit();
                return response($response, 200);
            } catch(\Exception $e)  {
                Log::error($e);
                DB::rollBack();
                $response = ['message'=> 'Some Internal error','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

        /*=============================================================================*/
    /*============================ End Chat History Filter ================================*/
        /*=============================================================================*/

        /*=============================================================================*/
    /*========================= Chat Start Time & End Time Get =============================*/
        /*=============================================================================*/

        public function startTimeEndTimeGet($id) {

                    $chatendTime = ChatMessages::where('session_id',$id)
                                              ->orderBy('created_at','desc')
                                              ->first();

                    $chatstart = ChatMessages::where('session_id',$id)
                                            ->first();

                    if(!empty($chatendTime) AND !empty($chatstart)){

                        if(isset($chatstart->created_at) AND !empty( $chatstart->created_at)){
                            $startTime = date_create($chatstart->created_at);
                            $startTime =   date_format($startTime,"H:i");
                        }
                                    
                        if(isset($chatendTime->created_at) AND !empty( $chatendTime->created_at)){
                            $endTime = date_create($chatendTime->created_at);
                            $endTime =   date_format($endTime,"H:i");
                        }

                        $time = ' [ '.$startTime.' - '.$endTime.' ] ';

                    } else {
                        $time = ' ';
                    }
                return $time;
        }

        /*=============================================================================*/
    /*========================= Chat Start Time & End Time Get =============================*/
        /*=============================================================================*/

        /*=============================================================================*/
    /*========================= Chat Details Get By Session id =============================*/
        /*=============================================================================*/

        public function chatDetails(Request $request){

            try {  
                DB::beginTransaction();  

                $chat_history_id = $request->chat_history_id;
                if(empty($chat_history_id)){
                    $response = ['response' => [],'message'=>"Chat history id required",'status'=>true];
                    return response($response, 200);
                }

               $chat = ChatMessages::where('session_id',$chat_history_id)->where('session_id',$chat_history_id)->get();
                if(!empty($chat)) {
                    $response = ['message'=>"success",'status'=>true, 'imgUrl'=>url('/public/chatAttachment/').'/','response' => $chat];
                } else {
                    $response = ['response' => [],'message'=>"Chat not found",'status'=>false,'imgUrl'=>url('/public/chatAttachment/').'/'];
                }                   
              
                DB::commit();
                return response($response, 200);
            } catch(\Exception $e)  {
                Log::error($e);
                DB::rollBack();
                $response = ['message'=> 'Some Internal error','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

        /*=============================================================================*/
    /*========================= End Chat Details Get By Session id =========================*/
        /*=============================================================================*/

        /*=============================================================================*/
    /*=========================== Waiting Live Queue Remove ===============================*/
        /*============================================================================ */

        public function liveQueueRemove(Request $request) {  
            try {  
                DB::beginTransaction(); 

                $user_id = $request->user_id;
                $category_id = $request->category_id;

                $checkWattingList = ChatSessions::where('chat_current_status','1')
                                            ->where('user_id',$request->user_id)
                                            ->where('category_id',$request->category_id)
                                            ->where('chat_type','1')
                                            ->first();
               
                if(!empty($checkWattingList)){
                    

                    $updateData['chat_current_status'] = '8';
                    ChatSessions::where('session_id',$checkWattingList->session_id)
                                  ->update($updateData);

                    $ChatAudit11 = array();
                    $ChatAudit11['session_id'] = $checkWattingList->session_id;
                    $ChatAudit11['chat_status_old'] = $checkWattingList->chat_current_status;
                    $ChatAudit11['chat_status_new'] = "8";
                    $ChatAudit11['changed_by'] = $user_id;                    

                    $allWaitingUser = ChatSessions::where('category_id',$request->category_id)
                                                ->where('chat_type','1')
                                                ->where('chat_current_status','1')
                                                ->where('user_id','!=',$request->user_id)
                                                ->get();

                    if(!empty($allWaitingUser) AND count($allWaitingUser) >=1){

                        $key = "live_counsellor_assign";
                        $body = 'Your queue number has been updated';
                        FcmCaredentials::sendNotificationQueueNoUpdate($allWaitingUser,$key,$body);                        
                    }  

                    $response = ['message'=>"success",'status'=>true];

                } else {
                    $response = ['message'=>"id not found",'status'=>false];
                }     

                DB::commit();
                return response($response, 200);
            } catch(\Exception $e)  {
                Log::error($e);
                DB::rollBack();
                $response = ['message'=> 'Some Internal error','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

        /*=============================================================================*/
    /*========================= End Waiting Live Queue Remove ==============================*/
        /*=============================================================================*/
    
        /*=============================================================================*/
    /*=========================== Current Live Queue Remove ===============================*/
        /*============================================================================ */

        public function liveCurrentQueueRemove(Request $request) {  

            try {  
                DB::beginTransaction(); 

                $user_id = $request->user_id;
                $category_id = $request->category_id;
                $counselor_id = $request->counselor_id;


                $checkWattingList = ChatSessions::where('counsellor_id',$counselor_id)
                                            ->where('user_id',$user_id)
                                            ->where('category_id',$request->category_id)
                                            ->where('chat_type','1')
                                            ->where(function($qq){
                                                $qq->orWhere('chat_current_status','2');
                                                $qq->orWhere('chat_current_status','3');
                                                $qq->orWhere('chat_current_status','4');
                                            })
                                            ->first();
               
                if(!empty($checkWattingList)){

                    
                    ChatSessions::where('session_id',$checkWattingList->session_id)
                                  ->update(array('chat_current_status'=>8));

                    $ChatAudit11 = array();
                    $ChatAudit11['session_id'] = $checkWattingList->session_id;
                    $ChatAudit11['chat_status_old'] = $checkWattingList->chat_current_status;
                    $ChatAudit11['chat_status_new'] = "8";
                    $ChatAudit11['changed_by'] = $user_id;

                    ChatAudit::create($ChatAudit11);

                    $getCounselors = User::where('id',$counselor_id)->where('status','2')->first();

                    $waitingAssignments = ChatSessions::where('category_id',$category_id)
                                                    ->where('chat_type','1')
                                                    ->where('chat_current_status','1')
                                                    ->first();

                    if(!empty($waitingAssignments)){
                        
                        $userAssignment  = array();
                        $userAssignment['counsellor_id'] = $counselor_id;
                        $userAssignment['chat_current_status'] = '2';

                        ChatSessions::where('session_id',$waitingAssignments->session_id)->update($userAssignment);

                        $ChatAudit11 = array();
                        $ChatAudit11['session_id'] = $waitingAssignments->session_id;
                        $ChatAudit11['chat_status_old'] = $waitingAssignments->chat_current_status;
                        $ChatAudit11['chat_status_new'] = "2";
                        ChatAudit::create($ChatAudit11);

                        //  User Notification send 
                        $userId = $waitingAssignments->user_id;
                        $categoryId =  $waitingAssignments->category_id;

                        // Send Notification to User 
                        $userId = $userId;
                        $key = "live_counsellor_assign";
                        $body = 'Your chat with The Able Mind is now active';
                            
                        FcmCaredentials::sendNotificationtoCounsellor($userId,$key,$body);

                        // Send Notification to counsellor 

                        $counsellor_id = $getCounselors->id;
                        $key = "user_assign_to_consellor";
                        $body = 'User assign to tam for Live chat';
                            
                        FcmCaredentials::sendNotificationtoCounsellor($counsellor_id,$key,$body);

                        //  All user Queue Number update notification send 
                        $allWaitingUser = ChatSessions::where('category_id',$request->category_id)
                                                        ->where('chat_type','1')
                                                        ->where('chat_current_status','1')
                                                        ->where('user_id','!=',$request->user_id)
                                                        ->get();

                    
                        // All waiting user notification send
                        if(!empty($allWaitingUser) AND count($allWaitingUser) >=1){

                            $key = "live_counsellor_assign";
                            $body = 'Your queue number has been updated';
                            FcmCaredentials::sendNotificationQueueNoUpdate($allWaitingUser,$key,$body);                        
                        }                  
                        
                    } else {
                        $getCounselors->chat_availability = '0';
                        $getCounselors->save();
                    }

                    $response = ['message'=>"success",'status'=>true];

                } else {

                    $response = ['message'=>"Chat assign to another counsellor , Please wait and try again later",'status'=>false];
                }
                   
                DB::commit();
                return response($response, 200);
            } catch(\Exception $e)  {
                Log::error($e);
                DB::rollBack();
                $response = ['message'=> 'Some Internal error','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

        /*=============================================================================*/
    /*=========================== End Current Live Queue Remove ============================*/
        /*============================================================================ */


        /*=============================================================================*/
    /*================================ Working Time Get ====================================*/
        /*============================================================================ */

        public function  workingTime() { 

            try {  
                DB::beginTransaction(); 
                $checkData = array();
                $current_time = Carbon::now();
                $time = date("H:i");

                if($time >='07:00' AND $time <='22:00'){
                    $response = ['message'=>"in","time"=>$time,'status'=>true]; 
                } else {
                    $response = ['message'=>"out","time"=>$time,'status'=>false]; 
                }        

                DB::commit();
                return response($response, 200);
            } catch(\Exception $e)  {
                Log::error($e);
                DB::rollBack();
                $response = ['message'=> 'Some Internal error','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

        /*=============================================================================*/
    /*============================= End Working Time Get ==================================*/
        /*============================================================================ */

        /*=============================================================================*/
    /*================================= Chat Resume =======================================*/
        /*============================================================================ */

        public function  user_resume_chat(Request $request){  

            try {  
                DB::beginTransaction(); 

                $chat['user_id'] = $request->user_id;
                $chat['status'] = $request->status;
                $chat['session_id'] = $request->session_id;
                $chat['created_at'] = date("Y-m-d H:i:s");
                $chat['updated_at'] = date("Y-m-d H:i:s");
                $data = ResumeChat::create($chat);

                if(!empty($data)){
                    if($request->status == 'Yes'){
                        $chatResume = ChatSessions::where('session_id',$request->session_id)
                                                    ->where('chat_type','1')
                                                    ->where('user_id',$request->user_id)
                                                    ->where(function($qq){
                                                        $qq->orWhere('chat_current_status','2');
                                                        $qq->orWhere('chat_current_status','3');
                                                        $qq->orWhere('chat_current_status','4');
                                                        $qq->orWhere('chat_current_status','5');
                                                    })
                                                    ->first();

                       
                        if(!empty($chatResume)){
                            $response = ['message'=>"success",'status'=>true ,'active'=>'1']; 
                        } else {
                            $response = ['message'=>"success",'status'=>true,'active'=>'2'];
                        }

                    } else {
                        $response = ['message'=>"success",'status'=>true];
                    }
                } else {
                    $response = ['message'=>"success",'status'=>false]; 
                }  

                DB::commit();
              return response($response, 200);
            } catch(\Exception $e)  {
                Log::error($e);
                DB::rollBack();
                $response = ['message'=> 'Some Internal error','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

        /*=============================================================================*/
    /*=============================== End Chat Resume =====================================*/
        /*============================================================================ */

        /*=============================================================================*/
    /*=============================== Singal FCM Token get ================================*/
        /*============================================================================ */

        public function  fcmTokenGet(Request $request){  
            try {  
                DB::beginTransaction(); 
                $counsellor_id = $request->counsellor_id;
                $FcmToken =  FcmToken::where('user_id',$counsellor_id)->whereNotNull('fcm_token')->whereNull('deleted_at')->orderBy('created_at','desc')->first();

                if(!empty($FcmToken)){
                    $response = ['message'=>"success",'status'=>true , 'FcmToken'=>$FcmToken->fcm_token]; 
                } else {
                    $response = ['message'=>"success",'status'=>false]; 
                }        

                DB::commit();
                return response($response, 200);
            } catch(\Exception $e)  {
                Log::error($e);
                DB::rollBack();
                $response = ['message'=> 'Some Internal error','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

        /*=============================================================================*/
    /*============================ End Singal FCM Token get ================================*/
        /*============================================================================ */

        /*=============================================================================*/
    /*=============================== Multipal FCM Token get ===============================*/
        /*============================================================================ */

        public function  fcmTokenGetSecond(Request $request)
        {  
            try {  
                DB::beginTransaction(); 
                $counsellor_id = $request->counsellor_id;
                $FcmToken = FcmToken::where('user_id',$counsellor_id)->whereNotNull('fcm_token')->pluck('fcm_token')->all();

                if(!empty($FcmToken)){
                    $response = ['message'=>"success",'status'=>true , 'FcmToken'=>$FcmToken]; 
                } else {
                    $response = ['message'=>"success",'status'=>false]; 
                }  

                DB::commit();
                return response($response, 200);
            } catch(\Exception $e)  {
                Log::error($e);
                DB::rollBack();
                $response = ['message'=> 'Some Internal error','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

        /*=============================================================================*/
    /*=============================== End Multipal FCM Token get ============================*/
        /*============================================================================ */
   
}
