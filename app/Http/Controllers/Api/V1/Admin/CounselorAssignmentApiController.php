<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAsyncChatRequest;
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
use Carbon\Carbon;
use Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Symfony\Component\HttpFoundation\Response;


// New code 
use App\Models\AsyncChatSessions;
use App\Models\AsyncChatAudit;
use App\Models\AsyncChatMessages;
use App\Models\CounsellorCategories;
use App\Models\Feature;
use App\Models\ChatFeature;
use App\Models\FcmCaredentials;

class CounselorAssignmentApiController extends Controller
{    

    public function async(Request $request)
    {
        try {                 
                DB::beginTransaction();

                $chatAssign = AsyncChatSessions::where('user_id',$request->user_id)
                                                ->where('category_id',$request->category_id)
                                                ->where('chat_current_status','!=','3')
                                                ->where('chat_type','2')
                                                ->first();

                if(!empty($chatAssign)){

                    if($chatAssign->chat_current_status == '1'){
                        $response = ['response' => $chatAssign,'message'=> 'This user already has async chat Waiting','status'=>true,'activity'=>'1'];
                        return response($response, 200);
                    }

                    $response = ['response' => $chatAssign,'message'=> 'This user already has async chat In Progress','status'=>true,'activity'=>'3'];
                    return response($response, 200);
                }

                $current = Carbon::now();
                $addHr = $current->addHours(10);
                $actualTime = $addHr->toTimeString();

                // $request->message
                $userAssignment['user_id'] = $request->user_id;
                $userAssignment['category_id'] = $request->category_id;
                $userAssignment['chat_type'] = "2";
                $userAssignment['chat_current_status'] = '1';
                $userAssignment['time_left'] = $actualTime;
                $ChatSessions = AsyncChatSessions::create($userAssignment);

                $ChatAudit['session_id'] = $ChatSessions->id;
                $ChatAudit['chat_status_old'] = "1";
                $ChatAudit['chat_status_new'] = "1";
                AsyncChatAudit::create($ChatAudit);
                
                $key = "async_user_chat";
                $body = 'User assign to tam for Async chat';
                
                $categoryCounsellor = CounsellorCategories::where('category_id',$request->category_id)
                                                            ->get();
                $data=FcmCaredentials::sendNotificationAsyncRequest($categoryCounsellor, $key, $body);

                $response = ['response' => $ChatSessions,'notification' => $data,'message'=> 'message send successfully.....!','status'=>true]; 

            DB::commit();
            return response($response, 200);
                
        } catch(\Exception $e)  {
            Log::error($e);
            DB::rollBack();
            $response = ['response' => array(),'message'=> 'Some Internal error','status'=>false,'error'=>$e];
            return response($response, 400);
        }
    }


    public function asyncChat(Request $request){ 

        try {
            DB::beginTransaction();
            $checkCounselorAssignment = AsyncChatSessions::where('session_id',$request->session_id)
                                                ->where(function($qq){
                                                    $qq->orWhere('chat_current_status','1');
                                                    $qq->orWhere('chat_current_status','2');          
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
                    AsyncChatMessages::create($chat);
                }
                if(!empty($checkCounselorAssignment->counsellor_id)){
                    $counsellor_id = $checkCounselorAssignment->counsellor_id;
                    $key = "async_user_chat";
                    $body = 'You have received a message from the user';
                    $data = FcmCaredentials::sendNotificationtoCounsellor($counsellor_id,$key,$body);
                } else {
                    $key = "async_user_chat";
                    $body = 'User assign to tam for Async chat';

                    $categoryCounsellor = CounsellorCategories::where('category_id',$request->category_id)->get();
                    $data=FcmCaredentials::sendNotificationAsyncRequest($categoryCounsellor, $key, $body);
                }                   
                                      
                $chats = AsyncChatMessages::where('session_id',$checkCounselorAssignment->session_id)->get();

                $response = ['response' => $chats,'notification' => $data,'message'=> 'message send successfully.....!','status'=>true];
            } else {
                $chats = [];
                $data = [];
                $response = ['response' => $chats,'notification' => $data,'message'=> 'failed','status'=>false,'errors'=>'async chat session not create'];
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

    public function getChat(Request $request){

            try { 
                DB::beginTransaction();
                $ChatAssignment = AsyncChatSessions::where('user_id',$request->user_id)
                                        ->where('category_id',$request->category_id)
                                        ->where('chat_type','2')
                                        ->get();

                if(!empty($ChatAssignment)) {   
                    $response = AsyncChatMessages::where(function($q) use($ChatAssignment){
                                if(!empty($ChatAssignment)){
                                    foreach ($ChatAssignment as $key => $value) {
                                        $q->orWhere('session_id',$value->session_id);
                                    }                    
                                }
                            })->get();

                    $response = ['message'=>"success",'status'=>true,'imgUrl'=>url('/public/chatAttachment/').'/','response' => $response];
                } else {
                  $response = ['response' => [], 'imgUrl'=>url('/public/chatAttachment/').'/','message'=>"Chat not found",'status'=>true];
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
   
}
