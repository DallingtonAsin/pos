<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Auth\Middleware\Authenticate;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LogsController;

class CustomLoginController extends Controller
{

	private $tbl;
	protected $dateTime;
	protected $session;

	public function __construct()
	{

		$this->tbl = 'users';
		$this->dateTime = now();
		$this->middleware('guest')->except('logout');
	}



	public function authenticate(Request $request)
	{

		($request->has('remember'))
		? $remembered = true
		: $remembered = false;

		$this->validate($request,[
			'pos_login' => 'required',
			'pos_password' => 'required',
		]);

		$login = $request->input('pos_login');
		$password = $request->input('pos_password');

		filter_var($login, FILTER_VALIDATE_EMAIL)
		? $fieldType = 'email' 
		: $fieldType = 'username';

		$user_id = $this->getUserId($login, $password);

		if(Auth::attempt([$fieldType => $login,
			'password' =>  $password
		], $remembered)){

			$userId = $this->getUserId($login);
			$status = $this->findAccountStatus($userId);
			// dd($status);
				if($status == 0){

					$this->flushSessionData($request);
				
					return back()
					->withInput()
					->with('loginErr', 'Your account is inactivated, see admin');
				}
				if($status == 1){
					$action = "logged into the system";
					LogsController::logger($request, $action, $this->dateTime);
					return redirect('/home');
           
				}
		}
		else
		{
                $this->flushSessionData($request);
				return back()
				->withInput()
				->with('loginErr', 'Invalid login credentials');
		
		}


	}


	public function logout(Request $request){

		$action = "logged out of the system";
		if(!empty($request->name)){
		 LogsController::logger($request, $action, $this->dateTime);
		}
		Auth::logout();
		$this->flushSessionData($request);
		
		return redirect('/');

	}

	//Flush login data and all session variables on login attempts
	protected function flushSessionData(Request $request)
	{
		
		Session::flush();
		

	}

	public function getUserId($login)
	{
		filter_var($login, FILTER_VALIDATE_EMAIL)
		? $fieldType = 'email' 
		: $fieldType = 'username';

		$userId = DB::table($this->tbl)
		->where($fieldType, $login)
		->value('email');

		return $userId;
	}

	public function findAccountStatus($id){

		$accountStatus = DB::table($this->tbl)
		->where('email', $id)->value('isActive');
		return $accountStatus;

	}


	public function findUsername(){

		$login = request()->input('pos_login');

		$fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

		request()->merge([$fieldType => $login]);
		if($fieldType){
			return $fieldType;  
		}

	}


}
