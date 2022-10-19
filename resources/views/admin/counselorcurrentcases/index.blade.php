@extends('layouts.admin')
@section('content')
@can('counselor_current_cases_create')
@endcan


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

 <input type="hidden" id="counselorLiveChatidds" value="<?php if(!empty($counselorLiveChats)) { echo count($counselorLiveChats);} ?>">

@endsection

@section('scripts')
@parent
<script>
function liveCurentCaseFilter(id,tableName){
            var category_id = $('#'+id).val();
            $.ajax({
                 url: "{{url('admin/live-curent-chat-list-admin')}}",
                 method:"GET",
                 data:{category_id:category_id},        
                 success: function(result){
                    $('#'+tableName).html(result.html_content);
                 }
             });
        }
</script>
<script>
         // setInterval(liveCurentCaseFilter_check_db, 1000);

          function liveCurentCaseFilter_check_db(){
            var category_id = 'all';
            var count = $('#counselorLiveChatidds').val();
           
            $.ajax({
                 url: "{{url('admin/live-curent-chat-list-admin-check-db')}}",
                 method:"GET",
                 data:{category_id:category_id ,count:count},        
            success: function(result){
                    if (result.success == true) {
                        window.location.href ="";
                    }
            }
            });
        }
    </script>

    <script>
        $('#chatsinProgressList').DataTable({ 
                select: false,
                "columnDefs": [{
                "visible": false,
                "ordering": true,
                "searchable":false
            }]
        });

        chatsinProgress();
        $(document).ready(function(){
            setInterval(function(){
                chatsinProgress();
            }, 5000);
        });
    

         function chatsinProgress(){

            var category_id = 'all';
            var table2 = $('#chatsinProgressList').DataTable();
               

            $.ajax({
                    url: "{{url('admin/counselor-live-chat-progress-list')}}",
                    // beforeSend: function() {
                    //     $('#loader').removeClass('hidden')
                    // },
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
      
@endsection