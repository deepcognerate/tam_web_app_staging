<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\notification;
use Illuminate\Http\Request;
use App\Models\FcmToken;

class Admin_to_user_notificationController extends Controller
{
    public function FcmServerKey(){
        return 'AAAA0yAqXOY:APA91bFx-9he2tSBX8bwjlnBHik0i-f_NhgsgaElzQQ0xDbefryv9G2dwAj0J-6lBhcMt14PWhIb0AfHXvaaW-V2NkE2rgTeLXDf5pbpAqvmmvvoVpYo73GfPsk4tYQo26s0c6p1pjLY';
}

    public function index(){
        return view('admin.notification.notification');
    }

    public function store(Request $request){
        $this->validate($request,
        [
            'title' => 'required',
            'description' => 'required',
             ]);
        if($request->image){
        $file =$request->file('image');
        $filename = 'image'. time().'.'.$request->image->extension();
        $file->move("upload/notification/",$filename);
        }else{
            $filename = "no_image";
        }
        $data = new notification;
        $data->title=$request->title;
        $data->description=$request->description;
        $data->image="$filename";
        $data->save();
        $datas = notification::orderBy('id','desc')->first();
        // dd($datas);
        $this->sendNotificationAdminToUser($datas->title,$datas->description,$datas->image,'admin_to_user');
        return redirect()->back()->with('message','notification send all of user:)');
    }

    public function sendNotificationAdminToUser($title, $description,$image,$keyMsg)
        {
           
            $url = 'https://fcm.googleapis.com/fcm/send';
          
            
            $FcmToken = FcmToken::whereNotNull('fcm_token')->pluck('fcm_token')->all();
           
            $serverKey = $this->FcmServerKey();
      
            $data = [
                "registration_ids" => $FcmToken,
                "notification" => [
                    "title" =>$title,
                    "body" => $description,  
                ],
                "data" => [
                    "key" => $keyMsg,
                    "image"=> ("{{ url('/upload/notification/'.$image) }}"),
                ]
            ];
            // dd($data);
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
}
