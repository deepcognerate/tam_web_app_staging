@extends('layouts.admin')
@section('content')
<style type="">
    .dt-buttons{
        display: none;
    }

</style>

<div class="page-header">
            <h3 class="page-title" style="text-align: center;
    color: #209153;
    font-size: -webkit-xxx-large;">
              <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
              </span> Welcome To The Able Mind
            </h3>
            <nav aria-label="breadcrumb">
              <ul class="">
                <!-- <li class="breadcrumb-item active" aria-current="page"> -->
                  <!-- <span></span>Overview<i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle" width="200px" height="340px" ></i> -->
                <!-- </li> -->
                <!-- <li>
                @if($sessionCounselorid != 1)
                  @if($getCounselorActive->counselor_availability == 0)
                    <p style="float:right;"><input type="button" class="btn btn-danger" name="availability" onclick="myFunction();" value="InActivate" id="inactive"></p>
                  @else
                    <p style="float:right;"><input type="button" class="btn btn-primary" name="availability" onclick="myFunction();" value="Activate" id="active"></p>
                  @endif 
                @endif
                <?php   
                $sessionCounselorid = Auth::user()->id;
                if($sessionCounselorid != 1)
                {?>
                     <label class="switch"><input type="hidden" class="tuggle"></label>
               <?php }
                  ?>
                </li> -->
              </ul>
            </nav>
          </div>
          <?php   
                $sessionCounselorid = Auth::user()->id;
                if($sessionCounselorid == 1)
                { ?>
          <div class="row">            
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Users Waiting 
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class=" table   table-striped   datatable datatable-User" id="usersWaitingList">
                                <thead>
                                    <tr>                        
                                        <th>S.No.</th>
                                        <th>{{ trans('cruds.counselor.fields.user_name') }}</th>
                                        <th>{{ trans('cruds.counselor.fields.age') }}</th>
                                        <th>{{ trans('cruds.counselor.fields.gender') }}</th>
                                        <th>{{ trans('cruds.counselor.fields.topic') }}</th>
                                        <th>{{ trans('cruds.counselor.fields.user_location') }}</th>
                                        <th>{{ trans('cruds.counselor.fields.queue_no') }}</th>
                                        <th  width="100px !important">{{ trans('cruds.counselor.fields.counselor_assignment') }}</th>
                                        
                                        <th width="160px !important">Assign To</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="users-waiting-list-count">
              </div>
                         
                <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                     Chats in Progress
                    </div>
                    <div class="card-body">
                            
                        <div class="table-responsive">
                            <table class=" table   table-striped   datatable datatable-counselors" id="chatsinProgressList">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>{{ trans('cruds.counselor.fields.user_name') }}</th>
                                        <th>User {{ trans('cruds.counselor.fields.age') }}</th>
                                        <th>User {{ trans('cruds.counselor.fields.gender') }}</th>
                                        <th>User {{ trans('cruds.counselor.fields.topic') }}</th>                      
                                        <th>Assigned To</th>
                                        <th>User Location</th>
                                        <th>Chat Type</th>
                                        <th> Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>
                            </table>
                        </div>
                    </div>

                 
                </div>
             </div>

          </div> 
          <?php } else { ?>
            <img style=" padding-top: 10%; width: 419px;margin-left: 30%;" src="<?php echo url('/public/dashbord/images');?>/Owl-Happy1.png">
          <?php } ?>  
          </div>

          <input type="hidden" id="counselorLiveChatidds" value="">
          <input type="hidden" id="waitingUsersids" value="">
      <!-- partial -->
 

@endsection
@section('scripts')
<script>
  $("document").ready(function(){
    startFCM();
});
</script>
<script>
function myFunction()
  {
    var thisUserId = $("input[name=availability]").val();
    if(thisUserId == "Activate")
    {
      var  thisStatus = 2;
    }else{
      var  thisStatus = 1;
    }
    $.ajax({
            url: "{{url('admin/counselor-availability')}}/"+ thisStatus,
            method: 'GET',
            success: function(data) {
              location.reload();
            }
        });
  }
</script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>
<script>

     var firebaseConfig = {

        // Tem washering.com/tamtes

        apiKey: 'AIzaSyBVwfEvl5Gtmi1u6Tq5q0pCDbfPugenQYE',
        authDomain: 'tam-app-dev.firebaseapp.com',
        databaseURL: 'https://auth-db206.hstgr.io/index.php?db=u789638131_tam_dev_1_5',
        projectId: 'tam-app-dev',
        storageBucket: 'tam-app-dev.appspot.com',
        messagingSenderId: '906777746662',
        appId: '1:906777746662:web:e4d6e511e2a1a4245d2f27',
        measurementId: 'G-BEFLVMNWLB',
        
        // tam.destress
        
        // apiKey: "AIzaSyAADXQHgQTNgBFZyCjDsV3W6Z7oc9B1B2g",
        // authDomain: "tam-app-staging.firebaseapp.com",
        // databaseURL: 'http://db-tam-beta.c1kzmzp0ms44.ap-south-1.rds.amazonaws.com',
        // projectId: "tam-app-staging",
        // storageBucket: "tam-app-staging.appspot.com",
        // messagingSenderId: "991731337312",
        // appId: "1:991731337312:web:ea28a22d09e482f34a9b05",
        // measurementId: "G-VVKT4ZT2YY",
    };
   
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
    function startFCM() {
        messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken()
            })
            .then(function (response) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route("admin.store.token") }}',
                    type: 'POST',
                    data: {
                        token: response
                    },
                    dataType: 'JSON',
                    success: function (response) {
                       
                    },
                    error: function (error) {
                        alert(error);
                    },
                });
            }).catch(function (error) {
                // alert(error);
            });
    }




</script>
<?php if($sessionCounselorid == 1) { ?> 
    <script>
        // function _user_waiting_filter_check_db(){
        //     var category_id = 'all';
        //     var count = $('#waitingUsersids').val();
            
        //     $.ajax({
        //          url: "{{url('admin/my-chat-admin-user-waiting-check-db')}}",
        //          method:"GET",
        //          data:{category_id:category_id ,count:count},        
        //         success: function(result){
        //                 if (result.success == true) {
        //                     if(count != '00' || count != '0'){
        //                         var table = $('#listdataUsersWaiting').DataTable();
        //                         table.destroy();
        //                     }
        //                     user_waiting_filter();
        //                 }
        //         }
        //     });
        // }

        // function liveCurentCaseFilter_check_db(){
        //     var category_id = 'all';
        //     var count = $('#counselorLiveChatidds').val();
           
        //     $.ajax({
        //          url: "{{url('admin/live-curent-chat-list-admin-check-db')}}",
        //          method:"GET",
        //          data:{category_id:category_id ,count:count},        
        //         success: function(result){
        //                 if (result.success == true) {
        //                     if(count != '00' || count != '0'){
        //                         var table = $('#listdataChatInProgress').DataTable();
        //                         table.destroy();
        //                     }
        //                     liveCurentCaseFilter();
        //                 }
        //         }
        //     });
        // }
        
        // setInterval(liveCurentCaseFilter_check_db, 3000);
        // setInterval(_user_waiting_filter_check_db, 3000);

        $(document).ready(function(){
        $('#usersWaitingList').DataTable({ 
                select: false,
                "columnDefs": [{
                "visible": false,
                "ordering": true,
                "searchable":false
            }]
        });

        $('#chatsinProgressList').DataTable({ 
                select: false,
                "columnDefs": [{
                "visible": false,
                "ordering": true,
                "searchable":false
            }]
        }); 

        setTimeout(usersWaitingListget, 500);
        setTimeout(chatsinProgress, 500);

        setInterval(function(){ 
            ajax_table_refresh();
        }, 1000)

        setInterval(function(){ 
            usersWaitingListget();
            chatsinProgress();
        }, 60000)
        
    });


    function ajax_table_refresh(){
        var userswaiting = $('#users-waiting-list-count').val();
        $.ajax({
            url: "{{url('admin/users-waiting-list-count')}}",
            method:"GET",
            data:{userswaiting:userswaiting},        
            success: function(result){
                if (result.success == true) {
                    usersWaitingListget();
                }
            }
        });
    }


    function usersWaitingListget(){
        var category_id = 'all';
        var table2 = $('#usersWaitingList').DataTable();
        $.ajax({
            url: "{{url('admin/users-waiting-list')}}",
            method:"GET",
            data:{category_id:category_id},        
            success: function(result){
                table2.clear();                   
                $('#users-waiting-list-count').val(result["countRow"]);
                $.each(result["waitingUsers"], function (idx, obj) {

                    var assignButton = '<a class="btn btn-gradient-primary btn-rounded btn-icon submit" onclick="userAssignToAdmin('+result["waitingUsers"][idx]["session_id"]+');" style="width: 70px;" title="MySelf">MySelf</a> <a class="btn btn-gradient-primary btn-rounded btn-icon submit" onclick="userAssignToCounselor('+result["waitingUsers"][idx]["session_id"]+');" style="width: 80px;" title="Counsellor">Counsellor</a>';

                    var dropDown  = '<select style="width: 90px;" class="form-control select2 " name="counselor" id="counsellor'+result["waitingUsers"][idx]["session_id"]+'">'+result["counsellor"]+'</select>';

                        
                    table2.table().rows.add([[idx+1,result["waitingUsers"][idx]["get_user"]["name"],result["waitingUsers"][idx]["get_user"]["age"],result["waitingUsers"][idx]["get_user"]["gender"],result["waitingUsers"][idx]["get_category"]["category_name"],result["waitingUsers"][idx]["get_user"]["location"],idx+1,dropDown,assignButton]]);                   
                });
                   
                table2.draw(false);
                    
            }
        });
    }

    function userAssignToCounselor(sessionId){
        var counsellor = $("#counsellor"+sessionId).val();    
        if (counsellor == null || counsellor == "") {
            alert("Please selecte Counsellor");
            return false;
        }
        $.ajax({
            url: "{{url('admin/admin-waiting-Assign-To')}}",
            method:"GET",
            data:{counsellor:counsellor,sessionId:sessionId},        
            success: function(dataResult){
                alert(dataResult.msg);
                if (dataResult.success == true) {
                    usersWaitingListget();
                    chatsinProgress();
                  
                }
            }
        });       
    }

    function userAssignToAdmin(sessionId) { 
        var counsellor = ''; 
        $.ajax({
                url: "{{url('admin/admin-waiting-Assign-To')}}",
                method:"GET",
                data:{sessionId:sessionId,counsellor:counsellor},        
                success: function(dataResult){
                    alert(dataResult.msg);
                    if (dataResult.success == true) {               
                        usersWaitingListget();
                        chatsinProgress();
                    }
                }
            });       
    }       

    function chatsinProgress(){

        var category_id = 'all';
        var table2 = $('#chatsinProgressList').DataTable();
               

        $.ajax({
            url: "{{url('admin/counselor-live-chat-progress-list')}}",
            method:"GET",
            data:{category_id:category_id},        
            success: function(result){
                table2.clear();                   
                $.each(result["counselorLiveChats"], function (idx, obj) {

                var id = '<a href="{{ route('admin.live-chat-view-admin.chat_view_show','') }}/'+result["counselorLiveChats"][idx]["session_id"]+'" class="round-button_active"><i class="fa fa-play fa-2x"></i></a>'
                        
                table2.table().rows.add([[idx+1,result["counselorLiveChats"][idx]["get_user"]["name"],result["counselorLiveChats"][idx]["get_user"]["age"],result["counselorLiveChats"][idx]["get_user"]["gender"],result["counselorLiveChats"][idx]["get_category"]["category_name"],result["counselorLiveChats"][idx]["get_user_counselor"]["name"],result["counselorLiveChats"][idx]["get_user"]["location"],'Live',id]]);
                        

                });
                   
                table2.draw(false);
                    
            }
        });
    }
 </script>
 <?php } ?>

@endsection



