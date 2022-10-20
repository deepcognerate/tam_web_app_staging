@extends('layouts.admin')
@section('content')
<style type="text/css">
    #select2-category_id-results{
        padding: 10px;
    }
</style>
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            
        </div>
    </div>
<div class="card">
    <div class="card-header">
      Live Waiting Escalated Chats
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table   table-striped   datatable datatable-counselors" 
            id="liveWaitingEscalatedChats">
                <thead>
                    <tr>                                               
                        <th> S.No. </th>
                        <th> {{ trans('cruds.counselor.fields.user_name') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.age') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.gender') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.topic') }}</th>
                        <th>User Location</th>
                        <th>Escalation Reason</th>
                        <th>Escalated By</th>
                        <th width="100px !important">Counsellor Assignment</th>
                        <th width="250px !important">Assign To</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    </div>
    <div class="card">
    <div class="card-header">
      Live Escalated Chats
    </div>

    <div class="card-body">
            
        <div class="table-responsive">
            <table class=" table   table-striped   datatable datatable-counselors" id="liveAssignEscalatedChats">
                <thead>
                    <tr>                        
                        <th>S.No.</th>
                        <th>{{ trans('cruds.counselor.fields.user_name') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.age') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.gender') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.topic') }}</th>
                        <th>User Location</th>
                        <th>Queue No</th>                        
                        <th>Escalation Reason</th>
                        <th>Escalated By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
               
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <div class="card">
    <div class="card-header">
     Async Escalated Chats
    </div>

    <div class="card-body">
        <div class="row">
                <div class="col-md-3">
                </div>

                <div class="col-md-4">
                    <label class="required" for="category_id">Topics</label>    
                        <select class="form-control select2 {{ $errors->has('category_id') ? 'is-invalid' : '' }}" name="category_id" id="category_id_async_list">
                        <option value="all" selected>All Topics</option>
                          @foreach($categorys as $category)
                           <option value="{{$category->id}}">{{$category->category_name }}</option>
                          @endforeach
                        </select>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group filter">
                        <button class="btn btn-primary" onClick="asyncEscalatedChatList('category_id_async','category_id_async_list');" type="submit">
                        Apply Filter
                        </button>
                    </div>
                </div>
            </div>
        <div class="table-responsive">
            <table class=" table   table-striped   datatable datatable-counselors" id="asyncAssignEscalatedChats">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>{{ trans('cruds.counselor.fields.user_name') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.age') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.gender') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.topic') }}</th>
                        <th>User Location</th>
                        <th>Escalated By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
          <div class="row">
                <div class="col-md-2">
                    <div class="m-form__section m-form__section--first">
                            <div class="form-group">
                                <label class="form-control-label">Start Date</label>
                                <input class="form-control date" type="text" name="fromdate" id="m_datepicker_2" placeholder="Start Date" required>
                            </div>
                        </div>
                </div>
                <div class="col-md-2">
                    <div class="m-form__section m-form__section--first">
                            <div class="form-group">
                                <label class="form-control-label">End Date </label>
                                <input class="form-control date" type="text" name="todate" id="m_datepicker_3" placeholder="End Date" required>
                            </div>
                        </div>
                </div>
                <div class="col-md-2">
                    <label class="required" for="name">Counsellor Name</label>    
                        <select class="form-control select2 {{ $errors->has('name') ? 'is-invalid' : '' }}" name="counsellor" id="counsellorMychat">
                        <option value="">All Counsellor</option>
                          @foreach($counsellors as $counsellor)
                           <option value="{{$counsellor->id}}">{{$counsellor->name }}</option>
                          @endforeach
                        </select>
                </div>
                <div class="col-md-2">
                    <label class="required" for="category_id">Topics</label>    
                        <select class="form-control select2 {{ $errors->has('category_id') ? 'is-invalid' : '' }}" name="category_id" id="category_idmyChat">
                        <option value="">All Topics</option>
                          @foreach($categorys as $category)
                           <option value="{{$category->id}}">{{$category->category_name }}</option>
                          @endforeach
                        </select>
                </div>
                <div class="col-md-2">
                    <label class="required" for="chat_type">Chat Type</label>    
                        <select class="form-control select2 {{ $errors->has('chat_type') ? 'is-invalid' : '' }}" name="chattype" id="chat_typemyChat">
                        <option value="">Select Chat type</option>
                        <option value="1">Live</option>
                        <option value="2">Async</option>
                        </select>
                </div>
                <div class="col-md-2">
                    <div class="form-group filter">
                        <button class="btn btn-primary" onClick="pastchatReport();" type="submit">
                        Apply Filter
                        </button>
                    </div>
                </div>
            </div>
    </div>
</div>
<div class="card">
    <div class="card-body"> 
        <div class="card-header">
            Live My Chat History 
        </div>
         <div class="table-responsive">
            <table class="table table-striped datatable datatable-counselors" id="liveEscalateClose">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Date</th>
                        <th>{{ trans('cruds.counselor.fields.user_name') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.age') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.gender') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.topic') }}</th>
                        <th>Chat type</th>
                        <th>Ops Lead Remarks</th>
                        <th>Issue Code</th>
                        <th>Escalated By</th>
                        <th>User Feedback </th>
                        <th> Action </th>
                    </tr>
                </thead>
                <tbody>
                
                </tbody>
            </table>
            </div>

    </div>
</div>
<div class="card">
    <div class="card-body"> 
        <div class="card-header">
             Async My Chat History 
        </div>
        <div class="table-responsive">
            <table class="table table-striped datatable datatable-counselors" id="myChatAsync">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Date</th>
                        <th>{{ trans('cruds.counselor.fields.user_name') }}</th>
                        <th> User {{ trans('cruds.counselor.fields.age') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.gender') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.topic') }}</th>
                        <th>Chat type</th>
                        <th>Ops Lead Remarks</th>
                        <th>Issue Code </th>
                        <th>Escalated By</th>
                        <th> User Feedback </th>
                        <th> Action </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')


    <script>

    $('#liveWaitingEscalatedChats').DataTable({ 
            select: false,
            "columnDefs": [{
            "visible": false,
            "ordering": false,
            "searchable":false
            }]
    });

    $('#liveAssignEscalatedChats').DataTable({ 
        select: false,
        "columnDefs": [{
        "visible": false,
        "ordering": false,
        "searchable":false
        }]
    });

    $('#liveEscalateClose').DataTable({ 
        select: false,
        "columnDefs": [{
        "visible": false,
        "ordering": false,
        "searchable":false
        }]
    });

    $('#asyncAssignEscalatedChats').DataTable({ 
        select: false,
        "columnDefs": [{
        "visible": false,
        "ordering": false,
        "searchable":false
        }]
    });

    $('#myChatAsync').DataTable({ 
        select: false,
        "columnDefs": [{
        "visible": false,
        "ordering": false,
        "searchable":false
        }]
    });


    waitingChatsinProgress();
    liveAssignEscalatedChats();
    liveEscalateClose();
    asyncEscalatedChatList();
    asyncEscalateClose();

    function pastchatReport(){
        liveEscalateClose();
        asyncEscalateClose();
    }
    
    function waitingChatsinProgress(){

        var category_id = 'all';
        var table2 = $('#liveWaitingEscalatedChats').DataTable();               

        $.ajax({
            url: "{{url('admin/live-Waiting-Escalated-Chats')}}",
            method:"GET",
            data:{category_id:category_id},        
            success: function(result){
                table2.clear(); 
                $.each(result["liveWaitingEscalated"], function (idx, obj) {

                    var assignButton = '<a class="btn btn-gradient-primary btn-rounded btn-icon submit" onclick="userAssignToAdmin('+result["liveWaitingEscalated"][idx]["session_id"]+');" style="width: 70px;" title="MySelf">MySelf</a> <a class="btn btn-gradient-primary btn-rounded btn-icon submit" onclick="userAssignToCounselor('+result["liveWaitingEscalated"][idx]["session_id"]+');" style="width: 80px;" title="Counsellor">Counsellor</a>'

                    var dropDown  = '<select style="width: 90px;" class="form-control select2 " name="counselor" id="counsellor'+result["liveWaitingEscalated"][idx]["session_id"]+'">'+result["counsellor"]+'</select>'
                            
                    table2.table().rows.add([[idx+1,result["liveWaitingEscalated"][idx]["get_user"]["name"],result["liveWaitingEscalated"][idx]["get_user"]["age"],result["liveWaitingEscalated"][idx]["get_user"]["gender"],result["liveWaitingEscalated"][idx]["get_category"]["category_name"],result["liveWaitingEscalated"][idx]["get_user"]["location"],result["liveWaitingEscalated"][idx]["escalated_reason"],result["liveWaitingEscalated"][idx]["get_user_counselor"]["name"],dropDown,assignButton]]);                            

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
            url: "{{url('admin/admin-escalated-Assign-To')}}",
            method:"GET",
            data:{counsellor:counsellor,sessionId:sessionId},        
            success: function(dataResult){
                alert(dataResult.msg);
                if (dataResult.success == true) {
                    waitingChatsinProgress();
                    liveAssignEscalatedChats();
                }
            }
        });       
    }

    function userAssignToAdmin(sessionId) { 
        var counsellor = ''; 
        $.ajax({
                url: "{{url('admin/admin-escalated-Assign-To')}}",
                method:"GET",
                data:{sessionId:sessionId,counsellor:counsellor},        
                success: function(dataResult){
                    alert(dataResult.msg);
                    if (dataResult.success == true) {               
                        waitingChatsinProgress();
                        liveAssignEscalatedChats();
                    }
                }
            });       
    }


    function liveAssignEscalatedChats(){

        var category_id = 'all';
        var table2 = $('#liveAssignEscalatedChats').DataTable();               

        $.ajax({
            url: "{{url('admin/my-chat-admin-report-live')}}",
            method:"GET",
            data:{category_id:category_id},        
            success: function(result){
                table2.clear(); 
                $.each(result["liveChat"], function (idx, obj) {


                    var assignButton = '<a href="{{ route('admin.counselor-live-chat.counselorLiveChat','') }}/'+result["liveChat"][idx]["session_id"]+'" class="round-button_active"><i class="fa fa-play fa-2x"></i></a>'
                            
                    table2.table().rows.add([[idx+1,result["liveChat"][idx]["get_user"]["name"],result["liveChat"][idx]["get_user"]["age"],result["liveChat"][idx]["get_user"]["gender"],result["liveChat"][idx]["get_category"]["category_name"],result["liveChat"][idx]["get_user"]["location"],idx+1,result["liveChat"][idx]["escalated_reason"],result["liveChat"][idx]["get_user_counselor"]["name"],assignButton]]);                            

                }); 
                $.each(result["liveEscalated"], function (idx, obj) {


                    var assignButton = '<a href="{{ route('admin.counselor-live-chat.counselorLiveChat','') }}/'+result["liveEscalated"][idx]["session_id"]+'" class="round-button_active"><i class="fa fa-play fa-2x"></i></a>'
                            
                    table2.table().rows.add([[idx+1,result["liveEscalated"][idx]["get_user"]["name"],result["liveEscalated"][idx]["get_user"]["age"],result["liveEscalated"][idx]["get_user"]["gender"],result["liveEscalated"][idx]["get_category"]["category_name"],result["liveEscalated"][idx]["get_user"]["location"],idx+1,result["liveEscalated"][idx]["escalated_reason"],result["liveEscalated"][idx]["get_user_counselor"]["name"],assignButton]]);                            

                });                       
                table2.draw(false);                    
            }
        });
    }

    function liveEscalateClose(){

        var fromdate = $('#m_datepicker_2').val();
        var todate = $('#m_datepicker_3').val();
        var counsellor = $("#counsellorMychat").val();
        var category = $("#category_idmyChat").val();
        var chattype = $("#chat_typemyChat").val();

        var table2 = $('#liveEscalateClose').DataTable();               

        $.ajax({
            url: "{{url('admin/live-Escalated-close')}}",
            method:"GET",
            data:{fromdate:fromdate,todate:todate,counsellor:counsellor,category:category,chattype:chattype},        
            success: function(result){
                table2.clear(); 
                $.each(result["chatHistorys"], function (idx, obj) {


                    var assignButton = '<a href="{{ route('admin.past-chat-history.show','') }}/'+result["chatHistorys"][idx]["session_id"]+'" class="iconeye btn btn-gradient-primary btn-rounded btn-icon"><i class="fa fa-eye" aria-hidden="true"></i></a>'


                    var date = dateFormat(result["chatHistorys"][idx]["created_at"], 'MM-dd-yyyy');
                    if(result["chatHistorys"][idx]["chat_type"] == 1){
                        var chatType = "Live";
                    } else {
                        var chatType = "Async";
                    }

                    table2.table().rows.add([[idx+1,date,
                        result["chatHistorys"][idx]["get_user"]["name"],
                        result["chatHistorys"][idx]["get_user"]["age"],
                        result["chatHistorys"][idx]["get_user"]["gender"],
                        result["chatHistorys"][idx]["get_category"]["category_name"],
                        chatType,
                        result["chatHistorys"][idx]["close_remark"],
                        result["chatHistorys"][idx]["close_issue_code"],
                        result["chatHistorys"][idx]["get_user_counselor"]["name"],
                        result["chatHistorys"][idx]["feedback_comment"],
                        assignButton]]);                            

                });                       
                table2.draw(false);                    
            }
        });
    }

    function dateFormat(inputDate, format) {
        //parse the input date
        const date = new Date(inputDate);

        //extract the parts of the date
        const day = date.getDate();
        const month = date.getMonth() + 1;
        const year = date.getFullYear();    

        //replace the month
        format = format.replace("MM", month.toString().padStart(2,"0"));        

        //replace the year
        if (format.indexOf("yyyy") > -1) {
            format = format.replace("yyyy", year.toString());
        } else if (format.indexOf("yy") > -1) {
            format = format.replace("yy", year.toString().substr(2,2));
        }

        //replace the day
        format = format.replace("dd", day.toString().padStart(2,"0"));

        return format;
    }

    function asyncEscalatedChatList(){

        var category_id = $('#category_id_async_list').val();
        var tableAsync = $('#asyncAssignEscalatedChats').DataTable();               

        $.ajax({
            url: "{{url('admin/my-chat-admin-report-async')}}",
            method:"GET",
            data:{category_id:category_id},        
            success: function(result){
                tableAsync.clear(); 
                $.each(result["AsyncEscalated"], function (idx, obj) {

                    var assignButton = '<a href="{{ route('admin.counselor-assign-user-admin.counselorAssignUserAdmin','') }}/'+result["AsyncEscalated"][idx]["session_id"]+'" class="round-button_active"><i class="fa fa-play fa-2x"></i></a> ';

                    
                            
                    tableAsync.table().rows.add([[idx+1,result["AsyncEscalated"][idx]["get_user"]["name"],result["AsyncEscalated"][idx]["get_user"]["age"],result["AsyncEscalated"][idx]["get_user"]["gender"],result["AsyncEscalated"][idx]["get_category"]["category_name"],result["AsyncEscalated"][idx]["get_user"]["location"],result["AsyncEscalated"][idx]["get_user_counselor"]["name"],assignButton]]);                            

                });                       
                tableAsync.draw(false);                    
            }
        });
    }

    function asyncEscalateClose(){

        var fromdate = $('#m_datepicker_2').val();
        var todate = $('#m_datepicker_3').val();
        var counsellor = $("#counsellorMychat").val();
        var category = $("#category_idmyChat").val();
        var chattype = $("#chat_typemyChat").val();

        if(fromdate > todate){
            alert("Please selecte to date is greater then from date");
            return false;
        }

        var tableAsyncClose = $('#myChatAsync').DataTable();               

        $.ajax({
            url: "{{url('admin/my-chat-admin-report')}}",
            method:"GET",
            data:{fromdate:fromdate,todate:todate,counsellor:counsellor,category:category,chattype:chattype},        
            success: function(result){
                tableAsyncClose.clear(); 
                $.each(result["chatHistorysAsync"], function (idx, obj) {


                    var assignButton = '<a href="{{ route('admin.past-chat-history-async.showAsync','') }}/'+result["chatHistorysAsync"][idx]["session_id"]+'" class="iconeye btn btn-gradient-primary btn-rounded btn-icon"><i class="fa fa-eye" aria-hidden="true"></i></a>'


                    var date = dateFormat(result["chatHistorysAsync"][idx]["created_at"], 'MM-dd-yyyy');
                    if(result["chatHistorysAsync"][idx]["chat_type"] == 1){
                        var chatType = "Live";
                    } else {
                        var chatType = "Async";
                    }

                    tableAsyncClose.table().rows.add([[idx+1,date,
                        result["chatHistorysAsync"][idx]["get_user"]["name"],
                        result["chatHistorysAsync"][idx]["get_user"]["age"],
                        result["chatHistorysAsync"][idx]["get_user"]["gender"],
                        result["chatHistorysAsync"][idx]["get_category"]["category_name"],
                        chatType,
                        result["chatHistorysAsync"][idx]["close_remark"],
                        result["chatHistorysAsync"][idx]["close_issue_code"],
                        result["chatHistorysAsync"][idx]["get_user_counselor"]["name"],
                        result["chatHistorysAsync"][idx]["feedback_comment"],
                        assignButton]]);                            

                });  

                $.each(result["chatHistorysAssign_byAsync"], function (idx, obj) {


                    var assignButton = '<a href="{{ route('admin.past-chat-history-async.showAsync','') }}/'+result["chatHistorysAssign_byAsync"][idx]["session_id"]+'" class="iconeye btn btn-gradient-primary btn-rounded btn-icon"><i class="fa fa-eye" aria-hidden="true"></i></a>'


                    var date = dateFormat(result["chatHistorysAssign_byAsync"][idx]["created_at"], 'MM-dd-yyyy');
                    if(result["chatHistorysAssign_byAsync"][idx]["chat_type"] == 1){
                        var chatType = "Live";
                    } else {
                        var chatType = "Async";
                    }

                    tableAsyncClose.table().rows.add([[idx+1,date,
                        result["chatHistorysAssign_byAsync"][idx]["get_user"]["name"],
                        result["chatHistorysAssign_byAsync"][idx]["get_user"]["age"],
                        result["chatHistorysAssign_byAsync"][idx]["get_user"]["gender"],
                        result["chatHistorysAssign_byAsync"][idx]["get_category"]["category_name"],
                        chatType,
                        result["chatHistorysAssign_byAsync"][idx]["close_remark"],

                        result["chatHistorysAssign_byAsync"][idx]["close_reason"],
                        result["chatHistorysAssign_byAsync"][idx]["get_counselor_assign_by"]["name"],
                        result["chatHistorysAssign_byAsync"][idx]["feedback_comment"],
                        assignButton]]);                            

                });                     
                tableAsyncClose.draw(false);                    
            }
        });
    }

</script> 
@endsection