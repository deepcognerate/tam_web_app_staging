@extends('layouts.admin')
@section('content')
<style>
     .dt-buttons{
        display: none;
    }
</style>
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
@endsection
@section('scripts')
@parent
<script>

    $(document).ready(function(){
        $('#usersWaitingList').DataTable({ 
                select: false,
                "columnDefs": [{
                "visible": false,
                "ordering": true,
                "searchable":false
            }]
        });

        setTimeout(usersWaitingListget, 500);
        setInterval(function(){ 
            ajax_table_refresh();
        }, 1000)

        setInterval(function(){ 
            usersWaitingListget();
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
                    }
                }
            });       
    }
</script>
@endsection