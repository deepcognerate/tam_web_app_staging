<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MassDestroyCounselorAssignmentRequest;
use App\Http\Requests\StoreCounselorAssignmentRequest;
use App\Http\Requests\UpdateCounselorAssignmentRequest;
use App\Models\CounselorAssignment;
use App\Models\CounselorPastCases;
use App\Models\WaitingAssignments;
use App\Models\CounselorCurrentCases;
use App\Models\CounselorCategoryUser;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Session;
use Gate;
use Auth;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

// New code 
use App\Models\AsyncChatSessions;
use App\Models\AsyncChatAudit;
use App\Models\AsyncChatMessages;
use App\Models\ChatSessions;
use App\Models\CounsellorCategories;
use App\Models\ChatAudit;
use App\Models\ChatMessages;


class ReportController extends Controller
{

    public function pastChatAdminReport(Request $request)
    {

        $sessionCounselorid = Auth::user()->id;

        $fromdate = $request->input('fromdate');  
        $todate = $request->input('todate');   
        $chattype = $request->input('chattype');
        $counsellor = $request->input('counsellor');  
        $category = $request->input('category');  

        $CounsellorCategories = CounsellorCategories::where('counsellor_id',$sessionCounselorid)
                                                    ->whereNull('deleted_at')
                                                    ->get();

            $chatHistorys = AsyncChatSessions::with('getUser','getCategory','getUserCounselor');

            $chatHistorys->where('chat_current_status','3');
            $chatHistorys->whereNull('assign_by');

                if(!empty($chattype)){
                    $chatHistorys->where('chat_type',$chattype);
                }               

                if(!empty($fromdate) AND !empty($todate)){
                    $chatHistorys->whereDate('created_at','>=',$fromdate);
                    $chatHistorys->whereDate('created_at','<=',$todate);
                }

                if(!empty($category)){
                    $chatHistorys->where('category_id',$category);
                }

                if(!empty($counsellor)){
                    $chatHistorys->where('counsellor_id',$counsellor);
                }
           
            $data = $chatHistorys->get();


            $chatHistorysAssign_by = AsyncChatSessions::with('getUser','getCategory','getCounselorAssignBy');

            $chatHistorysAssign_by->where('chat_current_status','3');
            $chatHistorysAssign_by->whereNotNull('assign_by');

                if(!empty($chattype)){
                    $chatHistorysAssign_by->where('chat_type',$chattype);
                }               

                if(!empty($fromdate) AND !empty($todate)){
                    $chatHistorysAssign_by->whereDate('created_at','>=',$fromdate);
                    $chatHistorysAssign_by->whereDate('created_at','<=',$todate);
                }

                if(!empty($category)){
                    $chatHistorysAssign_by->where('category_id',$category);
                }

                if(!empty($counsellor)){
                    $chatHistorysAssign_by->where('counsellor_id',$counsellor);
                }
           
        $chatHistorysAssign_by = $chatHistorysAssign_by->get();

       
        $output['chatHistorysAsync'] = $data;
        $output['chatHistorysAssign_byAsync'] = $chatHistorysAssign_by;

        return $output; 
    }

    public function liveEscalatedclose(Request $request){
         $sessionCounselorid = Auth::user()->id;
         $counsellors = User::where('id',$sessionCounselorid)->where('status','2')->whereNull('deleted_at')->first();
         $fromdate = $request->input('fromdate');  
         $todate = $request->input('todate');   
         $chattype = $request->input('chattype');  
         $chatHistorys = array();

         $CounsellorCategories = CounsellorCategories::where('counsellor_id',$sessionCounselorid)
                                                    ->whereNull('deleted_at')->get();

        if(!empty($fromdate) AND !empty($todate) AND !empty($chattype) AND $chattype == '1') {

            $chatHistorys = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where('chat_type','1')
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })
                                            ->whereDate('created_at','>=',$fromdate)
                                            ->whereDate('created_at','<=',$todate)
                                            ->where('chat_current_status','6')
                                            ->get();

            
        } else if(!empty($fromdate) AND !empty($todate) AND !empty($chattype) AND $chattype == 'Async')
        {
            $chatHistorys = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where('chat_type','2')
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })
                                            ->whereDate('created_at','>=',$fromdate)
                                            ->whereDate('created_at','<=',$todate)
                                            ->where('chat_current_status','6')
                                            ->get();
           
        } else if(!empty($fromdate) AND !empty($todate))
        {
            
        $chatHistorys = ChatSessions::with('getUser','getCategory','getUserCounselor')                                    
                                    ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })
                                    ->whereDate('created_at','>=',$fromdate)
                                    ->whereDate('created_at','<=',$todate)
                                    ->where('chat_current_status','6')
                                    ->get();
            
        }else if(!empty($chattype) AND $chattype == '1') {

            $chatHistorys = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where('chat_type','1')
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })
                                            ->where('chat_current_status','6')
                                            ->get();
            
        } else if($chattype == 'Async') 
        {
           $chatHistorys = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where('counsellor_id',$sessionCounselorid)
                                            ->where('chat_type','2')
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })
                                            ->where('chat_current_status','6')
                                            ->get();
            
        }


        if(empty($chatHistorys)){
                $chatHistorys = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })
                                            ->where('chat_current_status','6')
                                            ->get();
            }

       
        $output['chatHistorys'] = $chatHistorys;
        return $output;

    }

    


    public function pastChatCounselorReport(Request $request){
         $sessionCounselorid = Auth::user()->id;
         $counsellors = User::where('id',$sessionCounselorid)->where('status','2')->whereNull('deleted_at')->first();
         $fromdate = $request->input('fromdate');  
         $todate = $request->input('todate');   
         $chattype = $request->input('chattype');  
         $chatHistorys = array();

         $CounsellorCategories = CounsellorCategories::where('counsellor_id',$sessionCounselorid)
                                                    ->whereNull('deleted_at')->get();

        if(!empty($fromdate) AND !empty($todate) AND !empty($chattype)) {

            $chatHistorys = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where('chat_type',$chattype)
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })
                                            ->where(function($qq) use($CounsellorCategories){
                                                    if(!empty($CounsellorCategories)){
                                                        foreach ($CounsellorCategories as $key => $value) {
                                                            $qq->orWhere('category_id',$value->category_id);
                                                        }                    
                                                    }
                                            })
                                            ->whereDate('created_at','>=',$fromdate)
                                            ->whereDate('created_at','<=',$todate)
                                            ->where('chat_current_status','6')
                                            ->where(function($qqq) use($sessionCounselorid){
                                                  $qqq->orWhere('escalated_by','!=',$sessionCounselorid); 
                                                  $qqq->orwhereNull('escalated_by'); 
                                             })
                                            ->orderBy('created_at','desc')

                                            ->get();

            
        } else if(!empty($fromdate) AND !empty($todate))
        {
            $chatHistorys = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                            
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })

                                            ->where(function($qq) use($CounsellorCategories){
                                                    if(!empty($CounsellorCategories)){
                                                        foreach ($CounsellorCategories as $key => $value) {
                                                            $qq->orWhere('category_id',$value->category_id);
                                                        }                    
                                                    }
                                            })
                                            ->whereDate('created_at','>=',$fromdate)
                                            ->whereDate('created_at','<=',$todate)
                                            ->where('chat_current_status','6')
                                            ->where(function($qqq) use($sessionCounselorid){
                                                  $qqq->orWhere('escalated_by','!=',$sessionCounselorid); 
                                                  $qqq->orwhereNull('escalated_by'); 
                                             })
                                            ->orderBy('created_at','desc')
                                            ->get();
           
        } else if(!empty($chattype)) {

            $chatHistorys = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where('chat_type',$chattype)
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })
                                            ->where(function($qq) use($CounsellorCategories){
                                                    if(!empty($CounsellorCategories)){
                                                        foreach ($CounsellorCategories as $key => $value) {
                                                            $qq->orWhere('category_id',$value->category_id);
                                                        }                    
                                                    }
                                            })
                                            ->where('chat_current_status','6')
                                            ->where(function($qqq) use($sessionCounselorid){
                                                  $qqq->orWhere('escalated_by','!=',$sessionCounselorid); 
                                                  $qqq->orwhereNull('escalated_by'); 
                                             })
                                            ->orderBy('created_at','desc')
                                            ->get();
            
        }

        if(empty($chatHistorys)){
                $chatHistorys = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })
                                            ->where(function($qq) use($CounsellorCategories){
                                                    if(!empty($CounsellorCategories)){
                                                        foreach ($CounsellorCategories as $key => $value) {
                                                            $qq->orWhere('category_id',$value->category_id);
                                                        }                    
                                                    }
                                            })
                                            ->where('chat_current_status','6')
                                            
                                            ->where(function($qqq) use($sessionCounselorid){
                                                  $qqq->orWhere('escalated_by','!=',$sessionCounselorid); 
                                                  $qqq->orwhereNull('escalated_by'); 
                                             })
                                            ->orderBy('created_at','desc')
                                            ->get();
            }
       
        $output['chatHistorys'] = $chatHistorys;
        return $output;

    }

    public function pastChatCounselorReportAsync(Request $request){
         $sessionCounselorid = Auth::user()->id;
         $counsellors = User::where('id',$sessionCounselorid)->where('status','2')->whereNull('deleted_at')->first();
         $fromdate = $request->input('fromdate');  
         $todate = $request->input('todate');   
         $chattype = $request->input('chattype');  
         $chatHistorys = array();

         $CounsellorCategories = CounsellorCategories::where('counsellor_id',$sessionCounselorid)
                                                    ->whereNull('deleted_at')->get();

        if(!empty($fromdate) AND !empty($todate) AND !empty($chattype)) {

            $chatHistorys = AsyncChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where('chat_type',$chattype)
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })
                                            ->where(function($qq) use($CounsellorCategories){
                                                    if(!empty($CounsellorCategories)){
                                                        foreach ($CounsellorCategories as $key => $value) {
                                                            $qq->orWhere('category_id',$value->category_id);
                                                        }                    
                                                    }
                                            })
                                            ->whereDate('created_at','>=',$fromdate)
                                            ->whereDate('created_at','<=',$todate)
                                            ->where('chat_current_status','3')
                                            
                                            ->orderBy('created_at','desc')

                                            ->get();

            
        } else if(!empty($fromdate) AND !empty($todate))
        {
            $chatHistorys = AsyncChatSessions::with('getUser','getCategory','getUserCounselor')
                                            
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })

                                            ->where(function($qq) use($CounsellorCategories){
                                                    if(!empty($CounsellorCategories)){
                                                        foreach ($CounsellorCategories as $key => $value) {
                                                            $qq->orWhere('category_id',$value->category_id);
                                                        }                    
                                                    }
                                            })
                                            ->whereDate('created_at','>=',$fromdate)
                                            ->whereDate('created_at','<=',$todate)
                                            ->where('chat_current_status','3')
                                            
                                            ->orderBy('created_at','desc')
                                            ->get();
           
        } else if(!empty($chattype)) {

            $chatHistorys = AsyncChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where('chat_type',$chattype)
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })
                                            ->where(function($qq) use($CounsellorCategories){
                                                    if(!empty($CounsellorCategories)){
                                                        foreach ($CounsellorCategories as $key => $value) {
                                                            $qq->orWhere('category_id',$value->category_id);
                                                        }                    
                                                    }
                                            })
                                            ->where('chat_current_status','3')
                                            ->orderBy('created_at','desc')
                                            ->get();
            
        }

        if(empty($chatHistorys)){
                $chatHistorys = AsyncChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where(function($q) use($sessionCounselorid){
                                                  $q->orWhere('counsellor_id',$sessionCounselorid); 
                                                  $q->orWhere('assign_by',$sessionCounselorid); 
                                             })
                                            ->where(function($qq) use($CounsellorCategories){
                                                    if(!empty($CounsellorCategories)){
                                                        foreach ($CounsellorCategories as $key => $value) {
                                                            $qq->orWhere('category_id',$value->category_id);
                                                        }                    
                                                    }
                                            })
                                            ->where('chat_current_status','3')
                                            ->orderBy('created_at','desc')
                                            ->get();
            }
       
        $output['chatHistorysAsync'] = $chatHistorys;
        return $output;

    }

    public function liveChatHistoryList(Request $request){

        $sessionCounselorid = Auth::user()->id;

        $fromdate = $request->input('fromdate');  
        $todate = $request->input('todate');   
        $chattype = $request->input('chattype');
        $counsellor = $request->input('counsellor');  
        $category = $request->input('category');  

       

            $CounsellorCategories = CounsellorCategories::where('counsellor_id',$sessionCounselorid)
                                                    ->whereNull('deleted_at')
                                                    ->get();

            $chatHistorys = ChatSessions::with('getUser','getCategory','getUserCounselor');

            $chatHistorys->where('chat_current_status','6');
            $chatHistorys->whereNull('assign_by');

                if(!empty($chattype)){
                    $chatHistorys->where('chat_type',$chattype);
                }               

                if(!empty($fromdate) AND !empty($todate)){
                    $chatHistorys->whereDate('created_at','>=',$fromdate);
                    $chatHistorys->whereDate('created_at','<=',$todate);
                }

                if(!empty($category)){
                    $chatHistorys->where('category_id',$category);
                }

                if(!empty($counsellor)){
                    $chatHistorys->where('counsellor_id',$counsellor);
                }
           
        $data = $chatHistorys->get();


            $chatHistorysAssign_by = ChatSessions::with('getUser','getCategory','getCounselorAssignBy');

            $chatHistorysAssign_by->where('chat_current_status','6');
            $chatHistorysAssign_by->whereNotNull('assign_by');

                if(!empty($chattype)){
                    $chatHistorysAssign_by->where('chat_type',$chattype);
                }               

                if(!empty($fromdate) AND !empty($todate)){
                    $chatHistorysAssign_by->whereDate('created_at','>=',$fromdate);
                    $chatHistorysAssign_by->whereDate('created_at','<=',$todate);
                }

                if(!empty($category)){
                    $chatHistorysAssign_by->where('category_id',$category);
                }

                if(!empty($counsellor)){
                    $chatHistorysAssign_by->where('counsellor_id',$counsellor);
                }
           
        $chatHistorysAssign_by = $chatHistorysAssign_by->get();

       
        $output['chatHistorys'] = $data;
        $output['chatHistorysAssign_by'] = $chatHistorysAssign_by;

        return $output;
    }

    public function myChatAdminReport(Request $request)
    {
        $sessionCounselorid = Auth::user()->id;
        $fromdate = $request->input('fromdate');  
        $todate = $request->input('todate');
        $counsellor = $request->input('counsellor');  
        $category = $request->input('category');  
        $chattype = $request->input('chattype');  
        $adminpastchats = array();

        $CounsellorCategories = CounsellorCategories::where('counsellor_id',$sessionCounselorid)
                                                    ->whereNull('deleted_at')
                                                    ->get();

            $chatHistorys = AsyncChatSessions::with('getUser','getCategory','getUserCounselor');

            $chatHistorys->where('chat_current_status','3');
            $chatHistorys->whereNull('assign_by');
            $chatHistorys->where('counsellor_id',$sessionCounselorid);

                if(!empty($chattype)){
                    $chatHistorys->where('chat_type',$chattype);
                }               

                if(!empty($fromdate) AND !empty($todate)){
                    $chatHistorys->whereDate('created_at','>=',$fromdate);
                    $chatHistorys->whereDate('created_at','<=',$todate);
                }

                if(!empty($category)){
                    $chatHistorys->where('category_id',$category);
                }

                if(!empty($counsellor)){
                    $chatHistorys->where('counsellor_id',$counsellor);
                }
           
            $data = $chatHistorys->get();


            $chatHistorysAssign_by = AsyncChatSessions::with('getUser','getCategory','getCounselorAssignBy');

            $chatHistorysAssign_by->where('chat_current_status','3');
            $chatHistorysAssign_by->where('assign_by',$sessionCounselorid);
            

                if(!empty($chattype)){
                    $chatHistorysAssign_by->where('chat_type',$chattype);
                }               

                if(!empty($fromdate) AND !empty($todate)){
                    $chatHistorysAssign_by->whereDate('created_at','>=',$fromdate);
                    $chatHistorysAssign_by->whereDate('created_at','<=',$todate);
                }

                if(!empty($category)){
                    $chatHistorysAssign_by->where('category_id',$category);
                }

                if(!empty($counsellor)){
                    $chatHistorysAssign_by->where('counsellor_id',$counsellor);
                }
           
        $chatHistorysAssign_by = $chatHistorysAssign_by->get();

       
        $output['chatHistorysAsync'] = $data;
        $output['chatHistorysAssign_byAsync'] = $chatHistorysAssign_by;

        return $output; 

    }   

     

    public function myChatAdminReportLive(){

        $sessionCounselorid = Auth::user()->id;
        $categorys = Category::get();
        $liveEscalated = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                        ->where('chat_current_status','5') 
                                        ->where('assign_by',$sessionCounselorid)
                                        ->get();
        
        $output['liveEscalated'] = $liveEscalated;
        $output['categorys'] = $categorys;
        
        return $output;

    }

    public function liveWaitingEscalatedChats(){

        $sessionCounselorid = Auth::user()->id;
        $categorys = Category::get();
        $counsellor = User::with(['roles'])->where('status','2')
                                            ->where('counselor_availability','1')
                                            ->where('chat_availability','0')
                                            ->get();

        $liveWaitingEscalated = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                        ->where('chat_current_status','5') 
                                        ->where('chat_type','1')
                                        ->whereNull('assign_by')
                                        ->get();

        $counsellor = User::counsellorDropdown($counsellor);
        $output['liveWaitingEscalated'] = $liveWaitingEscalated;
        $output['categorys'] = $categorys;
        $output['counsellor'] =  $counsellor;       
        
        return $output;

    }


    public function myChatAdminReportAsync(Request $request){

        $sessionCounselorid = Auth::user()->id;
        $categorys = Category::get();
        $category_id = $request->input('category_id'); 

        $counsellor = User::with(['roles'])->where('status','2')
                                            ->where('counselor_availability','1')
                                            ->where('chat_availability','0')
                                           ->get();

        if(!empty($category_id)) {

            if($category_id == 'all') {
                $liveEscalated = AsyncChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where('chat_current_status','4') 
                                            ->where('chat_type','2')
                                            ->whereNull('assign_by')
                                            ->get();

            } else {
                $liveEscalated = AsyncChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where('chat_current_status','4') 
                                            ->where('chat_type','2')
                                            ->where('category_id',$category_id)
                                            ->whereNull('assign_by')
                                            ->get();
                
            }
        }

        $counsellor = User::counsellorDropdown($counsellor);
        $output['AsyncEscalated'] = $liveEscalated;
        $output['categorys'] = $categorys;
        $output['counsellor'] =  $counsellor;       
        
        return $output;

    }


    public function myChatAdminReportUserWaiting(Request $request)
    {

        $sessionCounselorid = Auth::user()->id;
        $category_id = $request->input('category_id'); 

            if($category_id == 'all'){
                if($sessionCounselorid == 1){
                    $waitingUsers = WaitingAssignments::with('getUser','getCategory')->get();

                    $counselorassignment = CounselorAssignment::with('getUser','getCategory')->get();

                    $counsellors = User::where('status','2')->whereNull('deleted_at')->get();

                }
            } else {
                if($sessionCounselorid == 1){
                    $waitingUsers = WaitingAssignments::with('getUser','getCategory')
                                                        ->where('category_id',$category_id)
                                                        ->get();

                    $counselorassignment = CounselorAssignment::with('getUser','getCategory')
                                                                ->where('category_id',$category_id)
                                                                ->get();

                    $counsellors = User::where('status','2')->whereNull('deleted_at')->get();

                }          
            }
         $categorys = Category::get();

        if(!empty($waitingUsers)){
            $output['countRow'] =  count($waitingUsers);
        } else {
            $output['countRow'] =  '00';
        }

        $output['html_content'] =  view('admin.counselorassignments.userWaitingFilter', compact('waitingUsers','categorys','counselorassignment','counsellors','sessionCounselorid'))->render();

        return $output; 
       
    }


     public function liveCurentChatListAdmin(Request $request)
    {
        $sessionCounselorid = Auth::user()->id;
        $category_id = $request->input('category_id');  

        $counselors = User::with(['roles'])->where('status','2')->get();

        $categorys = Category::get();
        if($category_id == 'all') {
           $counselorLiveChats = CounselorAssignment::with('getCategory','getUser','getUserCounselor')
                                                        ->where('chat_type','Live')
                                                        ->get();

            

        } else {

            $counselorLiveChats = CounselorAssignment::with('getCategory','getUser','getUserCounselor')
                                                        ->where('chat_type','Live')
                                                         ->where('category_id',$category_id)
                                                        ->get();
          
        }
        if(!empty($counselorLiveChats))  {
            $output['countRow'] =  count($counselorLiveChats);
        } else {
            $output['countRow'] =  '00';
        }

        
        $output['html_content'] =  view('admin.counselorcurrentcases.filterLiveChatList', compact('categorys','counselors','counselorLiveChats'))->render();   

        return $output;  
    } 

    public function liveCurentChatListAdminCheckDb(Request $request)
    {
        $sessionCounselorid = Auth::user()->id;
        $category_id = $request->input('category_id');  
        $count = $request->input('count');

        $counselors = User::with(['roles'])->where('status','2')->get();

        $categorys = Category::get();
        if($category_id == 'all') {
           $counselorLiveChats = CounselorAssignment::with('getCategory','getUser','getUserCounselor')
                                                        ->where('chat_type','Live')
                                                        ->get();

            if(!empty($counselorLiveChats)){
                    $newCount = count($counselorLiveChats);
                    if($newCount > $count || $newCount < $count ){
                        $output = ['success' => true];
                    } else {
                        $output = ['success' => false];
                    }
            }           

        } else {
            $output = ['success' => false];
        } 
        return $output; 
       
    } 

    public function myChatAdminReportUserWaitingCheckBd(Request $request) {

        $sessionCounselorid = Auth::user()->id;
        $category_id = $request->input('category_id'); 
        $count = $request->input('count'); 

        if($category_id == 'all'){
            if($sessionCounselorid == 1){
                $waitingUsers = WaitingAssignments::with('getUser','getCategory')->get();

                if(!empty($waitingUsers)){
                    $newCount = count($waitingUsers);
                    if($newCount > $count || $newCount < $count ){
                        $output = ['success' => true];
                    } else {
                        $output = ['success' => false];
                    }
                }
            }
        } else {
            $output = ['success' => false];
        } 
        return $output;      
    }

    public function counselorLiveChatList(){

        $sessionCounselorid = Auth::user()->id;
        $counselors = User::with(['roles'])->where('status','2')->get();
        $categorys = Category::get();

        $counselorCheckit = User::where('id',$sessionCounselorid)->where('status','2')->first();

        $CounsellorCategories = CounsellorCategories::where('counsellor_id',$sessionCounselorid)
                                                    ->whereNull('deleted_at')
                                                    ->get();

        $liveAssignList = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where('counsellor_id',$sessionCounselorid)
                                            ->where('chat_type','1')
                                            ->where(function($q) use($CounsellorCategories){
                                                    if(!empty($CounsellorCategories)){
                                                        foreach ($CounsellorCategories as $key => $value) {
                                                            $q->orWhere('category_id',$value->category_id);
                                                        }                    
                                                    }
                                                })
                                            ->where(function($qq){
                                                $qq->orWhere('chat_current_status','2');
                                                $qq->orWhere('chat_current_status','3');
                                                $qq->orWhere('chat_current_status','4');
                                            })->get();

        
        if(!empty($liveAssignList) AND count($liveAssignList) !=0){
           
            $output['liveAssignList'] = $liveAssignList;
            $output['categorys'] = $categorys;
            $output['count'] = count($liveAssignList);


        } else {
            
            $liveAssignList = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                            ->where('assign_by',$sessionCounselorid)
                                            ->where('chat_type','1')
                                            ->where(function($qq){
                                                $qq->orWhere('chat_current_status','2');
                                                $qq->orWhere('chat_current_status','3');
                                                $qq->orWhere('chat_current_status','4');
                                                $qq->orWhere('chat_current_status','5');
                                            })->get();

            $output['liveAssignList'] = $liveAssignList;
            $output['categorys'] = $categorys;
            if(!empty($liveAssignList)){
                $output['count'] = count($liveAssignList);
            } else {
                $output['count'] = '00';
            }
            
        }

        return $output;

    }

    public function counselorAsyncChatList(){

        $sessionCounselorid = Auth::user()->id;
        $counselors = User::with(['roles'])->where('status','2')->get();
        $categorys = Category::get();

        $counselorCheckit = User::where('id',$sessionCounselorid)->where('status','2')->first();

        $CounsellorCategories = CounsellorCategories::where('counsellor_id',$sessionCounselorid)
                                                    ->whereNull('deleted_at')->get();       


        $counselorCurrentChats = AsyncChatSessions::with('getCategory','getUser')
                                                ->where('chat_type','2')
                                                ->where(function($qq){
                                                    $qq->orWhere('chat_current_status','1');
                                                    $qq->orWhere('chat_current_status','2');              
                                                })
                                                ->where(function($qq) use($CounsellorCategories){
                                                    if(!empty($CounsellorCategories)){
                                                        foreach ($CounsellorCategories as $key => $value) {
                                                            $qq->orWhere('category_id',$value->category_id);
                                                        }                    
                                                    }
                                                })
                                                ->get(); 

             $output['counselorCurrentChats'] = $counselorCurrentChats;
             $output['sessionCounselorid'] = $sessionCounselorid;
             $output['counselorCheckit'] = $counselorCheckit;
             $output['categorys'] = $categorys;
             $output['counselors'] =  $counselors;

        return $output;

    }


    public function counselorLiveChatPorgressList(){

        $sessionCounselorid = Auth::user()->id;
        $counselors = User::with(['roles'])->where('status','2')->get();
        $categorys = Category::get();


        // $counselorLiveChats = CounselorAssignment::with('getCategory','getUser','getUserCounselor')->where('chat_type','Live')->orderBy('created_at','desc')->get();

        $counselorLiveChats = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                        ->where('chat_current_status','!=','7')
                                        ->where('chat_current_status','!=','8')
                                        ->where('chat_current_status','!=','6') 
                                        ->where('chat_current_status','!=','5') 
                                        ->where('chat_current_status','!=','1') 
                                        ->where('chat_type','1')
                                        ->get();





            $output['counselorLiveChats'] = $counselorLiveChats;
            $output['categorys'] = $categorys;
            $output['counselors'] =  $counselors;

        if(!empty($counselorLiveChats)){
            $output['countRow'] =  count($counselorLiveChats);
        } else {
            $output['countRow'] =  '00';
        }
        
        return $output;

    }

    public function usersWaitingList(){

        $sessionCounselorid = Auth::user()->id;
        $categorys = Category::get();
        $counsellor = User::with(['roles'])
                            ->where('status','2')
                            ->where('counselor_availability','1')
                            ->where('chat_availability','0')
                            ->get();

        $waitingUsers = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                    ->where('chat_current_status','1') 
                                    ->where(function($qq){
                                        $qq->orWhere('chat_type','2');
                                        $qq->orWhere('chat_type','1');
                                    })
                                    ->get();

        $counsellor = User::counsellorDropdown($counsellor);
        $output['waitingUsers'] = $waitingUsers;
        $output['categorys'] = $categorys;
        $output['counsellor'] =  $counsellor; 

        if(!empty($waitingUsers)){
            $output['countRow'] =  count($waitingUsers);
        } else {
            $output['countRow'] =  '0';
        }      
        
        return $output;

    }

    public function usersWaitingListCountCheck(){


        $waitingUsers = ChatSessions::with('getUser','getCategory','getUserCounselor')
                                    ->where('chat_current_status','1') 
                                    ->where(function($qq){
                                        $qq->orWhere('chat_type','2');
                                        $qq->orWhere('chat_type','1');
                                    })
                                    ->get();

        $userswaitingcount = request()->get('userswaiting');

            if(!empty($waitingUsers) AND count($waitingUsers) > $userswaitingcount || count($waitingUsers) < $userswaitingcount ){
                $output = ['success' => true,'msg' =>'user Waiting' ];
                          
            } else {
                $output = ['success' => false,'msg' => ''];
            }  

          return $output;

    }
    
}
