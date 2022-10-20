@extends('layouts.admin')
@section('content')
<style>
    .wrapper, body, html {
    min-height: 0% !important;
    overflow-x: revert !important;
}
  .button {
  background-color: #41b883 !important;
  -webkit-border-radius: 10px;
  border-radius: 10px;
  border: none;
  color: #FFFFFF;
  cursor: pointer;
  display: inline-block;
  font-family: Arial;
  font-size: 10px;
  padding: 5px 10px;
  text-align: center;
  text-decoration: none;
  -webkit-animation: glowing 1500ms infinite;
  -moz-animation: glowing 1500ms infinite;
  -o-animation: glowing 1500ms infinite;
  animation: glowing 1500ms infinite;
}
@-webkit-keyframes glowing {
  0% { background-color: #41b883; -webkit-box-shadow: 0 0 3px #41b883; }
  50% { background-color: #41b883; -webkit-box-shadow: 0 0 40px #41b883; }
  100% { background-color: #41b883; -webkit-box-shadow: 0 0 3px #41b883; }
}

@-moz-keyframes glowing {
  0% { background-color: #41b883; -moz-box-shadow: 0 0 3px #41b883; }
  50% { background-color: #41b883; -moz-box-shadow: 0 0 40px #41b883; }
  100% { background-color: #41b883; -moz-box-shadow: 0 0 3px #41b883; }
}

@-o-keyframes glowing {
  0% { background-color: #41b883; box-shadow: 0 0 3px #41b883; }
  50% { background-color: #41b883; box-shadow: 0 0 40px #41b883; }
  100% { background-color: #41b883; box-shadow: 0 0 3px #41b883; }
}

@keyframes glowing {
  0% { background-color: #41b883; box-shadow: 0 0 3px #41b883; }
  50% { background-color: #41b883; box-shadow: 0 0 40px #41b883; }
  100% { background-color: #41b883; box-shadow: 0 0 3px #41b883; }
}
  
  .tiblock {
      align-items: center;
      display: flex;
      height: 17px;
  }

  .ticontainer .tidot {
      background-color: #0a0a0a;
  }

  .tidot {
      -webkit-animation: mercuryTypingAnimation 1.5s infinite ease-in-out;
      border-radius: 2px;
      display: inline-block;
      height: 4px;
      margin-right: 2px;
      width: 4px;
  }

  @-webkit-keyframes mercuryTypingAnimation{
  0%{
    -webkit-transform:translateY(0px)
  }
  28%{
    -webkit-transform:translateY(-5px)
  }
  44%{
    -webkit-transform:translateY(0px)
  }
  }

  .tidot:nth-child(1){
  -webkit-animation-delay:200ms;
  }
  .tidot:nth-child(2){
  -webkit-animation-delay:300ms;
  }
  .tidot:nth-child(3){
  -webkit-animation-delay:400ms;
  }

  
  
.iconTickRight {
    font-size: 10px;
  }
</style>
<link rel="stylesheet" href="{{asset('public/chatboat/assets/vendors/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('public/chatboat/assets/vendors/css/vendor.bundle.base.css')}}">

  <link rel="stylesheet" href="{{asset('public/chatboat/assets/css/chatsection.css')}}">
  <link rel="stylesheet" href="{{asset('public/chatboat/assets/js/model.js')}}">

  <!-- End layout styles -->
  <link rel="shortcut icon" href="{{asset('public/chatboat/assets/images/favicon.ico')}}" />
  
<div class="content-wrapper">
<div id="chat-circle" class="btn btn-raised">
<div id="chat-overlay"></div>
<i class="material-icons mdi mdi-comment-multiple-outline"></i>
</div>
<div class="chat-box">
<div class="chat-box-header">

<div class="profile_img">
  <img class="img_circle" src="{{asset('public/assets/TAMlogo.png')}}" style="border: 1px solid #f9ae3b;" alt="Jesse Tino">
  <span class="availability_status online"></span>
</div>
<p class="chat_p" style="margin-left: 41px;
    margin-top: -4px;">@if($userData) {{ $userData->name }} @endif</p>
<span id="appLiveTimershow"></span>
<div class="text-right ml-auto" >
 <!--  <a class="btn start_btn mt-2 button" style="display:none;" id="liveChatButtonStart" onclick="liveChatStart('liveChatButtonStart','appLiveTimershow')">Start Chat</a> -->

  @if(!empty($getLiveChats->chat_current_status) AND $getLiveChats->chat_current_status != 5)
      <a class="btn start_btn mt-2 button" id="liveChatButtonStart" onclick="liveChatStart('liveChatButtonStart','appLiveTimershow')">Start Chat</a>
  @endif
  <!-- <a class="btn start_btn mt-2 liveChatButtonStop"  onclick="liveChatStart()">Stop</a> -->
</div>

</div>
<div class="chat-box-body">
  <div class="chat-logs chat-history">
    <ul>
    @if(!empty($liveChats))
    <?php $dates = array(); ?>
    @foreach($liveChats as $liveChat)
    <?php 
                  if(!in_array( $liveChat->date ,$dates)){
                    $dates[] = $liveChat->date;
                    echo '<div id="cm-msg-2" class="chat-msg user" >  </div><div id="cm-msg-1" class="chat-msg self" ></div>
                    <label style="text-align: center !important;
                padding: 1px;
                /* margin-left: 358px; */
                background-color: #8383830f;
                width: 100%;">'.$liveChat->date.'</label> <div id="cm-msg-2" class="chat-msg user" >  </div><div id="cm-msg-1" class="chat-msg self" ></div>';
                  }                  
                 ?>

    @if($liveChat->status == 1)
    <div id="cm-msg-2" class="chat-msg user" >          
        <span class="msg-avatar">            
            <img src="https://i.stack.imgur.com/l60Hf.png">          
        </span>          
        <div class="cm-msg-text"><span>{{ $liveChat->message }}</span></br><small>{{ $liveChat->date }} {{ $liveChat->time }} <i class="fa fa-check iconTickRight" aria-hidden="true"></i> </small></div>        
    </div>
    @else
    @if($liveChat->msgType == 1)
    <div id="cm-msg-1" class="chat-msg self" >
        <span class="msg-avatar">
            <img src="https://i.stack.imgur.com/l60Hf.png">
        </span>          
        <div class="cm-msg-text"><span><embed src="{{asset('public/chatAttachment/')}}/<?php echo $liveChat->chatAttachment; ?>" width= "300" height= "300"></span></br><small style="float:right;">{{ $liveChat->date }} {{ $liveChat->time }} <i class="fa fa-check iconTickRight" aria-hidden="true"></i> </small></div>        
    </div>
    @endif
     @if($liveChat->message != 'attachment')
        <div id="cm-msg-1" class="chat-msg self" >
            <span class="msg-avatar">
                <img src="https://i.stack.imgur.com/l60Hf.png">
            </span>          
            <div class="cm-msg-text"><span>{{ $liveChat->message }}</span></br><small style="float:right;">{{ $liveChat->date }} {{ $liveChat->time }} <i class="fa fa-check iconTickRight" aria-hidden="true"></i></small></div>        
        </div>
        @endif
    @endif
    @endforeach 
    @endif
  </ul>
  </div>
   <div class="chat-history" id="Typingmsg" style="display:none;">
    <ul>
      <div id="cm-msg-2" class="chat-msg user" >          
        <span class="msg-avatar">            
            <img src="https://i.stack.imgur.com/l60Hf.png">          
        </span>          
          <div class="ticontainer">
           <div class="tiblock" style="padding: 23px;">
            <div class="tidot"></div>
            <div class="tidot"></div>
            <div class="tidot"></div>
          </div>
        </div>   
    </div>
   </ul>
  </div>
  <!--chat-log -->
  <div id="showPreviewImg" style="display: none; bottom: -4px;
    position: absolute;
    margin: 34px;
    border: 2px solid #275642;
    margin-bottom: 0px;
    /* margin-left: 345px; */
    background-color: #000000b5;
    width: 100%;
    text-align: center; 
    height: 400px;
    ">   
  </div>
</div>
<div class="chat-input">
<form id="chatAjax_ids" method="POST" action="{{ route("admin.counselor-chat-live.liveChat") }}" enctype="multipart/form-data">
@csrf

    <input class="form-control" type="text" autocomplete="off" name="message" id="chat-input" oninput="checkmsglive(this)"  placeholder="Send a message..." minlength="1" maxlength="200" required>
    <input class="form-control" type="hidden" name="counselor_id"  id="counselor_id" value=" @if(!empty($getLiveChats->counsellor_id)) {{ $getLiveChats->counsellor_id }} @else @endif">
    <input class="form-control" type="hidden" id="user_id"  name="user_id" value=" @if(!empty($getLiveChats->user_id)) {{ $getLiveChats->user_id }} @else @endif">
    <input class="form-control" type="hidden" name="category_id" id="category_iddd" value=" @if(!empty($getLiveChats->category_id)) {{ $getLiveChats->category_id }} @else @endif">
      
    <input type="hidden" id="urlUpdatechat" value='{{ route("admin.counselor-chat-update-chat-live.update_chat_live_ajax",$getLiveChats->user_id ) }}'>

    <input type="hidden" id="urlChatStart" value='{{ route("admin.counselor-start-chat-live.start_chat_live_ajax",$getLiveChats->user_id ) }}'>

    <a class="btn attachment" onclick="imgcheck(this,'attachmentids')" ><i class="mdi mdi-paperclip"></i></a>
    <input type="file" name="file" id="attachmentids" style="display: none;" accept=".jpg, .png, .jpeg, .gif, .bmp, .pdf, .tif, .tiff|image/*" >

    <input type="hidden" name="hidecount" id="hidecountids" value="3">

    <input type="hidden" name="session_id" id="session_id" value="@if(!empty($getLiveChats->session_id)){{ $getLiveChats->session_id }}@endif">

    <!-- <a class="btn emoji"><i class="mdi mdi-emoticon"></i></a> -->
    <button  type="submit" class="chat-submit" id="liveChatbutton" disabled><i class="material-icons mdi mdi-send"></i></button>
  </form>

  
</div>
<div class="chat_footer">

<button class="btn btn-chat-footer btn-sm" id="myBtn" disabled>Escalate</button>

    <!-- The Modal -->
          <div id="myModal2" class="modal2">
            <!-- Modal content -->
            <div class="modal-content2" align="center">
              <div>
                  <h3> Choose any Option <span class="close2">&times;</span> </h3>
              </div>
              <hr>
                <div>

                <a  id="tab1" data-index="Inappropriate" name="report">
                <button class="btn btn-chat-footer inbtns btn-sm " style="margin:5px;"> Inappropriate </button> 
              </a>
              <a id="tab2" data-index="User At Risk" name="report">
              <button class="btn btn-chat-footer inbtns btn-sm " style="margin:5px;"> User At Risk </button>
              </a>
              <a id="tab3" data-index="Reassign User" name="report">
              <button class="btn btn-chat-footer inbtns btn-sm " style="margin:5px;">  Reassign User </button>
              </a>
              </div>
            </div>
          </div>
    <a>
      <button class="btn btn-chat-footer btn-sm" id="feedbackBtn"  style="display:none;">Close Chat</button>
      <button class="btn btn-chat-footer btn-sm" onclick="closechatLive()">Close Chat</button></a>
    <!-- The Modal -->
    <div id="myModal1" class="modal1">
      <!-- Modal content -->
      <div class="modal-content1">
        <form  method="post" id="chatCloseAjax_idsadmin" action="@if(!empty($getLiveChats->getUser->id)) {{ route('admin.chat-closed-live-admin.closeChatLiveByAdmin', $getLiveChats->getUser->id) }} @else  @endif " enctype="multipart/form-data">
        @csrf
        <span class="close1">&times;</span>
         <input class="form-control" type="hidden" name="category_id"  value=" @if(!empty($getLiveChats->category_id)) {{ $getLiveChats->category_id }} @else @endif">

        <label class="required" for="remark">Remark</label>
        <textarea class="form-control" name="remark" id="remark" rows="5" cols="20" required></textarea>
        <br>
        <label class="required" for="remark">Select issue code</label> <br>
                <select class="form-control select2" style="width: 620px;" name="selectIssueCode" id="selectIssueCodeId" tabindex="-1" aria-hidden="true">
                       <option value="">Select issue code</option>

                        <option value="Adjusting to academic system">College - Adjusting to academic system</option>
                        <option value="Homesickness/ relocation">College - Homesickness/ relocation</option>
                        <option value="Adulting skills">College - Adulting skills</option>
                        <option value="Study Skills">College - Study Skills</option>
                        <option value="Harassment/ Discrimination">College - Harassment/ Discrimination</option>
                        <option value="Test/ exam anxiety">College - Test/ exam anxiety</option>
                        <option value="Dealing with Failure">College - Dealing with Failure</option>
                        <option value="Career decisions & transitions">College -Career decisions & transitions</option>
                        <option value="Relationships- Peers">College - Relationships- Peers</option>
                        <option value="Relationships- management">College - Relationships- management</option>
                        <option value="Other college issues">College - Other college issues</option>

                        <option value="New Employee Adjustment">Work - New Employee Adjustment</option>
                        <option value="Business skills"> Work - Business skills</option>
                        <option value="Career decisions"> Work - Career decisions</option>
                        <option value="Low work control"> Work - Low work control</option>
                        <option value="Effort- reward"> Work - Effort- reward</option>
                        <option value="Work Transitions"> Work - Work Transitions</option>
                        <option value="New Manager Adjustment"> Work - New Manager Adjustment</option>
                        <option value="Maternity/ Paternity"> Work - Maternity/ Paternity</option>
                        <option value="Work conflict"> Work - Work conflict</option>
                        <option value="People Management"> Work - People Management</option>
                        <option value="Workplace Harassment/ Discrimination"> Work - Workplace Harassment/ Discrimination</option>
                        <option value="Romantic">Relationships  - Romantic</option>
                        <option value="Separations/ divorces"> Relationships - Separations/ divorces</option>
                        <option value="Family/in-laws"> Relationships - Family/in-laws</option>
                        <option value="Parent-child"> Relationships - Parent-child</option>
                        <option value="Others relationships"> Relationships - Others relationships</option>


                        <option value="Assertiveness">Personal - Assertiveness</option>
                        <option value="Relationship skills">Personal - Relationship skills</option>
                        <option value="Public speaking">Personal - Public speaking</option>
                        <option value="Managing emotions">Personal - Managing emotions</option>
                        <option value="Perfectionism/ procrastination/ TM">Personal - Perfectionism/ procrastination/ TM</option>
                        <option value="Problem solving/ DM">Personal - Problem solving/ DM</option>
                        <option value="Creativity, Spirituality">Personal - Creativity, Spirituality</option>
                        <option value="Self esteem/ confidence">Personal - Self esteem/ confidence</option>
                        <option value="Cognitive skills">Personal - Cognitive skills</option>
                        <option value="Other personal growth issues">Personal - Other personal growth issues</option>

                        <option value="Anxiety related">Mental Health - Anxiety related</option>
                        <option value="Mood related">Mental Health - Mood related</option>
                        <option value="Psychosis related">Mental Health - Psychosis related</option>
                        <option value="Addictions related">Mental Health - Addictions related</option>
                        <option value="Long term issues">Mental Health - Long term issues</option>
                        <option value="Body image/ eating disorders">Mental Health - Body image/ eating disorders</option>
                        <option value="Coping with Covid">Mental Health - Coping with Covid</option>
                        <option value="Advisory services">Mental Health - Advisory services</option>
                        <option value="Gender/Sexuality">Mental Health - Gender/Sexuality</option>
                        <option value="Other mental health issues">Mental Health - Other mental health issues</option>


                        <option value="Personal-Harm & loss">Non Work Place Other - Personal-Harm & loss</option>
                        <option value="Personal- Trauma">Non Work Place Other - Personal- Trauma</option>
                        <option value="Personal- Harassment/ Discrimination">Non Work Place Other - Personal- Harassment/ Discrimination</option>
                        <option value="Sleep">Non Work Place Other - Sleep</option>
                        <option value="Fitness">Non Work Place Other - Fitness</option>
                        <option value="Nutrition">Non Work Place Other - Nutrition</option>
                        <option value="Mind body practices">Non Work Place Other - Mind body practices</option>
                        <option value="Pregnancy">Non Work Place Other - Pregnancy</option>
                        <option value="Health concerns">Non Work Place Other - Health concerns</option>
                        <option value="Financial concerns">Non Work Place Other - Financial concerns</option>
                        <option value="Legal Concerns">Non Work Place Other - Legal Concerns</option>
                        <option value="Others Info">Non Work Place Other - Others Info</option>

                        <option value="Not enough information Provided">Not enough information Provided</option>
                </select>

                <br><br>
                <label class="required" for="remark">Close chat reason</label> <br>
                <select class="form-control select2" style="width: 620px;" name="closeChatReason" id="closeChatReasonId" tabindex="-1" aria-hidden="true">
                        <option value="">Select chat reason</option>
                        <option value="session ended">Session Ended</option>
                        <option value="user is inactive">User is inactive</option>
                        <option value="chat was reported">Chat was reported</option>
                </select>
                
                <br>
                <br>
                <input type="hidden" name="session_id_main" 
            value="@if(!empty($getLiveChats->session_id)){{ $getLiveChats->session_id }}@endif">
        <button class="btn btn-chat-footer btn-sm">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
<script src="{{asset('public/chatboat/assets/vendors/js/vendor.bundle.base.js')}}"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="{{asset('public/chatboat/assets/vendors/chart.js/Chart.min.js')}}"></script>
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{asset('public/chatboat/assets/js/off-canvas.js')}}"></script>
  <script src="{{asset('public/chatboat/assets/js/hoverable-collapse.js')}}"></script>
  <script src="{{asset('public/chatboat/assets/js/misc.js')}}"></script>
  <!-- endinject -->
  <!-- Custom js for this page -->
  <script src="{{asset('public/chatboat/assets/js/chart.js')}}"></script>
  <script src="{{asset('public/chatboat/assets/js/liveTimer.js')}}"></script>
  <script src="{{asset('public/chatboat/assets/js/chat.js')}}"></script>
  <script src="{{asset('public/chatboat/assets/js/modal.js')}}"></script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script>
    $('.chat-history,ul,div').animate({scrollTop: 9999999999});

    $(document).on("click", function(e){
        if($(e.target).is("#period_select_range_btn")){
          $("#selectPeriodRangePanel").show();
        }else{
            $("#selectPeriodRangePanel").hide();
        }
    });
</script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>
<script>

  var check = true;
 var msgcount = 1;

    var firebaseConfig = {
        // apiKey: 'AIzaSyBVwfEvl5Gtmi1u6Tq5q0pCDbfPugenQYE',
        // authDomain: 'tam-app-dev.firebaseapp.com',
        // databaseURL: 'https://auth-db582.hstgr.io/index.php?db=u789638131_tam_dev_1_5',
        // projectId: 'tam-app-dev',
        // storageBucket: 'tam-app-dev.appspot.com',
        // messagingSenderId: '906777746662',
        // appId: '1:906777746662:web:e4d6e511e2a1a4245d2f27',
        // measurementId: 'G-BEFLVMNWLB',


        apiKey: "AIzaSyAADXQHgQTNgBFZyCjDsV3W6Z7oc9B1B2g",
        authDomain: "tam-app-staging.firebaseapp.com",
        databaseURL: 'https://counsellor.theablemind.com/phpmyadmin/index.php?route=/database/structure&db=tam_web_staging',
        projectId: "tam-app-staging",
        storageBucket: "tam-app-staging.appspot.com",
        messagingSenderId: "991731337312",
        appId: "1:991731337312:web:ea28a22d09e482f34a9b05",
        measurementId: "G-VVKT4ZT2YY",
    };
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
 
    messaging.onMessage(function (payload) {
      var userId = parseFloat(payload.data.user_id);
      var categoryId = parseFloat(payload.data.category_id);
      var thisKey = payload.data.key;    
      update_chat_history_data();

      console.log(thisKey);
      if(thisKey == "async_user_message")
      {
        // $.ajax({
        //     url: "{{url('admin/counselor-assign-user-chat')}}/"+ userId + "/" + categoryId,
        //     method: 'GET',
        //     success: function(data) {
        //       console.log('result');
        //        console.log(data);
        //        // $('#employee_district_id').html(data.html);
        //     }
        // });
      }

      if(thisKey == "user_start_typing") {
        $('#Typingmsg').css('display','block');
      }


      if(thisKey == "counsellor_time") {
        counsellorTimerValueGet();
      }

      if(thisKey == "user_stop_typing") {
        $('#Typingmsg').css('display','none');
      }


    });

function counsellorTimerValueGet(){
            var time = timeLeft-1;      
            var user_id = $('#user_id').val();
            var category_id = $('#category_iddd').val();
            var urlStartChat = $('#urlChatStart').val();  

            $.ajax({
                    url: "{{url('admin/counsellor-timer-time-send')}}",
                    method:"GET",
                    data:{user_id:user_id,category_id:category_id,time:time},        
                  success: function(dataResult){

                  }
                });                    
        }

    $(document).on('submit', 'form#chatAjax_ids', function (e) {
      var msg = $('#chat-input').val();
            e.preventDefault();
             document.getElementById("liveChatbutton").disabled = true;
            // var data = $(this).serialize();
            var formData = new FormData(this);

            msgcount = msgcount+1;
             var msg =  $('#chat-input').val();
             $('#hidecountids').val(msgcount);

            if(msg != 'attachment'){            
              $('.chat-logs').append('<div id="msgdelete'+msgcount+'" ><div id="cm-msg-1" class="chat-msg self"><span class="msg-avatar"><img src="https://i.stack.imgur.com/l60Hf.png"></span><div class="cm-msg-text"><span>'+msg+'</span><br><small style="float:right;">Sending <i class="fa fa-spinner" aria-hidden="true"></i></small></div></div></div>');
            }

            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                success: function (result) {
                    if (result.success == true) {

                       $('#msgdelete'+result.hidecount).remove();
                       if(result.hidecount){
                        $('#msgdelete'+msgcount).remove();
                       }
                       
                        // var img = $('#showPreviewImg').html();
                        // if(img.replace(/\s/g, "").length){
                        //      $('.chat-logs').append('<div id="cm-msg-1" class="chat-msg self"><span class="msg-avatar"><img src="https://i.stack.imgur.com/l60Hf.png"></span><div class="cm-msg-text"><span>'+img+'</span><br><small style="float:right;"></small></div></div>');
                        // }                      

                       $('.chat-logs').append(result.data);
                       $('#chat-input').val('');
                       $('#attachmentids').val('');
                       document.getElementById("chat-input").placeholder = "Type message here..";
                       $('.chat-history,ul,div').animate({scrollTop: 9999999999});
                       $('#showPreviewImg').css('display','none');
                       $('#showPreviewImg').html('');
                    }
                  
                },
            });
    });


        $(document).on('submit', 'form#chatCloseAjax_idsadmin', function (e) {
   
            e.preventDefault();
            // var data = $(this).serialize();
            var formData = new FormData(this);

            $.ajax({
                method: 'post',
                url: $(this).attr('action'),
                dataType: 'json',
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                success: function (result) {
                   check = false;
                   window.location.href ="{{ route('admin.counselors.mychatAdmin') }}";
                },
            });
    });

    $(document).ready(function(){  
        resume_chat_by_admin();         
        setInterval(function(){
            update_chat_history_data();
        }, 2000);
    });


    function resume_chat_by_admin(){
     
        var user_id = $('#user_id').val();
        var category_id = $('#category_iddd').val();
        var urlStartChat = $('#urlChatStart').val();  
         $.ajax({
                 url: "{{url('admin/counselor-chat-resume-admin')}}",
                 method:"GET",
                 data:{user_id:user_id,category_id:category_id},        
                 success: function(dataResult){
                 }
        });
    }


     function update_chat_history_data(){
        var counselor_id = $('#counselor_id').val();
        var user_id = $('#user_id').val();
        var urlUpdate = $('#urlUpdatechat').val();
        var session_id = $('#session_id').val();
        $.ajax({
             url: urlUpdate,
             method:"GET",
             data:{user_id:user_id,counselor_id:counselor_id,session_id:session_id},        
             success: function(dataResult){

              if (dataResult.success == true) {
                  $('.chat-history,ul,div').animate({scrollTop: 9999999999});
                  $('.chat-logs').append(dataResult.data);
              }
             }
         });       
    }

      function closechatLive(){
          var user_id = $('#user_id').val();
          var session_id = $('#session_id').val();
          document.getElementById('feedbackBtn').click();

        $.ajax({
            url: "{{url('admin/chat-close-notification')}}",
            method:"GET",
            data:{user_id:user_id,session_id:session_id},        
            success: function(dataResult){
            }
        });
      }
    // Live msg check whiteSpace 

        function checkmsglive(obj){

            var user_id = $('#user_id').val();
            var category_id = $('#category_iddd').val();
            var urlStartChat = $('#urlChatStart').val();  

            var msg =$(obj).val();

            if (msg.replace(/\s/g, "").length) { 
                if(msg.length == 1){
                      $.ajax({
                       url: "{{url('admin/live-chat-typing-start')}}",
                       method:"GET",
                       data:{user_id:user_id,category_id:category_id},        
                       success: function(dataResult){
                       }
                     });
                }
                document.getElementById("liveChatbutton").disabled = false;
            } else {
              if(msg.length == 1){
                $.ajax({
                       url: "{{url('admin/live-chat-typing-start')}}",
                       method:"GET",
                       data:{user_id:user_id,category_id:category_id},        
                       success: function(dataResult){
                       }
                     });
              } if(msg.length == 0) {
                $.ajax({
                       url: "{{url('admin/live-chat-typing-stop')}}",
                       method:"GET",
                       data:{user_id:user_id,category_id:category_id},        
                       success: function(dataResult){
                       }
                     });
              } 
              document.getElementById("liveChatbutton").disabled = true;
            }        
        }
    // End validation 


    // Assign to admin 

    $("a[name=report]").on("click", function () { 
    var remark = $(this).data("index"); 
    var user_id = $('#user_id').val();
    var session_id = $('#session_id').val();
      $.ajax({
            url: "{{url('admin/user-assign-admin-live')}}",
            method:"GET",
            data:{user_id:user_id,remark:remark,session_id:session_id},        
            success: function(data) {
                // console.log(data);
                check = false;
              window.location.href ="{{ route('admin.counselorcurrentcases.index') }}";

            }
          });       
    });

    // End Assign to admin 


    function imgcheck(e,ids){
      var d = document.getElementById(ids).click();
      setTimeout(change_img, 8000);
    }


    

    function change_img() 
    {
        var img = 'attachmentids';
        var preview_img = 'showPreviewImg';
      
      var oFReader = new FileReader();
      oFReader.readAsDataURL($('#'+img)[0].files[0]);

      oFReader.onload = function (oFREvent) {
        console.log(oFREvent.target.result);
        var imgs = ' <embed src="'+oFREvent.target.result+'" width= "300" height= "300" style="    margin-top: 71px;">';
        $('#'+preview_img).html(imgs);
        if(oFREvent.target.result){
            $('#chat-input').val('attachment');
            document.getElementById("liveChatbutton").disabled = false;

            $('#showPreviewImg').css('display','block');
        }
      }
    }

</script>



<!-- Page refresh stop in CTRL F5 and CTRL R -->
<script>
    var ctrlKeyDown = false;

    $(document).ready(function(){    
        $(document).on("keydown", keydown);
        $(document).on("keyup", keyup);
    });

    function keydown(e) { 

        if ((e.which || e.keyCode) == 116 || ((e.which || e.keyCode) == 82 && ctrlKeyDown)) {
            // Pressing F5 or Ctrl+R
            e.preventDefault();
        } else if ((e.which || e.keyCode) == 17) {
            // Pressing  only Ctrl
            ctrlKeyDown = true;
        }
    };

    function keyup(e){
        // Key up Ctrl
        if ((e.which || e.keyCode) == 17) 
            ctrlKeyDown = false;
    };


    $(window).bind('beforeunload', function(e) { 
        if(check) {
        return "Unloading this page may lose data. What do you want to do..."
        e.preventDefault();
      }   
    });

</script>

<?php if(!empty($getLiveChats->chat_current_status) AND $getLiveChats->chat_current_status == 5) { ?>
<script>
      $(document).ready(function(){  
        adminUserCheckChatResume();      
    });

    function adminUserCheckChatResume(){
     
        var user_id = $('#user_id').val();
        var category_id = $('#category_iddd').val();
        var session_id = $('#session_id').val();
        var urlStartChat = $('#urlChatStart').val();  
         $.ajax({
                 url: "{{url('admin/admin-user-check-chat-resume')}}",
                 method:"GET",
                 data:{user_id:user_id,category_id:category_id,session_id:session_id},        
                 success: function(result){
                   if (result.success == true) {
                      if (result.status == 'Yes') {
                        liveChatStart('liveChatButtonStart','appLiveTimershow');
                      } else {
                        alert('User Decline Chat');
                      }
                   } else {
                    setTimeout(adminUserCheckChatResume, 1000);
                   }
                }
        });
    }
</script>
<?php } ?>
<!-- End refresh  -->
@endsection