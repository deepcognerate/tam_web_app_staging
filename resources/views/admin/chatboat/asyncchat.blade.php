@extends('layouts.admin')
@section('content')
<style>
    .wrapper, body, html {
    min-height: 0% !important;
    overflow-x: revert !important;
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
    <p> @if(!empty($user_details->name)) {{ $user_details->name }} @else  @endif</p>
    
          </div><span id="app"></span>
            </div>
            <div class="chat-box-body">
              <div class="chat-logs chat-history" >
                <ul>
                @if(!empty($asyncChats))
                <?php $dates = array(); ?>
                @foreach($asyncChats as $asyncChat)
                <?php 
                 $date = date('Y-m-d',strtotime($asyncChat->created_at));
                $time = date('H:i:s',strtotime($asyncChat->created_at));

                  if(!in_array( $date ,$dates)){
                    $dates[] = $date;
                    echo '<div id="cm-msg-2" class="chat-msg user" >  </div><div id="cm-msg-1" class="chat-msg self" ></div>
                    <label style="text-align: center !important;
                padding: 1px;
                /* margin-left: 358px; */
                background-color: #8383830f;
                width: 100%;">'. $date.'</label> <div id="cm-msg-2" class="chat-msg user" >  </div><div id="cm-msg-1" class="chat-msg self" ></div>';
                  }                  
                 ?>
                @if($asyncChat->status == 1)
                <div id="cm-msg-2" class="chat-msg user" >          
                    <span class="msg-avatar">            
                        <img src="https://i.stack.imgur.com/l60Hf.png">          
                    </span>          
                    <div class="cm-msg-text"><span>{{ $asyncChat->message }}</span></br><small>{{  $date }} {{ $time }}</small></div>        
                </div>
                @else 

                  @if($asyncChat->msgType == 1)

                  <div id="cm-msg-1" class="chat-msg self" >
                      <span class="msg-avatar">
                          <img src="https://i.stack.imgur.com/l60Hf.png">
                      </span>          
                      <div class="cm-msg-text"><span><embed src="{{asset('public/chatAttachment/')}}/<?php echo $asyncChat->chatAttachment; ?>" width= "300" height= "300"></span></br><small style="float:right;">{{  $date }} {{ $time }}</small></div>        
                  </div>
                 @endif

                 @if($asyncChat->message != 'attachment')
                    <div id="cm-msg-1" class="chat-msg self" >
                      <span class="msg-avatar">
                          <img src="https://i.stack.imgur.com/l60Hf.png">
                      </span>          
                      <div class="cm-msg-text"><span>{{ $asyncChat->message }}</span></br><small style="float:right;">{{  $date }} {{ $time }}</small></div>        
                  </div>
                    @endif

                @endif
                @endforeach 
                @endif
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
            <form id="chatAjax_ids" method="POST" action="{{ route("admin.counselor-chat.chat") }}" enctype="multipart/form-data">
            @csrf
                <input class="form-control" autocomplete="off" type="text" name="message" oninput="checkmsglive(this)" id="chat-input" placeholder="Send a message..." required>
                <input class="form-control" type="hidden" name="counselor_id" id="counselor_id" value="{{ $getchats->counsellor_id }}">
                <input class="form-control" type="hidden" id="user_id"  name="user_id" value="{{ $getchats->user_id }}">
                <input class="form-control" type="hidden" name="category_id" id="category_id_" value="{{ $getchats->category_id }}">
                
                <input type="hidden" id="urlUpdatechat" value='{{ route("admin.counselor-chat-update-chat.update_chat_ajax",$getchats->user_id ) }}'>

                <a class="btn attachment" onclick="imgcheck(this,'attachmentids')" ><i class="mdi mdi-paperclip"></i></a>
                  <input type="file" name="file" id="attachmentids" style="display: none;" accept=".jpg, .png, .jpeg, .gif, .bmp, .pdf, .tif, .tiff|image/*">

                <!-- <button class="btn attachment"><i class="mdi mdi-paperclip"></i></button> -->
                 <!-- <a class="btn emoji"><i class="mdi mdi-emoticon"></i></a> -->

                 <input type="hidden" name="session_id" id="session_id" value="@if(!empty($getchats->session_id)){{ $getchats->session_id }}@endif">

                <button  type="submit" class="chat-submit" id="asyncChatbutton" disabled><i class="material-icons mdi mdi-send"></i></button>
              </form>
            </div>
    <div class="chat_footer">
      <!-- <a class="btn attachment" onclick="imgcheck(this,'attachmentids')" ><i class="mdi mdi-paperclip"></i></a> -->
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
          <button class="btn btn-chat-footer btn-sm"  id="feedbackBtn">Close Chat</button>
            <!-- <a href="@if(!empty($getLiveChats->getUser->id)) {{ route('admin.chat-closed.closeChat', $getLiveChats->getUser->id) }} @else  @endif "><button class="btn btn-chat-footer btn-sm" id="feedbackBtn">Close Chat</button></a> -->
            <!-- The Modal -->
            <div id="myModal1" class="modal1">
              <!-- Modal content -->
              <div class="modal-content1">
                <span class="close1">&times;</span> 
                <label class="required" for="remark">Remark</label>
                <textarea class="form-control" name="remark" id="remark" rows="5" cols="20"></textarea>
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
                <button class="btn btn-chat-footer inbtns btn-sm" onClick="chatRemark();" >Submit</button>
              </div>
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
  <script src="{{asset('public/chatboat/assets/js/chat.js')}}"></script>
  <script src="{{asset('public/chatboat/assets/js/modal.js')}}"></script>
  <script>
     $('.chat-history,ul,div').animate({scrollTop: 99999999});
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
    });

     $(document).on('submit', 'form#chatAjax_ids', function (e) {

      var msg = $('#chat-input').val();
      console.log(msg);

        e.preventDefault();
        // $(this).find('button[type="submit"]')
        //     .attr('disabled', true);
        // var data = $(this).serialize();
        var formData = new FormData(this);


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
                   
                  
                  $('#attachmentids').val('');
                  $('#showPreviewImg').css('display','none');
                  $('#showPreviewImg').html('');
                  $('#chat-input').val('');
                  document.getElementById("chat-input").placeholder = "Type message here..";
                  $('.chat-history,ul,div').animate({scrollTop: 99999999});
                  $('.chat-logs').append(result.data);
                  // setTimeout(timeSetNotification, 600000);
                }  else {
                alert('This chat has been removed from your queue.');
                window.location.href ="{{ route('admin.counselorcurrentcases.index') }}";
              }               
            },
        });
    });


      $(document).ready(function(){
         
        setInterval(function(){
            update_chat_history_data();
        }, 1000);
        
    });

      // function timeSetNotification(){
        
      //   var user_id = $('#user_id').val();
      //   var category_id = $('#category_id_').val();

      //   var closeChatReasonId = $('#closeChatReasonId').val();
      //   $.ajax({
      //         url: "{{url('admin/chat-async-notification')}}",
      //         method:"GET",
      //         data:{user_id:user_id,category_id:category_id},        
      //         success: function(data) {
                
      //         }
      //   });
      // }

     function update_chat_history_data(){
        var counselor_id = $('#counselor_id').val();
        var user_id = $('#user_id').val();
        var urlUpdate = $('#urlUpdatechat').val();
        var session_id = $('#session_id').val();
        console.log(counselor_id);
        $.ajax({
             url: urlUpdate,
             method:"GET",
             data:{user_id:user_id,counselor_id:counselor_id,session_id:session_id},        
             success: function(dataResult){

              if (dataResult.success == true) {
                  $('.chat-history,ul,div').animate({scrollTop: 99999999});
                  $('.chat-logs').append(dataResult.data);
              }
             }
         });       
    }


    $("a[name=report]").on("click", function () { 
    var remark = $(this).data("index"); 
    var user_id = $('#user_id').val();
      $.ajax({
            url: "{{url('admin/user-assign-admin')}}",
            method:"GET",
            data:{user_id:user_id,remark:remark},        
            success: function(data) {
              window.location.href ="{{ route('admin.counselorcurrentcases.index') }}";

            }
      });       
});

    function chatRemark()
    {
      var remark = $('#remark').val();
      var user_id = $('#user_id').val();
      var selectIssueCodeId = $('#selectIssueCodeId').val();
      var closeChatReasonId = $('#closeChatReasonId').val();
       var session_id = $('#session_id').val();
      $.ajax({
            url: "{{url('admin/close-chat-async')}}",
            method:"GET",
            data:{session_id:session_id,user_id:user_id,remark:remark,selectIssueCodeId:selectIssueCodeId,closeChatReasonId:closeChatReasonId},        
            success: function(data) {
              window.location.href ="{{ route('admin.counselorcurrentcases.index') }}";
            }
      });       
    }
   
  function imgcheck(e,ids){
      var d = document.getElementById(ids).click();
      setTimeout(change_img, 2000);
    }

    function change_img() 
    {
        var img = 'attachmentids';
        var preview_img = 'showPreviewImg';
        var check  = $('#'+img).val();

      if(check.length){
         alert("Wait until attachment upload and preview...");
        var oFReader = new FileReader();
        oFReader.readAsDataURL($('#'+img)[0].files[0]);

        oFReader.onload = function (oFREvent) {
          console.log(oFREvent.target.result);
          var imgs = ' <embed src="'+oFREvent.target.result+'" width= "300" height= "300" style="    margin-top: 71px;">';
          $('#'+preview_img).html(imgs);
          if(oFREvent.target.result){
              $('#chat-input').val('attachment');
              document.getElementById("asyncChatbutton").disabled = false;

              $('#showPreviewImg').css('display','block');
          }
        }
      } else {
        setTimeout(change_img, 2000);
      }
    }

    // Live msg check whiteSpace 
        function checkmsglive(obj){
            var msg =$(obj).val();
            if (msg.replace(/\s/g, "").length) {                
                document.getElementById("asyncChatbutton").disabled = false;
            } else {
                document.getElementById("asyncChatbutton").disabled = true;
            }        
        }
    // End validation 

</script>


@endsection