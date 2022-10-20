<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MassDestroyCurrentCasesRequest;
use App\Http\Requests\StoreCurrentCasesRequest;
use App\Http\Requests\UpdateCurrentCasesRequest;
use App\Models\CounselorCurrentCases;
use App\Models\CounselorAssignment;
use App\Models\CounselorPastCases;
use App\Models\CurrentUserMessage;
use App\Models\User;
use App\Models\Category;
use App\Models\ChatHistory;
use App\Models\CounselorCategoryUser;
use App\Models\WaitingAssignments;
use App\Models\AsyncChat;
use App\Models\LiveChat;
use App\Models\ResumeChat;
use App\Models\Feedback;
use App\Models\FcmToken;
use Gate;
use Auth;
use DB;
use Session;
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

use App\Models\AsyncChatSessions;
use App\Models\AsyncChatAudit;
use App\Models\AsyncChatMessages;
use Illuminate\Support\Facades\Log;

class CounselorCurrentCasesController extends Controller
{   
   

        public function FcmServerKey(){
             return FcmCaredentials::fcmKey();
        }

    
        public function index(Request $request)
        {
            abort_if(Gate::denies('counselor_current_cases_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

            $sessionCounselorid = Auth::user()->id;
            $counselors = User::with(['roles'])->where('status','2')->get();
            $categorys = Category::get();

            if($sessionCounselorid == 1) { 
                return view('admin.counselorcurrentcases.index', compact('categorys','counselors'));
            } else {                
                return view('admin.counselorcurrentcases.counselorcurrentlist');
            }
        }
    
        public function create()
        {
            abort_if(Gate::denies('counselor_current_cases_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $categorys = Category::get();
            $categorys = User::where('status',0)->get();
            return view('admin.counselorcurrentcases.create', compact('roles','categorys'));
        }
    
        public function store(StoreCounselorRequest $request)
        {

            $counselorArr = array();
            $counselorArr['name'] = $request->name;
            $counselorArr['category_id'] = $request->category_id;
            $counselorArr['email'] = $request->email;
            $counselorArr['phone_no'] = $request->phone_no;
            $counselorArr['password'] = $request->password;
            $counselorArr['status'] = 2;
            $user = CounselorCurrentCases::create($counselorArr);
            $user->roles()->sync($request->input('roles', []));
    
            return redirect()->route('admin.counselorcurrentcases.index');
        }
    
        public function edit(User $counselor)
        {  
            abort_if(Gate::denies('counselor_current_cases_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $categorys = Category::get();
            return view('admin.counselorcurrentcases.edit', compact('counselor','categorys'));
        }



          public function counselorAssignUserAdmin($sessionId)
        {

            $sessionCounselorid = Auth::user()->id;
            $counselors = User::where('id',$sessionCounselorid)->where('status','2')->whereNull('deleted_at')->first();
            $getchats = AsyncChatSessions::where('session_id',$sessionId)
                                            ->where('chat_current_status','4')
                                            ->where('chat_type','2')
                                            ->first();
            if(!empty($getchats)) { 
                $ChatAssignment = AsyncChatSessions::where('user_id',$getchats->user_id)
                                        ->where('category_id',$getchats->category_id)
                                        ->where('chat_type','2')
                                        ->get();

                AsyncChatSessions::where('session_id', $sessionId)->update(['assign_by' => $sessionCounselorid]);

                    $asyncChats = array();
                    if(!empty($ChatAssignment)) {   
                        $asyncChats = AsyncChatMessages::where(function($q) use($ChatAssignment){
                                    if(!empty($ChatAssignment)){
                                        foreach ($ChatAssignment as $key => $value) {
                                            $q->orWhere('session_id',$value->session_id);
                                        }                    
                                    }
                        })->get();
                    }

                    $currentChat = AsyncChatMessages::where('session_id',$sessionId)->get();
                   


                    if(!empty($currentChat)){
                        AsyncChatMessages::where('session_id', $sessionId)
                                            ->update(['read_status' => 1]); 
                    }
 
                $user_details = User::where('id',$getchats->user_id)->where('status','0')->whereNull('deleted_at')->first();

            // return view('admin.chatboat.asyncchat',compact('getchats','asyncChats','user_details'));
             return view('admin.chatboat.admin.asyncchat',compact('getchats','user_details','asyncChats')); 

            } else {
                 return redirect()->route('admin.counselor.mychatAdmin')->with('error','Session id not valid. Try  Again later');  
            }            
        }

    public function counselorAssignUser($sessionId)
        {

            $sessionCounselorid = Auth::user()->id;
            $counselors = User::where('id',$sessionCounselorid)->where('status','2')->whereNull('deleted_at')->first();
            $getchats = AsyncChatSessions::where('session_id',$sessionId)
                                            ->where('chat_current_status','!=','3')
                                            ->where('chat_type','2')
                                            ->first();
            if(!empty($getchats)) { 
                $ChatAssignment = AsyncChatSessions::where('user_id',$getchats->user_id)
                                        ->where('category_id',$getchats->category_id)
                                        ->where('chat_type','2')
                                        ->get();

                    $asyncChats = array();
                    if(!empty($ChatAssignment)) {   
                        $asyncChats = AsyncChatMessages::where(function($q) use($ChatAssignment){
                                    if(!empty($ChatAssignment)){
                                        foreach ($ChatAssignment as $key => $value) {
                                            $q->orWhere('session_id',$value->session_id);
                                        }                    
                                    }
                        })->get();
                    }

                    $currentChat = AsyncChatMessages::where('session_id',$sessionId)->get();

                    if(!empty($currentChat)){
                        AsyncChatMessages::where('session_id', $sessionId)
                                            ->update(['read_status' => 1]); 
                    }

                    if($getchats->chat_current_status != 3){

                        AsyncChatSessions::where('session_id', $sessionId)->update(['chat_current_status' => 2,'counsellor_id'=>$sessionCounselorid]);

                         $ChatAudit11 = array();
                        $ChatAudit11['session_id'] = $sessionId;
                        $ChatAudit11['chat_status_old'] = $getchats->chat_current_status;
                        $ChatAudit11['chat_status_new'] = "2";
                        $ChatAudit11['changed_by'] = $sessionCounselorid;
                        AsyncChatAudit::create($ChatAudit11);
                    } 
            $user_details = User::where('id',$getchats->user_id)->where('status','0')->whereNull('deleted_at')->first();

            return view('admin.chatboat.asyncchat',compact('getchats','asyncChats','user_details'));

            } else {
                 return redirect()->route('admin.counselorcurrentcases.index')->with('error','Session id not valid. Try  Again later');  
            }            
        }

        

        public function counselorUserChat($userId, $categoryId)
        {
            $sessionCounselorid = Auth::user()->id;
            $counselorCategoryUsers = CounselorCategoryUser::where('category_id',$categoryId)
                                                          ->where('counselor_id',$sessionCounselorid)
                                                          ->where('user_id',$userId)
                                                          ->where('activate_chat',1)
                                                          ->first();
            if(!empty($counselorCategoryUsers))
            {
                $asyncChats = DB::select("SELECT * FROM `async_chat` WHERE `category_id` = ".$categoryId." AND (`sender_id` = $userId OR `reciver_id` = $userId) ORDER BY `date`,`time` ASC");
                $response = ['response' => $asyncChats,'message'=> 'message send successfully.....!'];

                return response($response, 200);
            }
            return response(400);
        }

    public function chat_view_show($counselor_assignment_id)
        {
            try{

                $sessionCounselorid = Auth::user()->id;

                $getLiveChats = ChatSessions::where('session_id',$counselor_assignment_id)->first();

                if(!empty($getLiveChats)){

                    $userData = User::where('id',$getLiveChats->user_id)
                                        ->where('status','0')
                                        ->first();
                    $liveChats = ChatMessages::where('session_id',$counselor_assignment_id)
                                                    ->get();
                            
                    return view('admin.chatboat.adminViewlivechat',compact('getLiveChats','liveChats','userData'));       
                } else { 
                    $liveChats = [];
                    $userData = array();
                    return view('admin.chatboat.adminViewlivechat',compact('getLiveChats','liveChats','userData'));       
                }
            } 
            catch(\Exception $errors)  {
                  Log::error($errors);
                  DB::rollBack();
                  $msg = $errors->errorInfo['2'];
                 return redirect()->route('admin.counselorcurrentcases.index')->with('error',$msg);
            }
        }

        public function counselorLiveChat($sessionId)
        {
            $sessionCounselorid = Auth::user()->id;          

            $counselors = User::where('id',$sessionCounselorid)->where('status','1')->first();

            if(!empty($counselors) AND $counselors->status == '1') {                
                $getLiveChats = ChatSessions::where('session_id',$sessionId)
                                            ->where('chat_type','1')
                                            ->first();

                
                if(!empty($getLiveChats)) {                    
                    $userData = User::where('id',$getLiveChats->user_id)->where('status','0')->first();

                    if($getLiveChats->chat_current_status =='5'){
                         $key_ = "live_counsellor_assign";
                         $body_ = 'Your chat with The Able Mind is now active';
                         FcmCaredentials::sendNotificationtoUser($getLiveChats->user_id,$getLiveChats->counsellor_id,$key_,$body_);
                         ChatSessions::where('session_id', $sessionId)->update(['escalated_status' => 2]);
                    } else {

                        $ChatAudit11 = array();
                        $ChatAudit11['session_id'] = $sessionId;
                        $ChatAudit11['chat_status_old'] = $getLiveChats->chat_current_status;
                        $ChatAudit11['chat_status_new'] = "3";
                        $ChatAudit11['changed_by'] = $sessionCounselorid;
                        ChatAudit::create($ChatAudit11);
                        ChatSessions::where('session_id', $sessionId)->update(['chat_current_status' => 3]);

                    }

                    $liveChats = ChatMessages::where('session_id',$sessionId)->get();

                    if(!empty(count($liveChats))){
                        ChatMessages::where('session_id', $sessionId)->update(['read_status' => 1]);
                    }
                   
                    return view('admin.chatboat.admin.livechat',compact('getLiveChats','liveChats','userData'));       
                } else  { 
                    return redirect()->route('admin.counselors.mychatAdmin');                      
                }

            } else {

                $counselors = User::where('id',$sessionCounselorid)->where('status','2')->first();

                $getLiveChats = ChatSessions::where('session_id',$sessionId)
                                            ->where('chat_current_status','!=','6')
                                            ->where('chat_type','1')
                                            ->first();




                if(!empty($getLiveChats)) {                    


                    $userData = User::where('id',$getLiveChats->user_id)->where('status','0')->first();
                    $chat_historylastThree = ChatSessions::with('getUser','getCategory','chat_history')
                                                            ->where('user_id',$getLiveChats->user_id)
                                                            ->where('chat_type','1')
                                                            ->where('chat_current_status','6')
                                                            ->orderBy('created_at','desc')
                                                            ->limit(3)
                                                            ->get();

                    $lastdata = array();
                    if(!empty($chat_historylastThree)){
                        foreach ($chat_historylastThree as $key => $value) {
                            $value->chat_history = ChatMessages::where('session_id',$value->session_id)->get();
                            $lastdata[]  = $value;
                        }
                    }
                    asort($lastdata);
                    $chat_historylastThree =  $lastdata;

                    $liveChats = ChatMessages::where('session_id',$getLiveChats->session_id)->get();

                    if(!empty(count($liveChats))){
                        ChatMessages::where('session_id', $getLiveChats->session_id)->update(['read_status' => 1]); 
                    }

                    if($getLiveChats->chat_current_status == '5'){
                         $key_ = "live_counsellor_assign";
                         $body_ = 'Your chat with The Able Mind is now active';
                        FcmCaredentials::sendNotificationtoUser($getLiveChats->user_id,$getLiveChats->counsellor_id,$key_,$body_);
                        ChatSessions::where('session_id', $sessionId)->update(['escalated_status' => 2]);


                    } else {
                        $ChatAudit11 = array();
                        $ChatAudit11['session_id'] = $sessionId;
                        $ChatAudit11['chat_status_old'] = $getLiveChats->chat_current_status;
                        $ChatAudit11['chat_status_new'] = "3";
                        $ChatAudit11['changed_by'] = $sessionCounselorid;
                        ChatAudit::create($ChatAudit11);
                        ChatSessions::where('session_id', $sessionId)->update(['chat_current_status' => 3]);

                       
                    }
                     return view('admin.chatboat.livechat',compact('getLiveChats','liveChats','userData','chat_historylastThree')); 
                          
                } else {

                    return redirect()->route('admin.counselorcurrentcases.index')->with('error','Already Close This Chat');  
                }

            }
        }


        public function liveChat(Request $request)
        {
            $sessionCounselorid = Auth::user()->id;

            $hidecount  = $request->hidecount;
            $session_id  = $request->session_id;

            $counselorCategoryUsers = ChatSessions::where('session_id',$session_id)
                                                ->where('chat_current_status','!=','6')
                                                ->first(); 
            
            if(!empty($counselorCategoryUsers))
            {
                if(!empty($request->file('file'))){
                    $file = $request->file('file');
                    $attachment = rand(111111, 999999) . '.' . $file->getClientOriginalExtension();
                    $request->file('file')->move("public/chatAttachment", $attachment);
                } else {
                    $attachment = '';
                }

                $chat['session_id'] = $counselorCategoryUsers->session_id;
                $chat['sender_id'] = $counselorCategoryUsers->counsellor_id;
                $chat['reciver_id'] = $counselorCategoryUsers->user_id;
                $chat['message'] = $request->message;
                $chat['read_status'] = 1;
                $chat['status'] = 2;
                $chat['chatAttachment'] = !empty($attachment)?$attachment:null;
                $chat['msgType'] = !empty($attachment)?'1':'0';
                $chat['created_at'] = date("Y-m-d H:i:s");
                $chat['updated_at'] = date("Y-m-d H:i:s");
                $chats = ChatMessages::create($chat);

                $userId = $counselorCategoryUsers->user_id;
                $categoryId = $counselorCategoryUsers->category_id;
                
                $key = "live_counsellor_message";
                $body = 'You have received a message from The Able Mind';
                FcmCaredentials::sendNotificationtoUser($userId,$categoryId,$key,$body);

                $key = "live_chat_typing_stop";
                $body = 'Counsellor typing stop';
                FcmCaredentials::sendNotificationtoUserTyping($userId,$categoryId,$key,$body);

                // return redirect()->route('admin.counselor-live-chat.counselorLiveChat', $getLiveChats->user_id);

                $date = date('Y-m-d',strtotime($chat['created_at']));
                $time = date('H:i:s',strtotime($chat['created_at']));


                $baseurl = url('/public/chatAttachment/').'/' ;
                if(!empty($attachment)){
                    if($request->message == 'attachment'){
                        $output = ['success' => true,
                            'data' => '<div id="cm-msg-1" class="chat-msg self" ><span class="msg-avatar"><img src="https://i.stack.imgur.com/l60Hf.png"></span><div class="cm-msg-text"><span><embed src="'.$baseurl.$chat['chatAttachment'].'" width= "300" height= "300"></span></br><small style="float:right;">'.$date.' '.$time.' <i class="fa fa-check iconTickRight" aria-hidden="true"></i> </small></div>        
                                </div>',
                            'msg' => __("added_success"),
                            'hidecount' => $hidecount
                        ];
                    } else {
                        $output = ['success' => true,
                            'data' => '<div id="cm-msg-1" class="chat-msg self" ><span class="msg-avatar"><img src="https://i.stack.imgur.com/l60Hf.png"></span><div class="cm-msg-text"><span><embed src="'.$baseurl.$chat['chatAttachment'].'" width= "300" height= "300"></span></br><small style="float:right;">'.$date.' '.$time.'</small></div>        
                                </div><div id="cm-msg-1" class="chat-msg self">
                                <span class="msg-avatar"><img src="https://i.stack.imgur.com/l60Hf.png">
                                </span><div class="cm-msg-text"><span>'.$request->message.'</span><br><small style="float:right;">'.$date.' '.$time.' <i class="fa fa-check iconTickRight" aria-hidden="true"></i></small></div></div>',
                            'msg' => __("added_success"),
                            'hidecount' => $hidecount
                            ];
                    } 
                } else {
                    $output = ['success' => true,
                        'data' => '<div id="cm-msg-1" class="chat-msg self">
                    <span class="msg-avatar"><img src="https://i.stack.imgur.com/l60Hf.png">
                    </span><div class="cm-msg-text"><span>'.$request->message.'</span><br><small style="float:right;">'.$date.' '.$time.' <i class="fa fa-check iconTickRight" aria-hidden="true"></i></small></div></div>',
                        'msg' => __("added_success"),
                        'hidecount' => $hidecount
                    ];
                }
                

            } else {
                $output = ['success' => false,
                    'data' => '',
                    'msg' => __("Already Chat Close")
                ];
            }

            return $output;

        }


        function notificationChat(){
            $user_id = request()->get('user_id');
            $category_id = request()->get('category_id');

            $key = "async_counsellor_message";
            $body = 'You have received a message from The Able Mind';
            FcmCaredentials::sendNotificationtoUser($user_id,$category_id,$key,$body);
        }

        
        public function chat(Request $request)
        {
            $session_id  = $request->session_id;


            $sessionCounselorid = Auth::user()->id;

           

            $counselorCategoryUsers = AsyncChatSessions::where('session_id',$session_id)
                                                ->where('chat_current_status','!=','3')
                                                ->first(); 

            if(!empty($request->file('file'))){
                $file = $request->file('file');
                $attachment = rand(111111, 999999) . '.' . $file->getClientOriginalExtension();
                $request->file('file')->move("public/chatAttachment", $attachment);
            } else {
                $attachment = '';
            } 
            $res = '';

            if(!empty($counselorCategoryUsers))
            {

                $chat['session_id'] = $counselorCategoryUsers->session_id;
                $chat['sender_id'] = $counselorCategoryUsers->counsellor_id;
                $chat['reciver_id'] = $counselorCategoryUsers->user_id;
                $chat['message'] = $request->message;
                $chat['read_status'] = 1;
                $chat['status'] = 2;
                $chat['chatAttachment'] = !empty($attachment)?$attachment:null;
                $chat['msgType'] = !empty($attachment)?'1':'0';
                $chat['created_at'] = date("Y-m-d H:i:s");
                $chat['updated_at'] = date("Y-m-d H:i:s");
                $chats = AsyncChatMessages::create($chat);

                $userId = $counselorCategoryUsers->user_id;
                $categoryId = $counselorCategoryUsers->category_id;
                
                $key = "async_counsellor_message";
                $body = 'You have received a message from The Able Mind';
                 FcmCaredentials::sendNotificationtoUser($userId,$categoryId,$key,$body);
                
                $date = date('Y-m-d',strtotime($chat['created_at']));
                $time = date('H:i:s',strtotime($chat['created_at']));

                $baseurl = url('/public/chatAttachment/').'/' ;

                if(!empty($attachment)){
                    if($request->message == 'attachment'){
                        $output = ['success' => true,
                            'data' => '<div id="cm-msg-1" class="chat-msg self" ><span class="msg-avatar"><img src="https://i.stack.imgur.com/l60Hf.png"></span><div class="cm-msg-text"><span><embed src="'.$baseurl.$chat['chatAttachment'].'" width= "300" height= "300"></span></br><small style="float:right;">'.$date.' '.$time.'</small></div>        
                                </div>',
                            'msg' => __("added_success")
                        ];
                    } else {
                        $output = ['success' => true,
                            'data' => '<div id="cm-msg-1" class="chat-msg self" ><span class="msg-avatar"><img src="https://i.stack.imgur.com/l60Hf.png"></span><div class="cm-msg-text"><span><embed src="'.$baseurl.$chat['chatAttachment'].'" width= "300" height= "300"></span></br><small style="float:right;">'.$date.' '.$time.'</small></div>        
                                </div><div id="cm-msg-1" class="chat-msg self">
                                <span class="msg-avatar"><img src="https://i.stack.imgur.com/l60Hf.png">
                                </span><div class="cm-msg-text"><span>'.$request->message.'</span><br><small style="float:right;">'.$date.' '.$time.'</small></div></div>',
                            'msg' => __("added_success")
                            ];
                    } 
                } else {
                    $output = ['success' => true,
                        'data' => '<div id="cm-msg-1" class="chat-msg self">
                    <span class="msg-avatar"><img src="https://i.stack.imgur.com/l60Hf.png">
                    </span><div class="cm-msg-text"><span>'.$request->message.'</span><br><small style="float:right;">'.$date.' '.$time.'</small></div></div>',
                        'msg' => __("added_success")
                    ];
                }

            } else {
                $output = ['success' => false,
                        'data' => '',
                        'msg' => __("Counselor Category User Not add")
                        
                    ];
            }
            // $output['Notification'] = $res;
            return $output;
        }


        public function update_chat_ajax(Request $request, $userId) {
            $counselor_id = request()->get('counselor_id');
            $session_id = request()->get('session_id');
            $user_id = $userId;

            $async = AsyncChatMessages::where('session_id', $session_id)
             ->where('read_status', '0')->where('status', '1')->take(1)->get();

            if(!empty(count($async))){
                $id = $async['0']->id;
                AsyncChatMessages::where('id', $id)->update(['read_status' => 1]);
                
                $date = date('Y-m-d',strtotime($async['0']->created_at));
                $time = date('H:i:s',strtotime($async['0']->created_at));

                $output = ['success' => true,
                        'data' => '<div id="cm-msg-2" class="chat-msg user">          
                        <span class="msg-avatar"><img src="https://i.stack.imgur.com/l60Hf.png"></span><div class="cm-msg-text">
                        <span>'.$async['0']->message.'</span><br><small>'.$date.' '.$time.'</small></div></div>',
                        'msg' => __("added_success")
                    ];
            } else {
                $output = ['success' => false,
                        'data' => '',
                        'msg' => __("no msg get ")
                    ];
            }
            return $output;
        }

        public function update_chat_live_ajax(Request $request, $userId) {
            $counselor_id = request()->get('counselor_id');
            $session_id = request()->get('session_id');
            $user_id = $userId;

            $live = ChatMessages::where('session_id', $session_id)
             ->where('read_status', '0')->where('status', '1')->take(1)->get();

            if(!empty(count($live))){
                $id = $live['0']->id;
                ChatMessages::where('id', $id)->update(['read_status' => 1]);
                
                $output = ['success' => true,
                        'data' => '<div id="cm-msg-2" class="chat-msg user">          
                        <span class="msg-avatar"><img src="https://i.stack.imgur.com/l60Hf.png"></span><div class="cm-msg-text">
                        <span>'.$live['0']->message.'</span><br><small>'.$live['0']->time.'</small></div></div>',
                        'msg' => __("added_success")
                    ];
            } else {
                $output = ['success' => false,
                        'data' => '',
                        'msg' => __("no msg get ")
                    ];
            }
            return $output;
        }
        
        public function start_chat_live_ajax(Request $request, $userId) {
            
            $user_id = $userId;
            $categoryId = request()->get('category_id');
            $session_id = request()->get('session_id');

            

            $output = ['success' => true,
                        'data' => '',
                        'msg' => __("added_success")
                    ];

            $sessionCounselorid = Auth::user()->id;
            $ChatSessionsdata = ChatSessions::where('session_id',$session_id)->first();

            $data['timer_status'] = '2';
            $data['timer_start_time'] = date("H:i:s");
            if($ChatSessionsdata->chat_current_status != 5){

                $key = "live_counsellor_chat_start";
                $body = 'Your chat with The Able Mind is now active';
                FcmCaredentials::sendNotificationtoUser($user_id,$categoryId,$key,$body);

                $data['chat_current_status'] = "4";            
                
                 
            }
            ChatSessions::where('session_id', $session_id)->update($data);

            $ChatAudit11 = array();
            $ChatAudit11['session_id'] = $session_id;
            $ChatAudit11['chat_status_old'] = $ChatSessionsdata->chat_current_status;
            $ChatAudit11['chat_status_new'] = "4";
            $ChatAudit11['changed_by'] = $sessionCounselorid;
            ChatAudit::create($ChatAudit11);

            return $output;
        }


        public function chat_resume_admin(Request $request) {
            
            
            $categoryId = request()->get('category_id');
            $userId = request()->get('user_id');

            $sessionCounselorid = Auth::user()->id;

            if($sessionCounselorid == 1) { 

                $getCurrentChat = CounselorAssignment::where('user_id',$userId)
                                                    ->where('category_id',$categoryId)
                                                    ->where('counselor_id',$sessionCounselorid)
                                                    ->whereNull('deleted_at')
                                                    ->first();

                if(!empty($getCurrentChat)){
                    $getCurrentChat->timer_status = '3';
                    $getCurrentChat->save();
                }

                $key = "chat_resume_admin";
                $body = 'Your chat with The Able Mind is now active';
                FcmCaredentials::sendNotificationtoUserChatResumeAdmin($userId, $sessionCounselorid, $categoryId,$key,$body);
            }           

            $output = ['success' => true,
                        'data' => '',
                        'msg' => __("added_success")
                    ];

            return $output;
        }

        public function live_chat_typing_start(Request $request) {
            
            $categoryId = request()->get('category_id');
            $userId = request()->get('user_id');

            $sessionCounselorid = Auth::user()->id;

            $key = "live_chat_typing_start";
            $body = 'Counsellor typing start';
            $responce = FcmCaredentials::sendNotificationtoUserTyping($userId,$categoryId,$key,$body);

            $output = ['success' => true,
                        'data' => '',
                        'msg' => $responce
                    ];

            return $output;
        }

        public function live_chat_typing_stop(Request $request) {
            
            $categoryId = request()->get('category_id');
            $userId = request()->get('user_id');

            $sessionCounselorid = Auth::user()->id;

            $key = "live_chat_typing_stop";
            $body = 'Counsellor typing stop';
            FcmCaredentials::sendNotificationtoUserTyping($userId,$categoryId,$key,$body);

            $output = ['success' => true,
                        'data' => '',
                        'msg' => __("added_success")
                    ];

            return $output;
        }

        public function live_chat_close_notification(Request $request) {
            
            $session_id = request()->get('session_id');
            $userId = request()->get('user_id');

            $sessionCounselorid = Auth::user()->id;
           

            $key = "live_chat_end_user";
            $body = 'Your conversation with The Able Mind has been closed';
            $notification = FcmCaredentials::sendNotificationToUserChatClose($userId,$session_id,$key,$body);

            $output = ['success' => true,
                        'data' => '',
                        'msg' =>$notification
                    ];

            return $output;
        }

        public function counsellor_timer_time_send(Request $request) {
            
            $categoryId = request()->get('category_id');
            $userId = request()->get('user_id');
            $time = request()->get('time');

            $sessionCounselorid = Auth::user()->id;


            $key = "live_chat_timer_time_send";
            $body = 'Counsellor typing stop';
            FcmCaredentials::sendNotificationtoUserChatTimeSend($userId,$categoryId,$key,$time);

            $output = ['success' => true,
                        'data' => $response,
                        'msg' => __("added_success")
                    ];

            return $output;
        }


        public function adminUserCheckChatResume(Request $request) {
            
            
            $userId = request()->get('user_id');
            $session_id = request()->get('session_id');

            $sessionCounselorid = Auth::user()->id;

            $counselorCategoryUsers = ResumeChat::where('user_id',$userId)
                                               ->where('session_id',$session_id)
                                               ->whereNull('deleted_at')
                                               ->first();

            if(!empty($counselorCategoryUsers)){

                ResumeChat::where('user_id',$userId)
                            ->where('session_id',$session_id)
                            ->whereNull('deleted_at')
                            ->delete();

                $output = ['success' => true,
                        'status' => $counselorCategoryUsers->status,
                        'msg' => __("added_success")
                    ];

            } else {
                $output = ['success' => false,
                        'data' => '',
                        'msg' => __("added_success")
                    ];               
            }

            return $output;
        }

        public function closeChat(Request $request)
        { 
            $remark = request()->get('remark');
            $issueCode = request()->get('selectIssueCodeId');  
            $session_id = request()->get('session_id');  
            $closeChatReason = request()->get('closeChatReasonId');
            $sessionCounselorid = Auth::user()->id;

            $getCounselors = User::where('id',$sessionCounselorid)->where('status','2')->first();

            $getChatSessions = AsyncChatSessions::where('session_id',$session_id)
                                                     ->where('chat_current_status','!=', '3')
                                                     ->first();
            
            if(!empty($getChatSessions))
            {

                $closeArray = array();
                $closeArray['close_reason'] = $closeChatReason;
                $closeArray['close_remark'] = $remark;
                $closeArray['close_issue_code'] = $issueCode;
                $closeArray['chat_current_status'] = 3;

                AsyncChatSessions::where('session_id', $session_id)->update($closeArray); 

                $ChatAudit11 = array();
                $ChatAudit11['session_id'] = $session_id;
                $ChatAudit11['chat_status_old'] = $getChatSessions->chat_current_status;
                $ChatAudit11['chat_status_new'] = "3";
                $ChatAudit11['changed_by'] = $sessionCounselorid;
                AsyncChatAudit::create($ChatAudit11);

                // Send Notification to User ( Live Chat Close )
                $userId = $getChatSessions->user_id;
                $key = "async_chat_end_user";
                $body = 'Your conversation with The Able Mind has been closed';

                FcmCaredentials::sendNotificationToUserChatClose($userId,$session_id,$key,$body);
                Session::flash('message', 'Chat close successfully...!');
                return 'true';

            } else {
                Session::flash('message', 'This session id not found');
                return 'false';
            }
        
        }

        public function closeChatAdmin(Request $request){

            $remark = request()->get('remark');
            $issueCode = request()->get('closeChatReasonId');  
            $session_id = request()->get('session_id');  
            $closeChatReason = request()->get('selectIssueCodeId');
            $sessionCounselorid = Auth::user()->id;

            $getCounselors = User::where('id',$sessionCounselorid)->where('status','1')->first();

            $getChatSessions = AsyncChatSessions::where('session_id',$session_id)
                                                     ->where('chat_current_status','!=', '3')
                                                     ->first();
            
            if(!empty($getChatSessions))
            {

                $closeArray = array();
                $closeArray['close_reason'] = $closeChatReason;
                $closeArray['close_remark'] = $remark;
                $closeArray['close_issue_code'] = $issueCode;
                $closeArray['chat_current_status'] = 3;

                AsyncChatSessions::where('session_id', $session_id)->update($closeArray); 

                $ChatAudit11 = array();
                $ChatAudit11['session_id'] = $session_id;
                $ChatAudit11['chat_status_old'] = $getChatSessions->chat_current_status;
                $ChatAudit11['chat_status_new'] = "3";
                $ChatAudit11['changed_by'] = $sessionCounselorid;
                AsyncChatAudit::create($ChatAudit11);

                // Send Notification to User ( Live Chat Close )
                $userId = $getChatSessions->user_id;
                $key = "async_chat_end_user";
                $body = 'Your conversation with The Able Mind has been closed';

                FcmCaredentials::sendNotificationToUserChatClose($userId,$session_id,$key,$body);
                Session::flash('message', 'Chat close successfully...!');
                return 'true';

            } else {
                Session::flash('message', 'This session id not found');
                return 'false';
            }
            
        }
        public function closeChatLiveByAdmin(Request $request, $userId)
        {

            $remark = request()->get('remark');
            $issueCode = request()->get('selectIssueCodeId');  
            $session_id = request()->get('session_id_main');  
            $closeChatReason = request()->get('closeChatReason');
            $sessionCounselorid = Auth::user()->id;

            $getCounselors = User::where('id',$sessionCounselorid)->where('status','2')->first();

            $getChatSessions = ChatSessions::where('session_id',$session_id)
                                                     ->where('chat_current_status','!=', '6')
                                                     ->first();
            
            if(!empty($getChatSessions))
            {

                $closeArray = array();
                $closeArray['close_reason'] = $closeChatReason;
                $closeArray['close_remark'] = $remark;
                $closeArray['close_issue_code'] = $issueCode;
                $closeArray['chat_current_status'] = 6;
                $closeArray['timer_end_time'] = date("H:i:s");

                ChatSessions::where('session_id', $session_id)->update($closeArray); 

                $ChatSessionsdata = ChatSessions::where('session_id',$session_id)->first();

                $ChatAudit11 = array();
                $ChatAudit11['session_id'] = $session_id;
                $ChatAudit11['chat_status_old'] = $getChatSessions->chat_current_status;
                $ChatAudit11['chat_status_new'] = "6";
                $ChatAudit11['changed_by'] = $sessionCounselorid;
                ChatAudit::create($ChatAudit11);

                // Send Notification to User ( Live Chat Close )
                $counsellor_id = $userId;
                $key = "live_chat_end_user";
                $body = 'Your conversation with The Able Mind has been closed';
                FcmCaredentials::sendNotificationToUserChatClose($userId,$session_id,$key,$body);

               Session::flash('message', 'Admin Chat close successfully...!');
                return 'true';

            } else {
                Session::flash('message', 'This session id not found');
                return 'false';
            }
        }

        public function closeChatLive(Request $request, $userId)
        {

            $remark = request()->get('remark');

            $issueCode = request()->get('selectIssueCode');  
            $session_id = request()->get('session_id_main');  
            $sessionCounselorid = Auth::user()->id;
            $closeChatReason = request()->get('closeChatReason');

            $getCounselors = User::where('id',$sessionCounselorid)->where('status','2')->first();

            $getCounselorCategoryUsers = ChatSessions::where('session_id',$session_id)
                                                     ->where('chat_current_status','!=', '6')
                                                     ->first();
            
            if(!empty($getCounselorCategoryUsers))
            {

                $closeArray = array();
                $closeArray['close_reason'] = $closeChatReason;
                $closeArray['close_remark'] = $remark;
                $closeArray['close_issue_code'] = $issueCode;
                $closeArray['chat_current_status'] = 6;
                $closeArray['timer_end_time'] = date("H:i:s");

                ChatSessions::where('session_id', $session_id)->update($closeArray); 

                $ChatSessionsdata = ChatSessions::where('session_id',$session_id)->first();

                $ChatAudit11 = array();
                $ChatAudit11['session_id'] = $session_id;
                $ChatAudit11['chat_status_old'] = $ChatSessionsdata->chat_current_status;
                $ChatAudit11['chat_status_new'] = "6";
                $ChatAudit11['changed_by'] = $sessionCounselorid;
                ChatAudit::create($ChatAudit11);

                // Send Notification to User ( Live Chat Close )
                $counsellor_id = $userId;
                $key = "live_chat_end_user";
                $body = 'Your conversation with The Able Mind has been closed';
                FcmCaredentials::sendNotificationToUserChatClose($userId,$session_id,$key,$body);

                $waitingAssignments = ChatSessions::join('tam_counsellor_categories as TCC', 'tam_chat_sessions.category_id' ,'=','TCC.category_id')
                                ->leftjoin('tam_counsellor_feature as TCF','TCC.counsellor_id','=','TCF.counsellor_id')
                                ->where('TCF.feature_id', '1')
                                ->whereNull('TCF.deleted_at')
                                ->where('TCC.counsellor_id',  $sessionCounselorid)
                                ->orderBy('tam_chat_sessions.created_at','asc')
                                ->where('tam_chat_sessions.chat_type',  '1')
                                ->where('tam_chat_sessions.chat_current_status', '1')
                                ->whereNull('TCC.deleted_at')
                                ->first();

                if(!empty($waitingAssignments)){ 

                    $userAssignment  = array();
                    $userAssignment['counsellor_id'] = $waitingAssignments->counsellor_id;
                    $userAssignment['chat_current_status'] = '2';

                    ChatSessions::where('session_id',$waitingAssignments->session_id)->update($userAssignment);
                    
                    $ChatAudit11 = array();
                    $ChatAudit11['session_id'] = $waitingAssignments->session_id;
                    $ChatAudit11['chat_status_old'] = $waitingAssignments->chat_current_status;
                    $ChatAudit11['chat_status_new'] = "2";
                    
                    ChatAudit::create($ChatAudit11);


                    $userId = $waitingAssignments->user_id;
                    $categoryId =  $waitingAssignments->category_id;
                    $key = "live_counsellor_assign";
                    $body = 'Your chat with The Able Mind is now active';

                    FcmCaredentials::sendNotificationtoCounsellor($userId,$key,$body);

                } else {
                    
                    $sessionCounselorid = Auth::user()->id;
                    $getCounselors = User::where('id',$sessionCounselorid)->where('status','2')->first();
                    $getCounselors->chat_availability = '0';
                    $getCounselors->save();
                }
                Session::flash('message', 'Chat close successfully...!');
                return 'true';

            } else {
                Session::flash('message', 'This user id not found in counsellor. Please Check another counsellor.....!');
                // return redirect()->route('admin.counselorcurrentcases.index');
                return 'false';

            }
        }

        public function userAssignAdmin(Request $request)
        {
            $userId = request()->get('user_id');
            $remark = request()->get('remark');

            $sessionCounselorid = Auth::user()->id;
            $counselorCategoryUsers = CounselorCategoryUser::where('user_id',$userId)
                                                            ->where('counselor_id',$sessionCounselorid)
                                                            ->where('activate_chat',1)
                                                            ->first();
            
            if(!empty($counselorCategoryUsers))
            {
                $currentCounselorRemove = CounselorCurrentCases::where('user_id',$userId)
                                                                ->where('category_id',$counselorCategoryUsers->category_id)
                                                                ->delete();

                CounselorCategoryUser::where('id',$counselorCategoryUsers->id)->delete();
                                                                    
                $getCounselors = User::where('id',$sessionCounselorid)->where('status','2')->first();
                
                $counselorAssigntoUser = array();
                $counselorAssigntoUser['counselor_id'] = 1;
                $counselorAssigntoUser['user_id'] = $counselorCategoryUsers->user_id;
                $counselorAssigntoUser['category_id'] = $counselorCategoryUsers->category_id;
                $counselorAssigntoUser['activate_chat'] = 1;
                $counselorAssigntoUser['counselor_name'] = $getCounselors->name;
                $counselorAssigntoUser['assign_by'] = $counselorCategoryUsers->id;

                $counselorAssigntoUser['report'] = $remark;
                $counselorAssignToUsers = CounselorCategoryUser::create($counselorAssigntoUser);
                Session::flash('message', 'Chat Assign to admin successfully...!');
            }
                return ;
        }
        

        public function userAssignAdminLive(Request $request)
        {
            $userId = request()->get('user_id');
            $remark = request()->get('remark');
            $session_id = request()->get('session_id');

            $sessionCounselorid = Auth::user()->id;
            
            $chatSessions = ChatSessions::where('session_id',$session_id)
                                        ->where('chat_current_status','!=', '6')
                                        ->first();
           
            if(!empty($chatSessions))
            {
                $escalated = array();
                $escalated['escalated_by'] = $sessionCounselorid;
                $escalated['escalated_reason'] = $remark;

                $ChatAudit['session_id'] = $session_id;
                $ChatAudit['chat_status_old'] = $chatSessions->chat_current_status;
                $ChatAudit['changed_by'] = $sessionCounselorid; 
                
                if( $remark == 'Inappropriate'){                    
                    $escalated['chat_current_status'] = '6';
                    $ChatAudit['chat_status_new'] = "6";
                } else {
                    $escalated['chat_current_status'] = '5';
                    $escalated['timer_start_time'] = null;
                    $escalated['timer_status'] = null;
                    $ChatAudit['chat_status_new'] = "5";
                }

                ChatSessions::where('session_id', $session_id)->update($escalated);
                ChatAudit::create($ChatAudit);

                $key = "live_counsellor_assign_to_admin";
                $body = 'This chat session is closed due to the reason provided by your counsellor';
                FcmCaredentials::sendNotificationtoCounsellor($userId,$key,$body);


                $waitingAssignments = ChatSessions::join('tam_counsellor_categories as TCC', 'tam_chat_sessions.category_id' ,'=','TCC.category_id')
                                ->where('TCC.counsellor_id',  $sessionCounselorid)
                                ->leftjoin('tam_counsellor_feature as TCF','TCC.counsellor_id','=','TCF.counsellor_id')
                                ->where('TCF.feature_id', '1')
                                ->whereNull('TCF.deleted_at')
                                ->orderBy('tam_chat_sessions.created_at','asc')
                                ->where('tam_chat_sessions.chat_type',  '1')
                                ->where('tam_chat_sessions.chat_current_status', '1')
                                ->whereNull('TCC.deleted_at')
                                ->first();

                if(!empty($waitingAssignments)){ 

                    $userAssignment  = array();
                    $userAssignment['counsellor_id'] = $waitingAssignments->counsellor_id;
                    $userAssignment['chat_current_status'] = '2';

                    ChatSessions::where('session_id',$waitingAssignments->session_id)->update($userAssignment); 
                    $ChatAudit11['session_id'] = $waitingAssignments->session_id;
                    $ChatAudit11['chat_status_old'] = $waitingAssignments->chat_current_status;
                    $ChatAudit11['chat_status_new'] = "2";
                    ChatAudit::create($ChatAudit11);
                    
                    $userId = $waitingAssignments->user_id;
                    $categoryId =  $waitingAssignments->category_id;

                    // Send Notification to User 
                        
                        $key = "live_counsellor_assign";
                        $body = 'Your chat with The Able Mind is now active';
                        FcmCaredentials::sendNotificationtoCounsellor($userId,$key,$body);

                } else {
                    
                    $getCounselors = User::where('id',$sessionCounselorid)->where('status','2')->first();
                    $getCounselors->chat_availability = '0';
                    $getCounselors->save();
                }

                $allAdmin = User::where('status','1')->get();
                $key = "live_counsellor_assign_to_admin";
                $body = 'New chat Escalated';
                FcmCaredentials::sendNotificationAdmin($allAdmin,$key,$body);

                Session::flash('message', 'Live Chat Assign to admin successfully...!');
                return true;
            }
            
        }

        public function update(UpdateCounselorRequest $request, User $counselor)
        {
            $counselorArr = array();
            $counselorArr['name'] = $request->name;
            $counselorArr['category_id'] = $request->category_id;
            $counselorArr['email'] = $request->email;
            $counselorArr['phone_no'] = $request->phone_no;
            $counselorArr['password'] = $request->password;  
            $counselorArr['status'] = 2;
            $counselor->update($counselorArr);
            return redirect()->route('admin..index');
        }
    
        public function show(User $counselor)
        {
            abort_if(Gate::denies('counselor_current_cases_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    
            $counselor->load('roles');
    
            return view('admin.counselorcurrentcases.show', compact('counselor'));
        }
    
        public function destroy(User $counselor)
        {
            abort_if(Gate::denies('counselor_current_cases_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    
            $counselor->delete();
    
            return back();
        }
    
        public function massDestroy(MassDestroyCounselorRequest $request)
        {
            CounselorCurrentCases::whereIn('id', request('ids'))->delete();
    
            return response(null, Response::HTTP_NO_CONTENT);
        }


        public function ajaxTableRefreshLiveAsync(Request $request)
            {
                $countAsync = request()->get('countAsync');
                $countLive = request()->get('countLive');

                $sessionCounselorid = Auth::user()->id;
                $CounsellorCategories = CounsellorCategories::where('counsellor_id',$sessionCounselorid)
                                                        ->whereNull('deleted_at')
                                                        ->get();

                $counselorLiveChats = ChatSessions::where('counsellor_id',$sessionCounselorid)
                                                    ->where('chat_type','1')
                                                    ->where(function($q) use($CounsellorCategories){
                                                        if(!empty($CounsellorCategories)){
                                                            foreach ($CounsellorCategories as $key => $value) {
                                                               $q->orWhere('category_id',$value->category_id);
                                                            }                    
                                                        }
                                                    })
                                                ->where(function($qq){
                                                    $qq->orWhere('chat_current_status','2');
                                                    $qq->orWhere('chat_current_status','3');
                                                    $qq->orWhere('chat_current_status','4');
                                                    $qq->orWhere('chat_current_status','5');
                                                })->get();

                

                if(!empty($counselorLiveChats) AND count($counselorLiveChats) > $countLive ||  count($counselorLiveChats) < $countLive ){
                        $output = ['success' => true,
                            'msg' =>'Live'
                        ];
                    return $output;
                } else {

                    $output = ['success' => false,
                                    'msg' => __("live no get")
                                ];
                        
                }
                
                $counselorCurrentChats = array();                

                $counselorCurrentChats = AsyncChatSessions::with('getCategory','getUser')
                                                    ->where('chat_type','2')
                                                    ->where('chat_current_status','1')
                                                    ->where(function($qq) use($CounsellorCategories){
                                                    if(!empty($CounsellorCategories)){
                                                        foreach ($CounsellorCategories as $key => $value) {
                                                            $qq->orWhere('category_id',$value->category_id);
                                                        }                    
                                                    }
                                                })
                                                ->get(); 

                $counselorAssignToUsers = AsyncChatSessions::with('getCategory','getUser')
                                                ->where('chat_current_status','2')
                                                ->where('chat_type','2')
                                                ->where('counsellor_id',$sessionCounselorid)
                                                ->get(); 

                $total = 0;
                if(!empty($counselorCurrentChats)){
                    $total = count($counselorCurrentChats);
                }
                if(!empty($counselorAssignToUsers)){
                    $total = $total+count($counselorAssignToUsers);
                }

                if($total > $countAsync || $total < $countAsync ){
                        $output = ['successAsync' => true,
                            'msg' =>'async'
                        ];
                        return $output;
                } else {
                        $output = ['successAsync' => false,
                            'msg' => __("Async no get 00")
                        ];
                }                

                return $output;            

            }
    
       
}
