@extends('layouts.admin')
@section('content')
<link rel="stylesheet" href="{{asset('public/chatboat/assets/vendors/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('public/chatboat/assets/vendors/css/vendor.bundle.base.css')}}">

  <link rel="stylesheet" href="{{asset('public/chatboat/assets/css/chatsection.css')}}">
  <link rel="stylesheet" href="{{asset('public/chatboat/assets/js/model.js')}}">

  <!-- End layout styles -->
  <link rel="shortcut icon" href="{{asset('public/chatboat/assets/images/favicon.ico')}}" />

  <div class="card">
      <div class="card-header">
          Chat History for User: @if(!empty($users->name)) {{ $users->name }} @else  @endif
      </div>
      <div class="card-body">
      
        @if(!empty($chatHistorys))
                @foreach($chatHistorys as $chatHistory)
                <?php $date = date('Y-m-d',strtotime($chatHistory->created_at));
                $time = date('H:i:s',strtotime($chatHistory->created_at)); ?>

                @if($chatHistory->status == 1)
                <div id="cm-msg-2" class="chat-msg user" >
      <div class="cm-msg-text"><span>{{ $chatHistory->message }}</span></br><small>{{ $date }} {{ $time }}</small></div>        
                </div>
                @else
                 @if($chatHistory->msgType == 1)
                <div id="cm-msg-1" class="chat-msg self" >
                          
                    <div class="cm-msg-text"><span><embed src="{{asset('public/chatAttachment/')}}/<?php echo $chatHistory->chatAttachment; ?>" width= "300" height= "300"></span></br><small style="float:right;">{{ $date }} {{ $time }}</small></div>        
                </div>
                @endif
                   @if($chatHistory->message != 'attachment')
                    <div id="cm-msg-1" class="chat-msg self" >
                     <div class="cm-msg-text"><span>{{ $chatHistory->message }}</span></br><small style="float:right;">{{ $date }} {{ $time }}</small></div>        
                    </div>
                    @endif
                
                @endif
                @endforeach 
                @endif
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
  <script>
  $(document).on("click", function(e){
    if($(e.target).is("#period_select_range_btn")){
      $("#selectPeriodRangePanel").show();
    }else{
        $("#selectPeriodRangePanel").hide();
    }
});
</script>


@endsection