@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
      Chat History
    </div>
    <div class="card-body">
    <div class="row">
    <div class="col-md-3">
                    <div class="m-form__section m-form__section--first">
                            <div class="form-group">
                                <label class="form-control-label">Start Date</label>
                                <input class="form-control date" type="text" name="fromdate" id="m_datepicker_2" placeholder="Start Date" required>
                            </div>
                        </div>
                </div>
                <div class="col-md-3">
                    <div class="m-form__section m-form__section--first">
                            <div class="form-group">
                                <label class="form-control-label">End Date </label>
                                <input class="form-control date" type="text" name="todate" id="m_datepicker_3" placeholder="End Date" required>
                            </div>
                        </div>
                </div>
                
                <div class="col-md-3">
                    <label class="required" for="chat_type">Chat Type</label>    
                        <select class="form-control select2 {{ $errors->has('chat_type') ? 'is-invalid' : '' }}" name="chattype" id="chat_typemyChat">
                        <option value="">Select Chat type</option>
                        <option value="1">Live</option>
                        <option value="2">Async</option>
                        </select>
                </div>
                <div class="col-md-3">
                    <div class="form-group filter">
                        <button class="btn btn-primary" onClick="pastchatCounselorReport();" type="submit">
                        Apply Filter
                        </button>
                    </div>
                </div>
            </div>
        <div class="table-responsive" id="filterdata">
            <table class=" table   table-striped   datatable datatable-User" id="pastChat">
                <thead>
                    <tr>
                        <th>{{ trans('cruds.counselor.fields.id') }}</th>
                        <th>{{ trans('cruds.counselor.fields.date') }}</th>
                        <th>{{ trans('cruds.counselor.fields.user_name') }}</th>
                        <th>{{ trans('cruds.counselor.fields.age') }}</th>
                        <th>{{ trans('cruds.counselor.fields.gender') }}</th>
                        <th>{{ trans('cruds.counselor.fields.topic') }}</th>
                        <th>{{ trans('cruds.counselor.fields.chat_type') }}</th>
                        <th>Escalation Reason</th>
                        <th>Counsellor Remarks</th>
                        <th>Issue Code</th>
                        <th>{{ trans('cruds.counselor.fields.feedback') }} </th>
                        <th>User Comments</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>                      
                </tbody>
            </table>
        </div>

        <div class="table-responsive">
            <table class=" table   table-striped   datatable datatable-User" id="chat_typemyChatAsync">
                <thead>
                    <tr>
                        <th>{{ trans('cruds.counselor.fields.id') }}</th>
                        <th>{{ trans('cruds.counselor.fields.date') }}</th>
                        <th>{{ trans('cruds.counselor.fields.user_name') }}</th>
                        <th>{{ trans('cruds.counselor.fields.age') }}</th>
                        <th>{{ trans('cruds.counselor.fields.gender') }}</th>
                        <th>{{ trans('cruds.counselor.fields.topic') }}</th>
                        <th>{{ trans('cruds.counselor.fields.chat_type') }}</th>
                        <th>Escalation Reason</th>
                        <th>Counsellor Remarks</th>
                        <th>Issue Code</th>
                        <th>{{ trans('cruds.counselor.fields.feedback') }} </th>
                        <th>User Comments</th>
                        <th>Action</th>
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

    $('#pastChat').DataTable({ 
        select: false,
        "columnDefs": [{
        "visible": false,
        "ordering": false,
        "searchable":false
        }]
    });

    $('#chat_typemyChatAsync').DataTable({ 
        select: false,
        "columnDefs": [{
        "visible": false,
        "ordering": false,
        "searchable":false
        }]
    });

    pastchatCounselorReport();
    pastchatCounselorReportAsync();

    function pastchatCounselorReport(){

        var fromdate = $('#m_datepicker_2').val();
        var todate = $('#m_datepicker_3').val();
        var chattype = $("#chat_typemyChat").val();

        var table2 = $('#pastChat').DataTable();               

        $.ajax({
            url: "{{url('admin/past-chat-counselor-report')}}",
            method:"GET",
            data:{fromdate:fromdate,todate:todate,chattype:chattype},        
            success: function(result){
                table2.clear(); 
                $.each(result["liveHistorys"], function (idx, obj) {

                    var assignButton = '<a href="{{ route('admin.past-chat-history.show','') }}/'+result["liveHistorys"][idx]["session_id"]+'" class="iconeye btn btn-gradient-primary btn-rounded btn-icon"><i class="fa fa-eye" aria-hidden="true"></i></a>'


                    var date = dateFormat(result["liveHistorys"][idx]["created_at"], 'MM-dd-yyyy');

                    var ratingYellow = '';
                    if(result["liveHistorys"][idx]["chat_type"] == 1){
                        var reviews  = result["liveHistorys"][idx]["feedback_star_reviews"];
                        
                          if(reviews >='1'){
                                for (let i = 1; i < 6; i++) {
                                    if(reviews >= i ){
                                        ratingYellow += '<span class="fa fa-star" style="color: orange;"></span>';
                                    } else {
                                        ratingYellow += '<span class="fa fa-star" ></span>';
                                    }
                                    
                                }
                          } else {
                            ratingYellow = 'NA';
                          }
                        var chatType = "Live";
                    } else {
                        var chatType = "Async";
                    }

                    table2.table().rows.add([[idx+1,date,
                        result["liveHistorys"][idx]["get_user"]["name"],
                        result["liveHistorys"][idx]["get_user"]["age"],
                        result["liveHistorys"][idx]["get_user"]["gender"],
                        result["liveHistorys"][idx]["get_category"]["category_name"],
                        chatType,
                        result["liveHistorys"][idx]["close_reason"],
                        result["liveHistorys"][idx]["close_remark"],

                        result["liveHistorys"][idx]["close_issue_code"],
                        
                        ratingYellow,
                        result["liveHistorys"][idx]["feedback_comment"],
                        assignButton]]);                            

                });  

                 $.each(result["liveHistorysAssign_by"], function (idx, obj) {

                    var assignButton = '<a href="{{ route('admin.past-chat-history.show','') }}/'+result["liveHistorysAssign_by"][idx]["session_id"]+'" class="iconeye btn btn-gradient-primary btn-rounded btn-icon"><i class="fa fa-eye" aria-hidden="true"></i></a>'


                    var date = dateFormat(result["liveHistorysAssign_by"][idx]["created_at"], 'MM-dd-yyyy');

                    var ratingYellow = '';
                    if(result["liveHistorysAssign_by"][idx]["chat_type"] == 1){
                        var reviews  = result["liveHistorysAssign_by"][idx]["feedback_star_reviews"];
                        
                          if(reviews >='1'){
                                for (let i = 1; i < 6; i++) {
                                    if(reviews >= i ){
                                        ratingYellow += '<span class="fa fa-star" style="color: orange;"></span>';
                                    } else {
                                        ratingYellow += '<span class="fa fa-star" ></span>';
                                    }
                                    
                                }
                          } else {
                            ratingYellow = 'NA';
                          }
                        var chatType = "Live";
                    } else {
                        var chatType = "Async";
                    }

                    table2.table().rows.add([[idx+1,date,
                        result["liveHistorysAssign_by"][idx]["get_user"]["name"],
                        result["liveHistorysAssign_by"][idx]["get_user"]["age"],
                        result["liveHistorysAssign_by"][idx]["get_user"]["gender"],
                        result["liveHistorysAssign_by"][idx]["get_category"]["category_name"],
                        chatType,
                        result["liveHistorysAssign_by"][idx]["close_reason"],
                        result["liveHistorysAssign_by"][idx]["close_remark"],

                        result["liveHistorysAssign_by"][idx]["close_issue_code"],
                        
                        ratingYellow,
                        result["liveHistorysAssign_by"][idx]["feedback_comment"],
                        assignButton]]);                            

                });  
                                     
                table2.draw(false);                    
            }
        });
    }

    function pastchatCounselorReportAsync(){

        var fromdate = $('#m_datepicker_2').val();
        var todate = $('#m_datepicker_3').val();
        var chattype = $("#chat_typemyChat").val();

        var table3 = $('#chat_typemyChatAsync').DataTable();               

        $.ajax({
            url: "{{url('admin/past-chat-counselor-report-async')}}",
            method:"GET",
            data:{fromdate:fromdate,todate:todate,chattype:chattype},        
            success: function(result){
                table3.clear(); 
                $.each(result["chatHistorysAsync"], function (idx, obj) {

                    var assignButton = '<a href="{{ route('admin.past-chat-history-async.showAsync','') }}/'+result["chatHistorysAsync"][idx]["session_id"]+'" class="iconeye btn btn-gradient-primary btn-rounded btn-icon"><i class="fa fa-eye" aria-hidden="true"></i></a>'


                    var date = dateFormat(result["chatHistorysAsync"][idx]["created_at"], 'MM-dd-yyyy');

                    var ratingYellow = '';
                    if(result["chatHistorysAsync"][idx]["chat_type"] == 2){
                        var reviews  = result["chatHistorysAsync"][idx]["feedback_star_reviews"];
                        
                          if(reviews >='1'){
                                for (let i = 1; i < 6; i++) {
                                    if(reviews >= i ){
                                        ratingYellow += '<span class="fa fa-star" style="color: orange;"></span>';
                                    } else {
                                        ratingYellow += '<span class="fa fa-star" ></span>';
                                    }
                                    
                                }
                          } else {
                            ratingYellow = 'NA';
                          }
                        var chatType = "Live";
                    } else {
                        var chatType = "Async";
                    }

                    table3.table().rows.add([[idx+1,date,
                        result["chatHistorysAsync"][idx]["get_user"]["name"],
                        result["chatHistorysAsync"][idx]["get_user"]["age"],
                        result["chatHistorysAsync"][idx]["get_user"]["gender"],
                        result["chatHistorysAsync"][idx]["get_category"]["category_name"],
                        chatType,
                        result["chatHistorysAsync"][idx]["close_reason"],
                        result["chatHistorysAsync"][idx]["close_remark"],

                        result["chatHistorysAsync"][idx]["close_issue_code"],
                        
                        ratingYellow,
                        result["chatHistorysAsync"][idx]["feedback_comment"],
                        assignButton]]);                            

                });                       
                table3.draw(false);                    
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
</script> 

    </script>
@endsection