<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use App\Models\FcmToken;
use Auth;
use Session;
class SessionCheck
{
    protected $session;
    protected $timeout = 1770; //Session Expire time in seconds

    public function __construct(Store $session){
        $this->session = $session;
    }
    public function handle($request, Closure $next){
        $isLoggedIn = $request->path() != 'admin/logout';

        if(!session('lastActivityTime'))
            $this->session->put('lastActivityTime', time());
        elseif(time() - $this->session->get('lastActivityTime') > $this->timeout){

            $this->session->forget('lastActivityTime');
            $cookie = cookie('intend', $isLoggedIn ? url()->current() : 'https://tamdemo.destress.in/');

                $sessionCounselorid = isset(Auth::user()->id)?Auth::user()->id:null;

                $getFcmTokens = FcmToken::where('user_id', $sessionCounselorid)->first();
                if(!empty($getFcmTokens))
                {
                    $removeFcmToken = FcmToken::where('user_id', $getFcmTokens->user_id)->delete();
                }

                if($sessionCounselorid != 1)
                {
                    $counselorActive = User::where('id', $sessionCounselorid)
                                            ->where('status','2')
                                            ->update(['counselor_availability'=>0]);            
                    
                }

            Auth::logout();
            return redirect('/login');
        }
        $isLoggedIn ? $this->session->put('lastActivityTime', time()) : $this->session->forget('lastActivityTime');
        return $next($request);
    }
}