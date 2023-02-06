<?php

namespace App\Http\Controllers;

use App\Http\Controllers\LogsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\LogAfterRequest;
use App\Http\Controllers\SmsController;
use App\Notifications\UserRegistration;
use App\Jobs\MailRegistration;
use App\DataTables\ManagersDataTable;
use App\DataTables\CashiersDataTable;
use App\DataTables\UsersDataTable;
use App\DataTables\ActiveUserAccountsDataTable;
use App\DataTables\InactiveUserAccountsDataTable;
use App\User;
use App\Models\Role;
use App\Helpers\Helper;
use Constant;


class UserController extends Controller
{

  public $controller;
  public function __construct(){
    $this->controller = 'UserController';
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $response = Gate::inspect('isSuperAdmin');

        if($response->allowed()){

        try{
           $users = User::where('id',"!=", $request->user()->id)->get();
           $number_of_users = User::count();
           return view('pages.users.index')
           ->with(compact('users', 'number_of_users'));

        }catch(\Exception $ex)
        {
              $data = array(
                    'username' => $request->username,
                    'error_code' => $ex->getCode(),
                    'error_message' => $ex->getMessage(),
                    'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
                    'method' => 'index' 
              );
              Helper::logError($data);
        }
       }else{
          // return view("errors.429");
       }
    }


    public function GetUsers(UsersDataTable $dataTable){
        return $dataTable->render('pages.users.index');
    }

    public function GetManagers(ManagersDataTable $dataTable){
        return $dataTable->render('pages.users.managers');
    }
    
    public function fetchManagers()
    {

        try{

        $role = 'Administrator';
        $number_of_managers = $this->GetRoleStats($role);
        $roles = Helper::getRoles();

        return view('pages.users.managers')->with([
        'number_of_managers' => $number_of_managers,
         'registeredRoles' => $roles,
       ]);

    }catch(\Exception $ex){
            return abort("405", "We have caught exception ".$ex->getMessage()." for you");
   }
    }


    public function GetCashiers(CashiersDataTable $dataTable){
        return $dataTable->render('pages.users.cashiers');
    }
    
    public function fetchCashiers(){

        try{

        $role = 'cashier';
        $number_of_cashiers = $this->GetRoleStats($role);

        return view('pages.users.cashiers')->with([
        'number_of_cashiers' => $number_of_cashiers

       ]);

    }catch(\Exception $ex){
            return abort("405", "We have caught exception ".$ex->getMessage()." for you");
   }
    }


     public function ActiveUsersAjax(ActiveUserAccountsDataTable $dataTable){
        return $dataTable->render('pages.users.active-users');
    }


    public function ActiveUsersIndex(Request $request)
    {
      try{
        $response = Gate::inspect('isSuperAdmin');
        if($response->allowed()){
           $users = User::where('isActive', true)
                         ->where('id', "!=", $request->user()->id)->get();
           $number_of_users = User::where('isActive', true)->count();
           return view('pages.users.active-users')
           ->with(compact('users', 'number_of_users'));
           
       }
      }catch(\Exception $ex){
        dd($ex->getMessage());
      }
    }


   public function LockedUsersAjax(InactiveUserAccountsDataTable $dataTable){
        return $dataTable->render('pages.users.inactive-users');
    }

    public function LockedUsersIndex(Request $request)
    {

      try{
        $response = Gate::inspect('isSuperAdmin');

        if($response->allowed()){
           $users = User::where('isActive', false)->get();
           $number_of_users = User::where('isActive', false)->count();
           return view('pages.users.inactive-users')
           ->with(compact('users', 'number_of_users'));
       }
      }catch(\Exception $ex){
        dd($ex->getMessage());
      }
    }


    public function LockUnlockUserAccount(Request $request){

      try{
        
        $admin = $request->user()->name;
        $method = "LockUnlockUserAccount";
        if($request->has('id') && $request->has('status')){

        $id = $request->input('id');
        $status = $request->input('status');
        // dd($id, $status);

        $user = User::find($id);
        $name = $user->name;
        switch(true){

          case ($status == true):
          $deactivated = User::where('id', $id)
          ->update(['isActive' => false, 'inactivated_by' => $admin]);
          if($deactivated){

            $action = "deactivated user ".$name."'s account";
            LogsController::logger($request, $action, now());
            $dataArr = array("code" => '200',
            "message" => $action,
            "method" => $method);
            LogAfterRequest::LogRequest($request, $dataArr);
            return response()
            ->json(['success' => $this->ActionMessage($action)]);

          } else{

            $messageErr = 'Unable to deactivate user account!';
            $dataArr = array("code" => '101',
            "message" => $messageErr,
            "method" => $method
                     );
            LogAfterRequest::LogRequest($request, $dataArr);
            return response()
            ->json(['error' => $messageErr]);
          }
          break;

          case ($status == false):
          $activated = User::where('id', $id)
          ->update(['isActive' => true, 'loginAttempts' => 0,
                    'otpAttempts' => 0,  'inactivated_by' => $admin]);
          if($activated){

            $action = "activated user ".$name."'s account";
            LogsController::logger($request, $action, now());
            $dataArr = array("code" => '200',
                             "message" => $action,
                             "method" => $method);
            LogAfterRequest::LogRequest($request, $dataArr);
            return response()
            ->json(['success' => $this->ActionMessage($action)]);

          } else
          {
            $messageErr = 'Unable to change user account status!';
            $dataArr = array("code" => '101',
            "message" => $messageErr,
            "method" => $method
                     );
            LogAfterRequest::LogRequest($request, $dataArr);
            return response()
            ->json(['error' => $messageErr]);
          }

          break;

        }

      }else{

        $messageErr= "Unable to get id and status in request";
         return response()
            ->json(['error' => $messageErr]);
      }
    
  }catch(\Exception $ex){
      dd("Exception while locking/unlocking user account".$ex->getMessage());
  }

    }


    public function LockUnlockAccount(Request $request, $id, $status, $name){


        $response = Gate::inspect('isSuperAdmin');

        if($response->allowed()){
        $admin = $request->user()->name;
        $method = "LockUnlockAccount";
        switch(true){

          case ($status == true):
          $deactivated = User::where('id', $id)
          ->update(['isActive' => false, 'inactivated_by' => $admin]);
          if($deactivated){

            $action = "deactivated user ".$name."'s account";
            LogsController::logger($request, $action, now());
            $dataArr = array("code" => '200',
            "message" => $action,
            "method" => $method);
            LogAfterRequest::LogRequest($request, $dataArr);
            return back()
            ->with('success',$this->ActionMessage($action));

          } else{

            $messageErr = 'Account deactivation failed!';
            $dataArr = array("code" => '101',
            "message" => $messageErr,
            "method" => $method
                     );
            LogAfterRequest::LogRequest($request, $dataArr);
            return back()
                    ->with('fail', $messageErr);
          }
          break;

          case ($status == false):
          $activated = User::where('id', $id)
          ->update(['isActive' => true, 'loginAttempts' => 0,
                    'otpAttempts' => 0,  'inactivated_by' => $admin]);
          if($activated){

            $action = "activated user ".$name."'s account";
            LogsController::logger($request, $action, now());
            $dataArr = array("code" => '200',
                             "message" => $action,
                             "method" => $method);
            LogAfterRequest::LogRequest($request, $dataArr);
            return back()
                    ->with('success',$this->ActionMessage($action));

          } else
          {
            $messageErr = 'Account activation failed!';
            $dataArr = array("code" => '101',
            "message" => $messageErr,
            "method" => $method
                     );
            LogAfterRequest::LogRequest($request, $dataArr);
            return back()
                    ->with('fail', $messageErr);
          }

          break;

        }
    }

      }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       return view('pages.users.register');
    }

    protected function getRole($id)
    {

      $role = DB::table('roles')->where('role_id', $id)
                     ->value('role');
      return $role;
    }


    protected function Enqueue($data)
    {
        MailRegistration::dispatch($data);
    }


    private function GetRoleStats($role = null){
           try{
                  if(!empty($role)){
                  $id = Helper::getRoleId($role);
                    $number_of_users = User::where('user_role', '=', $id)->count();
                  }else {
                    $number_of_users = User::count();
                  }
                  return $number_of_users;
           }catch(\Exception $ex){
                 dd($ex->getMessage());
           }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

          $method = "UserController@store";
          $user_fname = trim($request->input('firstName'));
          $user_lname = trim($request->input('lastName'));
          $user_address = trim($request->input('address'));
          $user_email = trim($request->input('email'));
          $user_telno = trim($request->input('tel_no'));
          $user_alt_telno = trim($request->input('alt_telno'));
          $user_nin = trim($request->input('NationalIDNo'));
          $user_gender = trim($request->input('gender'));
          $user_role = $request->input('role');
          $user_position= Helper::getRole($user_role);
          $registra = $request->user()->name;
          $name = $user_fname." ".$user_lname;
          $defaultPwd = '12345678';

          if($request->has('id') && $request->filled('id')) {
             $user = User::find($request->input('id'));
             $username = $user->username;
             $password = $user->password; 

          }else {
              $user = new User();
              $name = $user_fname." ".$user_lname;
              $username = strtolower(Str::random(6).".".$user_fname);
              $password = Hash::make($defaultPwd, ['rounds' => 12]);
          }
           
          $bool_userExists = User::where('username' ,$username)->exists();

          if(!$request->filled('id') && $bool_userExists)
          {
           $message =  "username ".$name." has already been taken, choose another one";
           $dataArr = array("code" => '101',
                            "message" => $message,
                            "method" =>  $method);
           LogAfterRequest::LogRequest($request, $dataArr);
           return back()->with('fail', $message);
         }
         else
         {
         
          
          $user->first_name = $user_fname;
          $user->last_name = $user_lname;
          $user->name = $name;
          $user->username = $username;
          $user->gender = $user_gender;
          $user->email = $user_email;
          $user->user_role = $user_role;
          $user->tel_no = $user_telno;
          $user->alt_telno = $user_alt_telno;
          $user->address = $user_address;
          $user->nationalID_no = $user_nin;
          $user->password = $password;
          $user->isActive = 1;
          $user->inactivated_by = $registra;

          $save_status = $user->save();
          if($save_status)
          {

            $subject = 'User Registration';
            $userEmail = $request->email;
            $registraPosition = $this->getRole($request->user()->user_role);
            $registraEmail = $request->user()->email;
            $default_password = $defaultPwd;
            $now = now();

            $action = "registered user ".$name."";
            $sendAction = "You have been registered as a
                      ".$user_position." today at ".$now."";
            LogsController::logger($request, $action, now());

            $data = array(
              'name' => $name,
              'username' => $username,
              'password' => $default_password,
              'user_position' => 'user',
              'registra' => $registra,
              'registraPosition' => $registraPosition,
              'registraEmail' => $registraEmail,
              'email' => $userEmail,
              'subject' => $subject,
              'created_at' => $now,
              'details' => $sendAction,
              'activity' => 'registration',
            );

          
            $user->notify(new UserRegistration($data));

            $this->Enqueue($data);
            $sessionVariable = 'success';


           if($this->is_connectedToInternet() == 1){

            $text_message = "Hey ".$name."";
            $text_message .= "".$sendAction."";
            $text_message .= "Your username is
            ".$username." and password is ".$default_password."";

            
            $sms = new SmsController();
            $from = config("app.name");
            $isSmsSent = Helper::SendTextMessage($request, $user_telno, $text_message);

            if($isSmsSent == true)
            {

            $SMSmessage = "SMS sent to registered ".$user_position." ".$name." successfully";
            $SMSdataArr = array("code" => '201',
                                  "message" => $SMSmessage,
                                  "method" => $method);
            LogAfterRequest::LogRequest($request,  $SMSdataArr);
            }
            else{
                $SMSmessageErr = "SMS was never sent to registered ".$user_position." ".$name."!";
                $SMSdataErrArr = array("code" => '101',
                                 "message" => $SMSmessageErr,
                                  "method" => $method);
                LogAfterRequest::LogRequest($request,   $SMSdataErrArr);

            }

          }else{
            $message = "".ucwords($user_position)." registration failed";
            $sessionVariable = 'fail';
          }

            $message = "User ".$name." has been registered successfully";
            $dataArr = array("code" => '201',
                                  "message" => $message,
                                  "method" => $method);
            LogAfterRequest::LogRequest($request, $dataArr);

        $statArr = Helper::GetUserStats($user_position);
        $number_of_users = $statArr['totl'];

         return response()
         ->json([$sessionVariable => $message,
                 'totl_no' => $number_of_users,
         ]);

            // return back()->with("success", $message);

           /* if($request->has('email') &&
              $request->filled('email')){

              if($this->is_connectedToInternet() == 1){
                Mail::send('pages.mail.welcome', $data, function($message) use ($name,$default_password, $user_position,$registra,$registraPosition, $registraEmail, $userEmail,$subject)
                {
                  $message->from($registraEmail, 'Dallington');
                  $message->to($userEmail, 'Henry')->subject($subject);
                });

                if(Mail::failures())
                {
                  $message = "User ".$name." has been registered but an email has not been sent";
                  $dataArr = array("code" => '201',
                                    "message" => $message,
                                    "method" => $method);
                   LogAfterRequest::LogRequest($request, $dataArr);
                  return back()->with("success", $message);

                }


                else{
                  $message = "User ".$name." has been registered & an email about account  details has been sent";
                  $dataArr = array("code" => '200',
                                    "message" => $message,
                                    "method" => $method);
                   LogAfterRequest::LogRequest($request, $dataArr);
                  return back()->with("success", $message);
                }

              }
              else if($this->is_connectedToInternet() == 0)
              {
                 $message = "User ".$name." has been registered successfully but couldn't send email because of no Internet connection!";
                 $dataArr = array("code" => '201',
                 "message" => $message,
                 "method" => $method);
                 LogAfterRequest::LogRequest($request, $dataArr);
                 return back()->with("success", $message);

              }

            }*/


          }
          else
          {
            $message = "User registration failed!";
            $dataArr = array("code" => '101',
                             "message" => $message,
                              "method" => $method);
            LogAfterRequest::LogRequest($request, $dataArr);
            return back()->with('fail', $message);
          }
        }

    }


    
    public function fetchRolesAjax(Request $request) {
        try {
            if($request->ajax()){
            $roles = Role::get();
            echo json_encode($roles);
            die();
            
            }
        } catch (\Exception $ex) {
            echo "Error ".$ex->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    protected function getUserRoleId($role)
    {
      $roleId = DB::table('roles')
                 ->where('role', $role)
                  ->value('role_id');
      return $roleId;

    }


    protected function searchRole(Request $request)
    {
   //  if($request->input('role')){
        $role ="user"; // $request->input('role');
        $roleId = $this->getUserRoleId($role);
        echo json_encode($roleId);
    // }

   }

   protected function validatorData(Request $request)
   {
      $request->validate([
            'name' => 'required',
            'address' => 'required',
            'contact1' => 'required',
           // 'roleID' => 'required',
      ]);

   }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validatorData($request);

        $user = User::find($id);
        $method = "UserController@update";

        $name = $request->input('name');
        $address = $request->input('address');
        $primary_telno = $request->input('contact1');
        $roleId = 3; //$request->input('roleID');

        ($request->has('email') && $request->filled('email'))
        ? $email = $request->input('email')
        : $email = $user->email;

        ($request->has('contact2') && $request->filled('contact2'))
        ? $alt_telno = $request->input('contact2')
        : $alt_telno = $user->alt_telno;

        $person = $user->name;
        $username = $user->username;
        $user->name = $name;
        $user->address = $address;
        $user->tel_no = $primary_telno;
        $user->alt_telno = $alt_telno;
        $user->email = $email;
        $user->user_role = $roleId;

        $registra = $request->user()->name;
        $userEmail = $request->email;
        $user_position = $this->getRole($roleId);
        $registraPosition = $this->getRole($request->user()->user_role);
        $registraEmail = $request->user()->email;
        $default_password = "didn't change your password";
        $now = now();

        $isUpdated = $user->save();
        if($isUpdated)
        {

            $subject = "Change of account details";
            $sendAction = "Your details have been edited by ".$registra." today
            at ".$now.". Check your profile to see what has been changed or not";

            $data = array(
                'name' => $name,
                'username' => $username,
                'password' => $default_password,
                'user_position' => $user_position,
                'registra' => $registra,
                'registraPosition' => $registraPosition,
                'registraEmail' => $registraEmail,
                'email' => $userEmail,
                'subject' => $subject,
                'created_at' => $now,
                'details' => $sendAction,
                'activity' => 'detailsChange',
              );

              $user->notify(new UserRegistration($data));

              $this->Enqueue($data);

          $action = "updated details of user ".$person." ins the system";
          LogsController::logger($request, $action, now());
          $data = array("code" => '200',
          "message" => $action,
          "method" => $method);
          LogAfterRequest::LogRequest($request, $data);
          return back()
          ->with("success", $this->ActionMessage($action));

        }
        else
        {
          $messageErr = "Failedto update details of user ".$person."!";
          $dataArr = array("code" => '101',
                          "message" => $messageErr,
                          "method" => $method);
          LogAfterRequest::LogRequest($request, $dataArr);
          return back()
                     ->with('fail', $messageErr);
       }




    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        // dd($id);
        $hasRights = Gate::inspect('isSuperAdmin');
        $hasRights1 = Gate::inspect('isAdmin');
        $user = User::find($id);
        $user_position = Helper::getRole($user->user_role);
        if($hasRights->allowed() || $hasRights1->allowed()){
       
        $method = "UserController@destroy";
        $isDeleted = $user->delete();

        if($isDeleted){
            $name = $user->name;
            $action = "removed user ".$name." from the system";
            LogsController::logger($request, $action, now());
            $data = array("code" => '200',
            "message" => $action,
            "method" => $method);
            LogAfterRequest::LogRequest($request, $data);
            $sessionVariable = 'success';
            $message = $this->ActionMessage($action);
     }
    else{
      $messageErr = "Users ".$name." not removed from the system!";
      $dataArr = array("code" => '101',
                      "message" => $messageErr,
                      "method" => $method);
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'fail';
      $message = $messageErr;
   }

        $statArr = Helper::GetUserStats($user_position);
        $number_of_users = $statArr['totl'];

         return response()
         ->json([$sessionVariable => $message,
                 'totl_no' => $number_of_users,
         ]);

        }

    }

    public function RemoveAllActiveUsers(Request $request)
    {

        $response = Gate::inspect('isSuperAdmin');
        if($response->allowed()){

    $method = "UserController@RemoveAllActiveUsers";
    $isTruncated = User::where('isActive', true)->delete();
    if($isTruncated)
    {

      $action = "removed all active users from the system";
      LogsController::logger($request, $action, now());
      $data = array("code" => '200',
      "message" => $action,
      "method" => $method);
      LogAfterRequest::LogRequest($request, $data);
      return back()
      ->with("success", $this->ActionMessage($action));

    }
    else
    {
      $messageErr = 'Active users not removed from the system!';
      $dataArr = array("code" => '101',
                      "message" => $messageErr,
                      "method" => $method);
      LogAfterRequest::LogRequest($request, $dataArr);
      return back()
                 ->with('fail', $messageErr);
   }

}

    }

    public function RemoveAllLockedUsers(Request $request)
    {

         $response = Gate::inspect('isSuperAdmin');

        if($response->allowed()){
    $method = "UserController@RemoveAllLockedUsers";
    $isTruncated = User::where('isActive', false)->delete();
    if($isTruncated)
    {

      $action = "removed all locked users from the system";
      LogsController::logger($request, $action, now());
      $data = array("code" => '200',
      "message" => $action,
      "method" => $method);
      LogAfterRequest::LogRequest($request, $data);
      return back()
      ->with("success", $this->ActionMessage($action));

    }
    else
    {
      $messageErr = 'Locked users not removed from the system!';
      $dataArr = array("code" => '101',
                      "message" => $messageErr,
                      "method" => $method);
      LogAfterRequest::LogRequest($request, $dataArr);
      return back()
                 ->with('fail', $messageErr);
   }

}

    }


 public function RemoveSelected(Request $request)
    {
        try {
            $ids =  $request->input('selected_rows');
            $DeletedUsers = array();

            if (count($ids) > 0) {
              $extUser = User::find($ids[0]);
              $user_position = Helper::getRole($extUser->user_role);
                foreach ($ids as $id) {
                    $user = User::find($id);
                    $user->delete();
                    array_push($DeletedUsers, $user->name);
                }
            }
            $sessionVariable = 'success';
            $deletedUsersStr = implode(", ", $DeletedUsers);
            $action = "removed users ".$deletedUsersStr." from the system";
            if (count($ids) == 1) {
                $action = Str::replaceFirst('users', 'user', $action);
            }
            $response = $this->SuccessMessage($action);

            $dataArr = array("code" => '200',
                "message" => $action,
                "method" => "UsersController@RemoveSelected"
            );
            LogsController::logger($request, $action, now());
            LogAfterRequest::LogRequest($request, $dataArr);

            $statArr = Helper::GetUserStats($user_position);
            $number_of_users = $statArr['totl'];

         return response()
         ->json([$sessionVariable => $response,
                 'totl_no' => $number_of_users,
         ]);

           
        } catch (\Exception $ex) {
            $data = array(
          'username' => auth()->user()->username,
          'error_code' => $ex->getCode(),
          'error_message' => $ex->getMessage(),
          'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
          'controller' => $this->controller,
          'method' => 'RemoveSelected'
        );
            Helper::logError($data);
            abort(409, $ex->getMessage());
        }
    }



protected function ActionMessage($action)
{
  $message = "You have successfully ".$action."";
  return $message;
}

private function getUserRole($userRoleId)
    {
      $userRole = DB::table('roles')
                 ->where('role_id', $userRoleId)
                  ->value('role');
      return $userRole;

    }


    //method to check if there is internet connection

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


   
  protected function SuccessMessage($msg)
{
  $message = "You have successfully ".$msg."";
  return $message;
}


protected function FailedMessage($failmsg)
{
  $message = "".$failmsg."";
  return $message;
}




}
