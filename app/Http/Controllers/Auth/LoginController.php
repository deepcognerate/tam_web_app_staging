<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth; 
use App\Models\FcmToken;
use App\Models\User;

use Carbon\Carbon;
use App\Models\CounselorAssignment;
use App\Models\WaitingAssignments;
use App\Models\CounselorCurrentCases;
use App\Models\CounselorCategoryUser;
use App\Models\Category;
use App\Models\AsyncChat;
use App\Models\FcmCaredentials;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        
    }

        public function FcmServerKey(){
             return FcmCaredentials::fcmKey();
        }

    /* added */
    public function logout(Request $request) {
       
        $sessionCounselorid = Auth::user()->id;
        $getFcmTokens = FcmToken::where('user_id', $sessionCounselorid)->first();
        if(!empty($getFcmTokens))
        {
            $removeFcmToken = FcmToken::where('user_id', $getFcmTokens->user_id)->delete();
        }

        if($sessionCounselorid != 1)
        {
            $counselorActive = User::where('id', $sessionCounselorid)
                                    ->where('status','2')
                                    ->update(['counselor_availability'=>0]);            
            
        }

        Auth::logout();
        return redirect('/login');
    }

    public function userAssignToAdmin_cronJob(Request $request){
           
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
            
            $start_time = strtotime(date('H:i', strtotime($current_time)));

            foreach($getUsers as $getUser)
            {
                $checkData['user_id'] = $getUser->user_id;
                $checkData['chat_type'] = $getUser->chat_type;
                $actual_time =  $getUser->time_left;

                // $end_time = strtotime($actual_time);

                $end_time = strtotime(date('H:i', strtotime($actual_time)));

                
                $time1hrsAgo = date('H:i', strtotime($actual_time.'-1 hour'));
                $current_timeFormate = date('H:i', strtotime($current_time));

                if($time1hrsAgo == $current_timeFormate){
                    
                    $allCounselor =  User::where('category_id',$getUser->category_id)->where('status','2')->get();

                    if(!empty($allCounselor)){
                        foreach ($allCounselor as $key => $value) {
                           $this->sendNotificationAsyncEscalatedCounsellor($value->id,$value->category_id,'async_chat_escalated_alert_to_counsellor','One hour left for Async Chat to be escalated. Please reply ');
                        }
                    }
                    
                }
                echo "<br>";  
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



     public function sendNotificationAsyncEscalatedCounsellor($userId,$category_id,$keyMsg,$body)
        {
           
            $url = 'https://fcm.googleapis.com/fcm/send';
    
            // $getCounselor =  User::where('category_id',$categoryId)->where('status','2')->get();
          
             
            $FcmToken = FcmToken::where('user_id',$userId)->whereNotNull('fcm_token')->pluck('fcm_token')->all();
           
            $serverKey = $this->FcmServerKey();
      
            $data = [
                "registration_ids" => $FcmToken,
                "notification" => [
                    "title" =>"New Message",
                    "body" => $body,  
                ],
                "data" => [
                    "key" => $keyMsg,
                    "category_id" => $category_id,
                ]
            ];
            $encodedData = json_encode($data);
        
            $headers = [
                'Authorization:key=' . $serverKey,
                'Content-Type: application/json',
            ];
        
            $ch = curl_init();
          
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
    
            // Execute post
            $result = curl_exec($ch);
    
            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }        
    
            // Close connection
            curl_close($ch);
    
            // FCM response
            // dd($result);  
            return $result;      
        }
}
