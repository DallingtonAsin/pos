<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogAfterRequest;
use App\User;
class SessionTimeout
{

  protected $session;
      protected $timeout = 3600; // 30 minutes

      public function __construct(Store $session)
      {
        $this->session = $session;
      }
      /**
       * Handle an incoming request.
       *
       * @param  \Illuminate\Http\Request  $request
       * @param  \Closure  $next
       * @return mixed
       */
      public function handle($request, Closure $next)
      {
        $isLoggedIn = $request->path() != '/logout';
        $lastActivityTime = $this->session->get('lastActivityTime');
        (($this->timeout/60) > 1)
           ? $units = "minutes"
           : $units = "minute";

        if(! session('lastActivityTime'))
        {
          $this->session->put('lastActivityTime', time());
        }

        else if(time() - $lastActivityTime  > $this->timeout)
        {
          $this->session->forget('lastActivityTime');
          $cookie = cookie('intend', $isLoggedIn ? url()->current() : 'dashboard');
          $email = $request->user()->email;
          User::where("id", $request->user()->id)
               ->update(["OTPcode" => null, "isVerified" => false]);

        $msg = "session timed out after ".$this->timeout/60 ." ".$units." inactive";
        $dataArr = array("code" => '404',
        "message" => $msg,
        "method" => "Middleware@SessionTimeout@handle"
        );
        LogAfterRequest::LogRequest($request, $dataArr); 
        LogsController::logger($request, $msg, now());


          Auth::logout();
          //Session::flush();
          if ($request->session()->exists('pos_login')) {
            $request->session()->forget('pos_login');
          }
          if ($request->session()->exists('pos_password')) {
            $request->session()->forget('pos_password');
          }  
         // $this->session->flush();

          
          $message = "No activity within ".$this->timeout/60 ." ".$units."";
          return redirect('/')->with('sessionExpiredMessage',$message
          ,"warning", "try re-login");
            //   ->withInput(compact('email'))->withCookie($cookie);
            // "You had no activity in ".$this->timeout/60 ." ".$units.", please re-login."
        }


        $isLoggedIn 
        ? $this->session->put('lastActivityTime', time())
        : $this->session->forget('lastActivityTime');

        return $next($request);
      }


    }
