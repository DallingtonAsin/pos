<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use App\Http\Controllers\LogsController;
use Illuminate\Support\Str;
use App\Imports\ImportCashiers;
use App\Exports\ExportCashiers;
use Illuminate\Support\Facades\Mail;
use App\Notifications\UserRegistration;
use App\Http\Controllers\LogAfterRequest;
use App\Models\Role;
use App\Jobs\MailRegistration;
use App\User;
use Excel;
use App\Helpers\Helper;
use  App\Helpers\Constants as Constant;

class CashiersController extends Controller
{
    use Notifiable;

    private $controller;

    public function __construct()
    {
        $this->controller = 'CashiersController';
    }


    protected function getUserRoleId($role)
    {
        try {
            $roleId = Role::where('role', 'like', '%'.$role.'%')
                     ->value('role_id');
            return $roleId;
        } catch (\Exception $ex) {
            $data = array(
                'username' => auth()->user()->username,
                'error_code' => $ex->getCode(),
                'error_message' => $ex->getMessage(),
                'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
                'controller' => $this->controller,
                'method' => 'getRole'
            );
            Helper::logError($data);
            abort(409, $ex->getMessage());
        }
    }

    protected function getRole($id)
    {
        try {
            $role = Role::where('role_id', $id)
                   ->value('role');
            return $role;
        } catch (\Exception $ex) {
            $data = array(
      'username' => auth()->user()->username,
      'error_code' => $ex->getCode(),
      'error_message' => $ex->getMessage(),
      'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
      'controller' => $this->controller,
      'method' => 'getRole'
  );
            Helper::logError($data);
            abort(409, $ex->getMessage());
        }
    }

    protected function GetCashierStats()
    {
        try {
            $role = 'Cashier';
            $role_id = $this->getUserRoleId($role);
            $cashiers_list = User::where('user_role', $role_id)->get();
            $number_of_cashiers = User::where('user_role', $role_id)->count();
            $data = array(
        'totl' => $number_of_cashiers,
        'list' => $cashiers_list
        );

            return $data;
        } catch (\Exception $ex) {
            $data = array(
          'username' => auth()->user()->username,
          'error_code' => $ex->getCode(),
          'error_message' => $ex->getMessage(),
          'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
          'controller' => $this->controller,
          'method' => 'GetCashierStats'
      );
            Helper::logError($data);
            abort(409, $ex->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $arr = $this->GetCashierStats();
            $number_of_cashiers = $arr['totl'];
            $cashiers = $arr['list'];
            return view('pages.main.cashiers')
               ->with(compact('cashiers', 'number_of_cashiers'));
        } catch (\Exception $ex) {
            $data = array(
                  'username' => auth()->user()->username,
                  'error_code' => $ex->getCode(),
                  'error_message' => $ex->getMessage(),
                  'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
                  'controller' => $this->controller,
                  'method' => 'index'
              );
            Helper::logError($data);
            abort(409, $ex->getMessage());
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


    private function Enqueue($CashierData)
    {
        MailRegistration::dispatch($CashierData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
          'firstName' => ['required'],
          'lastName' => ['required'],
          'address' => ['required'],
          'tel_no' => ['required'],
          'NationalIDNo' => ['required'],
          'gender' => ['required'],
        ]);

        $method = "CashiersController@store";

        try {
            ($request->has('alt_telno') && $request->filled('alt_telno'))
        ? $cashier_alt_telno = trim($request->input('alt_telno'))
        : $cashier_alt_telno = null;

            ($request->has('email') &&  $request->filled('email'))
        ? $cashier_email = trim($request->input('email'))
        : $cashier_email = null;


            $cashier_fname = trim($request->input('firstName'));
            $cashier_lname = trim($request->input('lastName'));
            $cashier_address = trim($request->input('address'));
            $cashier_telno = trim($request->input('tel_no'));
            $cashier_nin = trim($request->input('NationalIDNo'));
            $cashier_gender = trim($request->input('gender'));
            $role = trim($request->input('role'));
            $registra = Auth::user()->name;

            $cashier_name = $cashier_fname." ".$cashier_lname;
            $username = strtolower(Str::random(6).".".$cashier_fname);
            $defaultPwd = '12345678'; // Str::random(8);


            $bool_userExists = User::where('username', $username)->exists();

            if ($bool_userExists) {
                $message =  "username ".$cashier_name." has already been taken, choose another one";
                $dataArr = array("code" => '101',
                          "message" => $message,
                          "method" => $method);
                LogAfterRequest::LogRequest($request, $dataArr);
                return back()->with('fail', $message);
            } else {
                $cashier = new User();
                $role = 'Cashier';
                $cashier->first_name = $cashier_fname;
                $cashier->last_name = $cashier_lname;
                $cashier->name = $cashier_name;
                $cashier->username = $username;
                $cashier->gender = $cashier_gender;
                $cashier->email = $cashier_email;
                $cashier->user_role = $this->getUserRoleId($role);
                $cashier->tel_no = $cashier_telno;
                $cashier->alt_telno = $cashier_alt_telno;
                $cashier->address = $cashier_address;
                $cashier->nationalID_no = $cashier_nin;
                $cashier->password = Hash::make($defaultPwd, ['rounds' => 12]);
                $cashier->isActive = 1;
                $cashier->inactivated_by = $registra;

                $save_status = $cashier->save();
                if ($save_status) {
                    $subject = 'User Registration';
                    $CashierEmail = $request->email;
                    $registraPosition = $this->getRole($request->user()->user_role);
                    $registraEmail = $request->user()->email;
                    $default_password = $defaultPwd;
                    $now = now();

                    $action = "Registered cashier ".$cashier_name."";
                    LogsController::logger($request, $action, now());
                    $user_position = 'Cashier';
                    $sendAction = "You have been registered as a
                      ".$user_position." today at ".$now."";

                    $data = array(
                        'name' => $cashier_name,
                        'username' => $username,
                        'password' => $default_password,
                        'user_position' => $user_position,
                        'registra' => $registra,
                        'registraPosition' => $registraPosition,
                        'registraEmail' => $registraEmail,
                        'email' => $CashierEmail,
                        'subject' => $subject,
                        'created_at' => $now,
                        'details' => $sendAction,
                        'activity' => 'registration',
                    );



                    $cashier->notify(new UserRegistration($data));

                    $this->Enqueue($data);

                    $text_message = "Hey ".$cashier_name.".".$sendAction.".Your username is
                    ".$username." and password is ".$default_password."";


                    // if ($this->is_connectedToInternet() == 1) {
                    //     $from = config("app.name");
                    //     $isSmsSent = $this->SendTextMessage($from, $cashier_telno, $text_message);

                    //     if ($isSmsSent == true) {
                    //         $SMSmessage = "SMS sent to registered ".$user_position." ".$cashier_name." successfully";
                    //         $SMSdataArr = array("code" => '200',
                    //             "message" => $SMSmessage,
                    //             "method" => $method);
                    //         LogAfterRequest::LogRequest($request, $SMSdataArr);
                    //     } else {
                    //         $SMSmessageErr = "SMS was never sent to registered ".$user_position." ".$cashier_name."!";
                    //         $SMSdataErrArr = array("code" => '101',
                    //            "message" => $SMSmessageErr,
                    //             "method" => $method);
                    //         LogAfterRequest::LogRequest($request, $SMSdataErrArr);
                    //     }
                    // }

                    $message = "Cashier ".$cashier_name." has been registered successfully";
                    $dataArr = array("code" => '201',
                                "message" => $message,
                                "method" => $method);
                    LogAfterRequest::LogRequest($request, $dataArr);

                    return back()->with("success", $message);


                /*  if($request->has('email') &&
                    $request->filled('email')){

                    if($this->is_connectedToInternet() == 1){
                      Mail::send('pages.mail.welcome', $data, function($message)
                                  use ($cashier_name,$default_password, $user_position,
                                       $registra,$registraPosition, $registraEmail, $CashierEmail,$subject)
                      {
                        $message->from($registraEmail, 'Dallington');
                        $message->to($CashierEmail, 'Henry')->subject($subject);
                      });

                      if(Mail::failures())
                      {
                        $message = "Cashier ".$cashier_name." has been registered but an email has not been sent";
                        $dataArr = array("code" => '201',
                                          "message" => $message,
                                          "method" => "CashiersController@store");
                         LogAfterRequest::LogRequest($request, $dataArr);
                        return back()->with("success", $message);

                      }


                      else{
                        $message = "Cashier ".$cashier_name." has been registered & an email about account  details has been sent";
                        $dataArr = array("code" => '200',
                                          "message" => $message,
                                          "method" => "CashiersController@store");
                         LogAfterRequest::LogRequest($request, $dataArr);
                        return back()->with("success", $message);
                      }

                    }
                    else if($this->is_connectedToInternet() == 0)
                    {
                       $message = "Cashier ".$cashier_name." has been registered successfully but couldn't send email because of no Internet connection!";
                       $dataArr = array("code" => '201',
                       "message" => $message,
                       "method" => "CashiersController@store");
                       LogAfterRequest::LogRequest($request, $dataArr);
                       return back()->with("success", $message);

                    }

                  }
                  else
                  {
                    $message = "Cashier ".$cashier_name." has been registered successfully";
                    $dataArr = array("code" => '201',
                                          "message" => $message,
                                          "method" => "CashiersController@store");
                    LogAfterRequest::LogRequest($request, $dataArr);
                    return back()->with("success", $message);
                  }*/
                } else {
                    $message = "Cashier registration failed!";
                    $dataArr = array("code" => '101',
                           "message" => $message,
                            "method" => $method);
                    LogAfterRequest::LogRequest($request, $dataArr);
                    return back()->with('fail', $message);
                }
            }
        } catch (\Exception $ex) {
            $data = array(
          'username' => auth()->user()->username,
          'error_code' => $ex->getCode(),
          'error_message' => $ex->getMessage(),
          'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
          'controller' => $this->controller,
          'method' => 'store'
      );
            Helper::logError($data);
            abort(409, $ex->getMessage());
        }
    }


    protected function SendTextMessage($from, $receiver, $message)
    {

        try{
        // Call API Nexmo
        $nexmo = app('Nexmo\Client');
        $result = $nexmo->message()->send([
             'to' => $receiver,
             'from' => $from,
             'text' => $message,
      ]);
        ($result)
      ? $isSent = true
      : $isSent = false;

        return $isSent;
        
        }catch(\Exception $ex){
            throw $ex;
        }
    }



    //method to check if there is internet connection

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


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    public function RemoveSelectedCashiers(Request $request)
    {
        try {
            $ids =  $request->input('CashierId');
            $DeletedCashiers = array();

            if (count($ids) > 0) {
                foreach ($ids as $id) {
                    $findId = User::find($id);
                    $findId->delete();
                    array_push($DeletedCashiers, $findId->name);
                }
            }
            $sessionVariable = 'success';
            $deletedCashiersStr = implode(", ", $DeletedCashiers);
            $action = "removed cashiers ".$deletedCashiersStr." from the system";
            if (count($ids) == 1) {
                $action = Str::replaceFirst('cashiers', 'cashier', $action);
            }
            $response = $this->ActionMessage($action);

            $dataArr = array("code" => '200',
                "message" => $action,
                "method" => "CashiersController@RemoveSelectedCashiers"
            );
            LogsController::logger($request, $action, now());
            LogAfterRequest::LogRequest($request, $dataArr);

            $arr = $this->GetCashierStats();
            $number_of_cashiers = $arr['totl'];

            return response()
        ->json([$sessionVariable => $response,
                'totl' => $number_of_cashiers,
        ]);
        } catch (\Exception $ex) {
            $data = array(
          'username' => auth()->user()->username,
          'error_code' => $ex->getCode(),
          'error_message' => $ex->getMessage(),
          'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
          'controller' => $this->controller,
          'method' => 'RemoveSelectedCashiers'
        );
            Helper::logError($data);
            abort(409, $ex->getMessage());
        }
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
        $request->validate([
          'firstName' => ['required'],
          'lastName' => ['required'],
          'address' => ['required'],
          'tel_no' => ['required'],
          'NationalIDNo' => ['required'],
          'gender' => ['required'],
        ]);


        try {
            $cashier = User::find($id);
            $names = $cashier->first_name." ".$cashier->last_name;
            ($request->has('alt_telno') && $request->filled('alt_telno'))
        ? $cashier_alt_telno = trim($request->input('alt_telno'))
        : $cashier_alt_telno = null;

            ($request->has('email') &&  $request->filled('email'))
        ? $cashier_email = trim($request->input('email'))
        : $cashier_email = null;

            $cashier_fname = trim($request->input('firstName'));
            $cashier_lname = trim($request->input('lastName'));
            $cashier_address = trim($request->input('address'));
            $cashier_telno = trim($request->input('tel_no'));
            $cashier_nin = trim($request->input('NationalIDNo'));
            $cashier_gender = trim($request->input('gender'));
            $registra = $request->user()->name;

            $cashier_name = $cashier_fname." ".$cashier_lname;

            $cashier->first_name = $cashier_fname;
            $cashier->last_name = $cashier_lname;
            $cashier->name = $cashier_name;
            $cashier->gender = $cashier_gender;
            $cashier->email = $cashier_email;
            $cashier->tel_no = $cashier_telno;
            $cashier->alt_telno = $cashier_alt_telno;
            $cashier->address = $cashier_address;
            $cashier->nationalID_no = $cashier_nin;
            $res = $cashier->save();

            if ($res) {
                $subject = 'Update about User Details Change';
                $CashierEmail = $request->email;
                $registraPosition = $this->getRole($request->user()->user_role);
                $registraEmail = $request->user()->email;
                $now = now();

                $action = "updated cashier ".$names."'s details";

                LogsController::logger($request, $action, now());
                $password = "didn't change your password";
                $user_position = 'Cashier';
                $username = "didn't change [in otherwords remains the same";
                $sendAction = "Your details have been edited by ".$registra." today
                at ".$now.". Check your profile to see what has been changed or not";

                $data = array(
                    'cashier_name' => $names,
                    'name' => $names,
                    'username' =>  $username,
                    'password' =>  $password,
                    'user_position' => $user_position,
                    'registra' => $registra,
                    'registraPosition' => $registraPosition,
                    'registraEmail' => $registraEmail,
                    'email' => $CashierEmail,
                    'subject' => $subject,
                    'created_at' => $now,
                    'details' => $sendAction,
                    'activity' => 'detailsChange',
                );

                $cashier->notify(new UserRegistration($data));

                $this->Enqueue($data);

                $dataArr = array("code" => '201',
          "message" => $action,
          "method" => "CashiersController@update");
                LogAfterRequest::LogRequest($request, $dataArr);
                return back()->with("success", $this->ActionMessage($action));

                if ($request->has('email') && $request->filled('email')) {
                    if ($this->is_connectedToInternet() == 1) {
                        Mail::send('pages.mail.update-about-cashier-details', $data, function ($message) use (
                            $names,
                            $username,
                            $password,
                            $user_position,
                            $registra,
                            $registraPosition,
                            $registraEmail,
                            $CashierEmail,
                            $subject
                        ) {
                            $message->from($registraEmail, 'Dallington');
                            $message->to($CashierEmail, 'Henry')->subject($subject);
                        });

                        if (Mail::failures()) {
                            $message = "".$this->ActionMessage($action)." but an email has not been sent";
                            $dataArr = array("code" => '201',
                                  "message" => $message,
                                  "method" => "CashiersController@update");
                            LogAfterRequest::LogRequest($request, $dataArr);
                            return back()->with("success", $message);
                        } else {
                            $message = "".$this->ActionMessage($action)." & an email about account  details has been sent";
                            $dataArr = array("code" => '200',
                                  "message" => $message,
                                  "method" => "CashiersController@update");
                            LogAfterRequest::LogRequest($request, $dataArr);
                            return back()->with("success", $message);
                        }
                    } elseif ($this->is_connectedToInternet() == 0) {
                        $message = "".$this->ActionMessage($action)." but couldn't send email because of no Internet connection!";
                        $dataArr = array("code" => '201',
                                "message" => $message,
                                "method" => "CashiersController@update");
                        LogAfterRequest::LogRequest($request, $dataArr);
                        return back()->with("success", $message);
                    }
                }
            } else {
                $message = "Failed to update cashier ".$cashier_name."'s details, please re-try failed!";
                $dataArr = array("code" => '101',
                            "message" => $message,
                            "method" => "CashiersController@update");
                LogAfterRequest::LogRequest($request, $dataArr);
                return back()->with('fail', $action);
            }
        } catch (\Exception $ex) {
            $data = array(
          'username' => auth()->user()->username,
          'error_code' => $ex->getCode(),
          'error_message' => $ex->getMessage(),
          'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
          'controller' => $this->controller,
          'method' => 'update'
      );
            Helper::logError($data);
            abort(409, $ex->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $cashier = User::find($id);
            $cashier_name = $cashier->name;
            $cashier_delete_status = $cashier->delete();

            if ($cashier_delete_status) {
                $action = "removed cashier ".$cashier_name." from list of cashiers in the system";
                LogsController::logger($request, $action, now());

                $dataArr = array("code" => '200',
          "message" => $action,
          "method" => "CashiersController@destroy");
                LogAfterRequest::LogRequest($request, $dataArr);

                return back()->with("success", $this->ActionMessage($action));
            } else {
                $messageError = "Cashier not removed!";
                $dataArr = array("code" => '101',
                  "message" => $messageError,
                "method" => "CashiersController@destroy");
                LogAfterRequest::LogRequest($request, $dataArr);
                return back()->with('fail', $messageError);
            }
        } catch (\Exception $ex) {
            $data = array(
          'username' => auth()->user()->username,
          'error_code' => $ex->getCode(),
          'error_message' => $ex->getMessage(),
          'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
          'controller' => $this->controller,
          'method' => 'destroy'
      );
            Helper::logError($data);
            abort(409, $ex->getMessage());
        }
    }

    public function deleteAllCashiers(Request $request)
    {
        try {
            $CashierRoleId = $this->getUserRoleId('Cashier');
            $result = User::where('user_role', $CashierRoleId)->delete();

            if ($result) {
                $action = "removed all cashiers from the system";
                LogsController::logger($request, $action, now());

                $dataArr = array("code" => '200',
                                 "message" => $action,
                                 "method" => "CashiersController@deleteAllCashiers");

                LogAfterRequest::LogRequest($request, $dataArr);
                $sessionVariable = 'success';
                $responseInfo = $this->ActionMessage($action);
            } else {
                $error_message = "cashiers not removed from the system!";
                $dataArr = array("code" => '101',
                                 "message" => $error_message,
                                 "method" => "CashiersController@deleteAllCashiers");
                LogAfterRequest::LogRequest($request, $dataArr);
                $sessionVariable = 'fail';
                $responseInfo = $error_message;
            }

            $arr = $this->GetCashierStats();

            return response()
     ->json([$sessionVariable => $responseInfo,
             'totl' => $arr['totl'],
     ]);
        } catch (\Exception $ex) {
            $data = array(
                'username' => auth()->user()->username,
                'error_code' => $ex->getCode(),
                'error_message' => $ex->getMessage(),
                'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
                'controller' => $this->controller,
                'method' => 'deleteAllCashiers');
            Helper::logError($data);
            abort(409, $ex->getMessage());
        }
    }

    public function importCashiers(Request $request)
    {
        $this->validate(
            $request,
            ['select_file' => 'required|mimes:xls,xlsx'],
            ['select_file.mimes' => 'Please select only excel files to import cashiers']
        );
        try {
            $importSuccess = Excel::import(new ImportCashiers, request()->file('select_file'));

            if ($importSuccess) {
                $action = "imported an excel file of cashiers into the system";
                LogsController::logger($request, $action, now());

                $dataArr = array("code" => '200',
                "message" => $action,
                "method" => "CashiersController@importCashiers");
                LogAfterRequest::LogRequest($request, $dataArr);

                return back()->with('success', $this->ActionMessage($action));
            } else {
                $error_message = "Excel Cashiers data not imported!";
                $dataArr = array("code" => '101',
                "message" => $error_message,
                "method" => "CashiersController@importCashiers");
                LogAfterRequest::LogRequest($request, $dataArr);
                return back()->with('fail', $error_message);
            }
        } catch (\Exception $ex) {
            $data = array(
          'username' => auth()->user()->username,
          'error_code' => $ex->getCode(),
          'error_message' => $ex->getMessage(),
          'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
          'controller' => $this->controller,
          'method' => 'importCashiers');
            Helper::logError($data);
            abort(409, $ex->getMessage());
        }
    }

    public function exportCashiers()
    {
        try {
            $download = Excel::download(new ExportCashiers, 'cashiers.xlsx');
            return $download;
        } catch (\Exception $ex) {
            $data = array(
            'username' => auth()->user()->username,
            'error_code' => $ex->getCode(),
            'error_message' => $ex->getMessage(),
            'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
            'controller' => $this->controller,
            'method' => 'exportCashiers');
            Helper::logError($data);
            abort(409, $ex->getMessage());
        }
    }

    public function ChangeAccountStatus(Request $request, $id, $status, $name)
    {
        try {


            switch (true) {

                case ($status == true):
                $deactivated = User::where('id', $id)->update(['isActive' => false]);
                if ($deactivated) {
                    $action = "deactivated cashier ".$name."'s account";
                    LogsController::logger($request, $action, now());

                    $dataArr = array("code" => '200',
                "message" => $action,
                "method" => "CashiersController@ChangeAccountStatus");
                    LogAfterRequest::LogRequest($request, $dataArr);

                    return back()->with('success', $this->ActionMessage($action));
                } else {
                    $error_message = "Account deactivation failed!";
                    $dataArr = array("code" => '101',
                "message" => $error_message,
                "method" => "CashiersController@ChangeAccountStatus");
                    LogAfterRequest::LogRequest($request, $dataArr);

                    return back()->with('fail', $error_message);
                }
                break;

                case ($status == false):
                $activated = User::where('id', $id)->update(['isActive' => true]);
                if ($activated) {
                    $action = "activated cashier ".$name."'s account";
                    LogsController::logger($request, $action, now());
                    $dataArr = array("code" => '200',
                "message" => $action,
                "method" => "CashiersController@ChangeAccountStatus");
                    LogAfterRequest::LogRequest($request, $dataArr);

                    return back()->with('success', $this->ActionMessage($action));
                } else {
                    $error_message = "Account deactivation failed!";
                    $dataArr = array("code" => '101',
                "message" => $error_message,
                "method" => "CashiersController@ChangeAccountStatus");
                    LogAfterRequest::LogRequest($request, $dataArr);
                    return back()->with('fail', $error_message);
                }

                break;

            }
        } catch (\Exception $ex) {
            $data = array(
            'username' => auth()->user()->username,
            'error_code' => $ex->getCode(),
            'error_message' => $ex->getMessage(),
            'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
            'controller' => $this->controller,
            'method' => 'ChangeAccountStatus');
            Helper::logError($data);
            abort(409, $ex->getMessage());
        }
    }


    protected function ActionMessage($action)
    {
        $message = "You have successfully ".$action."";
        return $message;
    }
}
