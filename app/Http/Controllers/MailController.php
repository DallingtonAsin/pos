<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogAfterRequest;
use App\Jobs\SendingEmail;
use App\Models\Role;
use App\User;


class MailController extends Controller
{
 /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.main.mail');
    }

    protected function getRole($role_id)
    {
        $role = Role::where('id', $role_id)->value('name');
        return $role;
    }

    protected function GetRoleId($role)
    {
        $role_id = Role::where('name', $role)->value('id');
        return $role_id;
    }

    protected function GetUserData($UserRoleId)
    {
        $userEmails = User::where('role_id', $UserRoleId)->get();
        return $userEmails;
    }


    protected function ValidationA(Request $request)
    {
        $this->validate($request, [
            'receiverName' => 'required',
            'receiverEmail' => 'required',
        ]);

    }

    protected function ValidationB(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required',
            'message' => 'required',

        ]);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {



        $emails = array();

        $senderName = $request->user()->name;
        $senderEmail = $request->user()->email;

        $method = "MailController@store";
        $toAllCashiers = $request->input("toAllCashiers");
        $toAllManagers = $request->input("toAllManagers");

        if($toAllCashiers == 'yes' || $toAllManagers == 'yes'){

          $this->ValidationB($request);

          $subject = $request->input("subject");
          $writing = $request->input("message");

        if($toAllCashiers == 'yes')
        {
            $role = 'Cashier';
            $CashierRoleId = $this->GetRoleId($role);
            $CashiersData = $this->GetUserData($CashierRoleId);
            foreach($CashiersData as $cashier){
                array_push($emails, $cashier->email);
            }
            $receiverId = "all cashiers";

        }

        if($toAllManagers == 'yes')
        {
            $role = 'Administrator';
            $AdminRoleId = $this->GetRoleId($role);
            $AdminData = $this->GetUserData($AdminRoleId);
            foreach($AdminData as $admin){
                array_push($emails, $admin->email);
            }
            $receiverId = "all managers";
        }

        ($toAllCashiers == 'yes' && $toAllManagers == 'yes')
        ? $receiverName = "all cashiers and managers"
        : $receiverName = $receiverId;


    }else
    {
        $this->ValidationA($request);
        $this->ValidationB($request);

        $subject = $request->input("subject");
        $writing = $request->input("message");
        $receiver = $request->input("receiverName");
        $receiverEmail = $request->input("receiverEmail");
        array_push($emails, $receiverEmail);
        $receiverName = $receiver;

    }

        $position = $this->getRole($request->user()->role_id); //position of the person sending the email
        
        $data = array(
            'type' => 'mail',
            'senderName' => $senderName,
            'senderEmail' => $senderEmail,
            'receiverName' => $receiverName,
            'receiverEmail' => $emails,
            'position' => $position,
            'subject' => $subject,
            'writing' => $writing,
            'recordedOn' => date("Y-m-d H:i:s.u"),
        );
        
        
        if($request->hasfile('email-attachment')){

                $uploadedFileDetails = $attachments = array();

                foreach($request->file('email-attachment') as $attachment)
                {
                  
                    $path = $attachment->getRealPath();
                    $fileName = $attachment->getClientOriginalName();
                    $mime = $attachment->getClientMimeType();
                    $extension = $attachment->extension();

                    array_push($uploadedFileDetails,  array(
                        $path,
                        $fileName,
                        $mime
                    ));

                    $contents= file_get_contents($path);
                    Storage::disk('public')->put($fileName, $contents);


                }        
         }
         else
         {
                $uploadedFileDetails = null;
         }

       
          $isMailQueued = $this->Enqueue($data,  $uploadedFileDetails);

            $action = "sent an email message to ".$receiverName."";
            LogsController::logger($request, $action, now());
            $dataArr = array("code" => '200',
            "message" => "".$senderName." at email ".$senderEmail." ".$action."",
            "method" => $method
             );

            LogAfterRequest::LogRequest($request, $dataArr);

            // $usersToBeNotified = $this->GetUsersToNotify($receiverEmail);
            // $notified = Notification::send($usersToBeNotified, new NewEmailNotifier($data));

             return back()
                        ->with('success', $this->ActionMessage($action));


       }

       protected function GetUsersToNotify($email)
       {
           $users = User::where('email', $email)->get();
           return $users;
       }


       protected function Enqueue($data, $attachment)
       {
           dispatch(new SendingEmail($data, $attachment));
        //    SendingEmail::SaveInQueuedMails($data, $data['receiverEmail'], $data['writing']);
       }

public static function is_connectedToInternet()
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


public function jobs()
{
    return QueuedEmails::orderBy('created_at', 'desc')->get()->toArray();
}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mail  $mail
     * @return \Illuminate\Http\Response
     */
    public function show(Mail $mail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mail  $mail
     * @return \Illuminate\Http\Response
     */
    public function edit(Mail $mail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mail  $mail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mail $mail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mail  $mail
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mail $mail)
    {
        //
    }

       protected function ActionMessage($action)
      {
  $message = "You have successfully ".$action."";
  return $message;
     }






}
