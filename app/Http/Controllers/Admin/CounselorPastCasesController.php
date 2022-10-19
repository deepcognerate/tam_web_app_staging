<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MassDestroyPastCasesRequest;
use App\Http\Requests\StorePastCasesRequest;
use App\Http\Requests\UpdatePastCasesRequest;
use App\Models\CounselorPastCases;
use App\Models\User;
use App\Models\Category;
use App\Models\AsyncChat;
use App\Models\Feedback;
use App\Models\ChatHistory;
use Gate;
use Auth;
use DB;
use Symfony\Component\HttpFoundation\Response;

// New code 
use App\Models\ChatSessions;
use App\Models\CounsellorCategories;
use App\Models\ChatAudit;
use App\Models\ChatMessages;
use App\Models\AsyncChatSessions;
use App\Models\AsyncChatAudit;
use App\Models\AsyncChatMessages;

class CounselorPastCasesController extends Controller
{
    
    public function index()
    {
        abort_if(Gate::denies('counselor_past_cases_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $sessionCounselorid = Auth::user()->id;
        $counsellors = User::where('status','2')->whereNull('deleted_at')->get();  
        $categorys = Category::get();
        if($sessionCounselorid == 1) {
            return view('admin.counselorpastcases.index', compact('counsellors','categorys'));
        } else { 
            return view('admin.counselorpastcases.index_two', compact('counsellors','categorys'));
        }
    }

    public function create()
    {
        abort_if(Gate::denies('counselor_past_cases_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles = Role::all()->pluck('title', 'id');
        $categorys = Category::get();
        return view('admin.counselorpastcases.create');
    }

    
    public function show($pastChatId)
    {
        abort_if(Gate::denies('counselor_past_cases_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $ChatSessions = ChatSessions::where('session_id',$pastChatId)->first();

        $chatHistorys = ChatMessages::where('session_id',$pastChatId)->get();
       
        if(empty($ChatSessions)){
            // Session::flash('message', 'Chat Historys Not Found');
            return redirect()->route('admin.counselorpastcases.index');
        } else {
            $users = User::where('id',$ChatSessions->user_id)->where('status','0')->first();
        }

        return view('admin.counselorpastcases.show',compact('chatHistorys','users'));
    }


    public function showAsync($pastChatId)
    {
        abort_if(Gate::denies('counselor_past_cases_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $ChatSessions = AsyncChatSessions::where('session_id',$pastChatId)->first();

        $chatHistorys = AsyncChatMessages::where('session_id',$pastChatId)->get();
       
        if(empty($ChatSessions)){
            Session::flash('message', 'Chat Historys Not Found');
            return redirect()->route('admin.counselorpastcases.index');
        } else {
            $users = User::where('id',$ChatSessions->user_id)->where('status','0')->first();
        }

        return view('admin.counselorpastcases.show',compact('chatHistorys','users'));
    }


    public function destroy(User $counselor)
    {
        abort_if(Gate::denies('counselor_past_cases_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $counselor->delete();

        return back();
    }

    public function massDestroy(MassDestroyCounselorRequest $request)
    {
        CounselorPastCases::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    
    
}
