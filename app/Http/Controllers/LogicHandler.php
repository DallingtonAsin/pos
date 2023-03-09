<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

class LogicHandler extends Controller
{

	public function is_connectedToInternet()
	{
		$connected = @fsockopen('www.google.com', 80);
		if ($connected) {
			$is_conn = 1;
			fclose($connected);
		} else {
			$is_conn = 0;
		}

		return $is_conn;
	}

	protected function sendMail(
		$mailContentPage,
		$receiverEmail,
		$dataX,
		$data
	) {

		$mailState = 0;

		if ($this->is_connectedToInternet() == 1) {
			$senderEmail = config('app.companyEmail');
			Mail::send(
				$mailContentPage,
				$dataX,
				function ($message) use ($data) {
					$message->from($data['senderEmail'], 'Dallington');
					$message->to($data['receiverEmail'])->subject($data['subject']);
				}
			);

			(Mail::failures())
				? $mailState = 1
				: $mailState = -1;

			return $mailState;
		}
	}
}
