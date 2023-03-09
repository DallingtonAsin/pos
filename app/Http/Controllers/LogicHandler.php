<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

class LogicHandler extends Controller
{


	public function is_connectedToInternet()
	{
		$connected = @fsockopen('www.google.com', 80);
		if($connected){
			$is_conn = 1;
			fclose($connected);
		}
		else{
			$is_conn = 0;
		}

		return $is_conn;
	}

	protected function sendMail($mailContentPage, $receiverEmail, 
		                        $dataX, $dataY){

		$mailState = 0; 
		
		if($this->is_connectedToInternet() == 1)
		 {
			$senderEmail = config('app.companyEmail');
			Mail::send($mailContentPage, $dataX, 
				   function($message) use ($dataY)
			{   
				$message->from($senderEmail, 'Dallington');
				$message->to($receiverEmail)->subject($subject);
			}); 

			(Mail::failures())
			  ? $mailState = 1
			  : $mailState = -1;

              return $mailState;
		}
		
    }
    
}
