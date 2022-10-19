<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feq;
use App\Models\User;
use Illuminate\Support\Facades\Auth;



class FeqController extends Controller
{
    // public function store(Request $r){
    //     $user_id = Auth::user()->id;
    //     $datas = new Feq();
    //     $datas->question=$r->question;
    //     $datas->user_id=$user_id;
    //     $datas->save();
    //     $data = Feq::join('users','feq.user_id','=','users.id')->where('user_id',$user_id)->get();
    //     // dd($data);
    //     Mail::to('ankushkushwah2000@gmail.com')->send(new \App\Mail\Mymail($data));
    //     return redirect()->back();
    // }

    public function add_feq(Request $request){
        $data = new Feq();
        $data->question = $request->question;
        $data->ans = $request->ans;
        $data->save();
        return redirect()->back();
    }

    public function index(){
        $data = Feq::all();
        return view('admin/Feq/feq',compact('data'));
    }

    public function update(Request $request,$id){
        $data = Feq::find($id);
        $data->question = $request->question;
        $data->ans = $request->ans;
        $data->save();
        return redirect()->back();
    }

    public function delete($id){
        $data = Feq::find($id);
        $data->delete();
        return redirect()->back();
    }

}

?>