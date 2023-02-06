<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Http\Controllers\LogsController;
use App\Jobs\ProcessSendSms;
use App\Helpers\Helper;

class SmsController extends Controller
{
    public function __construct(){
       
    }
    
    public function index()
    {
        return view('pages.main.sms');
    }

    public function SendSMS(Request $request){

        $this->validate($request, [
              'to' => 'required',
              'message'=> 'required',
        ]);

        $from = config('app.name');
        $to = $request->input('to');
        $text_message = $request->input('message');

        try{

            $arr = array(
                   'from' => $from,
                   'to' => $to,
                   'message' => $text_message,
                   'created_at' => now(),
            );


        //put a method to log this request before API call
         $this->EnqueueSms($arr); // $this->SendTextMessage($from, $to, $text_message);
	//	Log::info('app.requests', ['request' => $request->all(), 'response' => $response]);

   // if($res){
        $action = "sent a text message to ".$to."";
        //Log this transactional request
        LogsController::logger($request, $action, now());

        return back()
        ->with("success", "You have successfully ".$action."");
    // }
    // else{
    //     return back()->with("fail", "Sorry, message has not been sent!");

    // }
}catch(\Exception $ex){
    echo('Problems thhh');
    return back()->with("fail", "Sorry, message has not been sent!");
}

    }

    protected function EnqueueSms($data)
    {
        dispatch(new ProcessSendSms($data))->onQueue('sms');
      
    }










}
