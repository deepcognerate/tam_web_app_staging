<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use App\Models\FcmToken;

class FcmCaredentials extends Model
{
    public static function fcmKey()
    {
         return 'AAAA5ufLSGA:APA91bErMuEZI2OAByMduZo1tlGB_aua4Y8Q6HsMTtXIqn9EqgV8l2n8QBN9ejm14ZMwwdB91sJKChbY_XawLTXX4xrK_YnwdnGAoGVI6HHCCcQSEj0I1HUuXTnn51X3JTq8SiHpgL4R';
    }


    public static function sendNotificationtoUser($userId, $categoryId,$key,$body){   

            $url = 'https://fcm.googleapis.com/fcm/send';
    
            // $getCounselor =  User::where('category_id',$categoryId)->where('status','2')->get();
          
             
            $FcmToken = FcmToken::where('user_id',$userId)->whereNotNull('fcm_token')->pluck('fcm_token')->all();
           
            $serverKey = FcmCaredentials::fcmKey();


            $data = [
                "registration_ids" => $FcmToken,
                "notification" => [
                    "title" =>"New Message",
                    "body" => $body,  
                ],
                "data" => [
                    "key" => $key,
                    "category_id" => $categoryId,
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

     public static function sendNotificationtoUserTyping($userId, $categoryId,$key,$body){   

            $url = 'https://fcm.googleapis.com/fcm/send';
    
            // $getCounselor =  User::where('category_id',$categoryId)->where('status','2')->get();
          
             
            $FcmToken = FcmToken::where('user_id',$userId)->whereNotNull('fcm_token')->pluck('fcm_token')->all();
           
            $serverKey = FcmCaredentials::fcmKey();


            $data = [
                "registration_ids" => $FcmToken,
                
                "data" => [
                    "key" => $key,
                    "category_id" => $categoryId,
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
     public static function sendNotificationtoUserChatResumeAdmin($userId, $counsellorId,$category_id,$key,$body){   

            $url = 'https://fcm.googleapis.com/fcm/send';
    
            // $getCounselor =  User::where('category_id',$categoryId)->where('status','2')->get();
          
             
            $FcmToken = FcmToken::where('user_id',$userId)->whereNotNull('fcm_token')->pluck('fcm_token')->all();
           
            $serverKey = FcmCaredentials::fcmKey();


            $data = [
                "registration_ids" => $FcmToken,
                "notification" => [
                    "title" =>"New Message",
                    "body" => $body,  
                ],
                "data" => [
                    "key" => $key,
                    "counsellorId" => $counsellorId,
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
            return true;      
    }

    public static function sendNotificationtoUserChatTimeSend($userId, $categoryId,$key,$time){   

            $url = 'https://fcm.googleapis.com/fcm/send';
    
            // $getCounselor =  User::where('category_id',$categoryId)->where('status','2')->get();
          
             
            $FcmToken = FcmToken::where('user_id',$userId)->whereNotNull('fcm_token')->pluck('fcm_token')->all();
           
            $serverKey = FcmCaredentials::fcmKey();

            $data = [
                "registration_ids" => $FcmToken,
                
                "data" => [
                    "key" => $key,
                    "time" => $time,
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
            return true;      
    }

    public static function sendNotificationtoCounsellor($counselor_id,$key,$body){
       
        $url = 'https://fcm.googleapis.com/fcm/send';

        $FcmToken = FcmToken::where('user_id',$counselor_id)->whereNotNull('fcm_token')->pluck('fcm_token')->all();
       
        $serverKey = FcmCaredentials::fcmKey();
  
        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => "New Message",
                "body" => $body,  
            ],
            "data" => [
                "key" => $key, 
                
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
        
        return $result;
    }

    public static function sendNotificationQueueNoUpdate($allWaitingUser,$key,$body){
           
            $url = 'https://fcm.googleapis.com/fcm/send';
             
            $FcmToken = FcmToken::whereNotNull('fcm_token')
                                ->whereNull('deleted_at')
                                ->where(function($q) use($allWaitingUser){
                                if(!empty($allWaitingUser)){
                                    foreach ($allWaitingUser as $key => $value) {
                                        $q->orWhere('user_id',$value->user_id);
                                    }                    
                                }
                            })
                            ->pluck('fcm_token')
                            ->all();
           
            $serverKey = FcmCaredentials::fcmKey();
      
            $data = [
                "registration_ids" => $FcmToken,
                "notification" => [
                    "title" =>"New Message",
                    "body" => $body,  
                ],
                "data" => [
                    "key" => $key,
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
            return true;      
    }

    public static function sendNotificationAsyncRequest($allCounsellor,$key,$body){
           
            $url = 'https://fcm.googleapis.com/fcm/send';
             
            $FcmToken = FcmToken::whereNotNull('fcm_token')
                                ->whereNull('deleted_at')
                                ->where(function($q) use($allCounsellor){
                                if(!empty($allCounsellor)){
                                    foreach ($allCounsellor as $key => $value) {
                                        $q->orWhere('user_id',$value->counsellor_id);
                                    }                    
                                }
                            })
                            ->pluck('fcm_token')
                            ->all();
           
            $serverKey = FcmCaredentials::fcmKey();
      
            $data = [
                "registration_ids" => $FcmToken,
                "notification" => [
                    "title" =>"New Message",
                    "body" => $body,  
                ],
                "data" => [
                    "key" => $key,
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

    public static function sendNotificationAdmin($allAdmin,$key,$body){
           
            $url = 'https://fcm.googleapis.com/fcm/send';
             
            $FcmToken = FcmToken::whereNotNull('fcm_token')
                            ->where(function($q) use($allAdmin){
                                if(!empty($allAdmin)){
                                    foreach ($allAdmin as $key => $value) {
                                        $q->orWhere('user_id',$value->id);
                                    }                    
                                }
                            })
                            ->pluck('fcm_token')
                            ->all();
           
            $serverKey = FcmCaredentials::fcmKey();
      
            $data = [
                "registration_ids" => $FcmToken,
                "notification" => [
                    "title" =>"New Message",
                    "body" => $body,  
                ],
                "data" => [
                    "key" => $key,
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
            return true;      
    }

    public static function sendNotificationToUserChatClose($userId, $chatCloseId,$keyMsg,$body){       
           
            $url = 'https://fcm.googleapis.com/fcm/send';
             
            $FcmToken = FcmToken::where('user_id',$userId)->whereNotNull('fcm_token')->pluck('fcm_token')->all();
           
            $serverKey = FcmCaredentials::fcmKey();
      
            $data = [
                "registration_ids" => $FcmToken,
                "notification" => [
                    "title" =>"New Message",
                    "body" => $body,  
                ],
                "data" => [
                    "key" => $keyMsg,
                    "chatCloseId" => $chatCloseId,
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