@extends('layouts.admin')
@section('content')
<style type="">
    .dt-buttons{
        display: none;
    }

</style>
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            
        </div>
    </div>

    <div class="card">
       
    <div class="card-header">
    Assigned Live Chats 
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class=" table   table-striped   datatable datatable-counselors" id="counsellorLiveChatList">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>{{ trans('cruds.counselor.fields.user_name') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.age') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.gender') }}</th>
                        <th> User {{ trans('cruds.counselor.fields.topic') }}</th>
                        <th>Queue No</th>
                        <th>User Location</th>
                        <th>Chat Type</th>
                        <th>Activate</th>
                    </tr>
                </thead>
                <tbody>
                
                </tbody>
            </table>
            <input type="hidden" id="ajax_table_refresh_live" value="00">
        </div>
    </div>
</div>

    <div class="card">
       
    <div class="card-header">
    Assigned Escalated Live Chats 
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class=" table   table-striped   datatable datatable-counselors" id="counsellorLiveChatList">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>{{ trans('cruds.counselor.fields.user_name') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.age') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.gender') }}</th>
                        <th> User {{ trans('cruds.counselor.fields.topic') }}</th>
                        <th>Queue No</th>
                        <th>User Location</th>
                        <th>Chat Type</th>
                        <th>Activate</th>
                    </tr>
                </thead>
                <tbody>
                
                </tbody>
            </table>
            <input type="hidden" id="ajax_table_refresh_live" value="00">
        </div>
    </div>
</div>
    
    <div class="card">
        
    <div class="card-header">
    Async Chats 
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table   table-striped   datatable datatable-counselors" id="counselorCurrentChatsAsynclist">
                <thead>
                    <tr>
                        <th> S.No. </th>
                        <th>{{ trans('cruds.counselor.fields.user_name') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.age') }}</th>
                        <th>User {{ trans('cruds.counselor.fields.gender') }}</th>
                        <th> User {{ trans('cruds.counselor.fields.topic') }}</th>
                        <th> Escalation Time </th>
                        <th>User Location</th>
                        <th> Chat Type</th>
                        <th>Activate </th>
                    </tr>
                </thead>
                <tbody>
                
                </tbody>
            </table>
            <input type="hidden" id="ajax_table_refresh_async" value="00">           
        </div>
    </div>
</div>


</div>

@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('counselor_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.counselors.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-counselor:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})
</script>

<script>
    
    $(document).ready(function(){

        $('#counselorCurrentChatsAsynclist').DataTable({ 
                select: false,
                "columnDefs": [{
                "visible": false,
                "ordering": true,
                "searchable":false
            }]
        });

        
        setInterval(function(){
            ajax_table_refresh();
        }, 1000);
        
    });

   

    $(document).ready(function(){
        setInterval(function(){
            time_left_admin_assign();
        }, 1000);

        setInterval(function(){
            asyncCurentCase();
        }, 1000);
    });
    
    function time_left_admin_assign(){
      
        $.ajax({
             url: "{{url('admin/time-left-user-assign-admin')}}",
             method:"GET",
             success: function(result){
              if (result.success == true) {
                alert("User assign to admin successfully..!");
                window.location.href ="{{ route('admin.counselorcurrentcases.index') }}";
              } else {
                console.log(result.msg);
              }
             }
         });
    }    

</script>

<script>
        // setTimeout(liveCurentCase, 500);
        setTimeout(asyncCurentCase, 500);

         function ajax_table_refresh(){
            var countAsync = $('#ajax_table_refresh_async').val();
            var countLive = $('#ajax_table_refresh_live').val();
            $.ajax({
                 url: "{{url('admin/ajax-table-refresh-Live-async')}}",
                 method:"GET",
                 data:{countAsync:countAsync,countLive:countLive},        
                 success: function(result){
                  if (result.success == true && result.msg == 'Live') {
                     liveChatList();
                  }

                  if (result.success == true && result.msg == 'async') {
                    asyncCurentCase();
                  }

                 }
             });
        }
        

        

    function asyncCurentCase(){
            var category_id = 'all';
            var table2 = $('#counselorCurrentChatsAsynclist').DataTable();
               

            $.ajax({
                    url: "{{url('admin/counselor-async-chat-list')}}",                   
                    method:"GET",
                    data:{category_id:category_id},        
                    success: function(result){
                    table2.clear();                   

                    $.each(result["counselorCurrentChats"], function (idx, obj) {


                        var id = '<a href="{{ route('admin.counselor-assign-user.counselorAssignUser','') }}/'+result["counselorCurrentChats"][idx]["session_id"]+'" class="round-button_active"><i class="fa fa-play fa-2x"></i></a>'

                        if(result["counselorCurrentChats"][idx]["chat_current_status"] == '1'){
                            table2.table().rows.add([[idx+1,result["counselorCurrentChats"][idx]["get_user"]["name"],result["counselorCurrentChats"][idx]["get_user"]["age"],result["counselorCurrentChats"][idx]["get_user"]["gender"],result["counselorCurrentChats"][idx]["get_category"]["category_name"],result["counselorCurrentChats"][idx]["time_left"],result["counselorCurrentChats"][idx]["get_user"]["location"],'Async',id]]);
                        }

                        if(result["counselorCurrentChats"][idx]["chat_current_status"] == '2' && result["counselorCurrentChats"][idx]["counsellor_id"] == result["sessionCounselorid"]){

                            table2.table().rows.add([[idx+1,result["counselorCurrentChats"][idx]["get_user"]["name"],result["counselorCurrentChats"][idx]["get_user"]["age"],result["counselorCurrentChats"][idx]["get_user"]["gender"],result["counselorCurrentChats"][idx]["get_category"]["category_name"],result["counselorCurrentChats"][idx]["time_left"],result["counselorCurrentChats"][idx]["get_user"]["location"],'Async',id]]);
                        }
                        
                        $('#ajax_table_refresh_async').val(idx);
                  });
                    table2.draw(false);                    
                 }
             });
        }


</script>

<script>

   $('#counsellorLiveChatList').DataTable({ 
                select: false,
                "columnDefs": [{
                "visible": false,
                "ordering": true,
                "searchable":false
            }]
        });
   
   liveChatList();

    function liveChatList(){
        
        var category_id = 'all';
        var table2 = $('#counsellorLiveChatList').DataTable();               

        $.ajax({
            url: "{{url('admin/counselor-Live-chat-list')}}",
            method:"GET",
            data:{category_id:category_id},        
            success: function(result){
                $('#ajax_table_refresh_live').val(result["count"]);
                table2.clear(); 
                $.each(result["liveAssignList"], function (idx, obj) {

                    var assignButton = '<a href="{{ route('admin.counselor-live-chat.counselorLiveChat','') }}/'+result["liveAssignList"][idx]["session_id"]+'" class="round-button_active"><i class="fa fa-play fa-2x"></i></a>'
                            
                    table2.table().rows.add([[idx+1,result["liveAssignList"][idx]["get_user"]["name"],result["liveAssignList"][idx]["get_user"]["age"],result["liveAssignList"][idx]["get_user"]["gender"],result["liveAssignList"][idx]["get_category"]["category_name"],idx+1,result["liveAssignList"][idx]["get_user"]["location"],'Live',assignButton]]);                            

                });                       
                table2.draw(false);                    
            }
        });
    }
</script>
@endsection