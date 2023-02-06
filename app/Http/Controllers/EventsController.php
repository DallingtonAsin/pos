<?php

namespace App\Http\Controllers;

use App\Notifications\NotifyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;
use App\Notifications\EventNotifier;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogAfterRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use Helper;
use App\Jobs\MailEvent;
use App\DataTables\EventsDataTable;
use App\User;


class EventsController extends Controller
{
  use Notifiable;

  private $old_title, $old_content, $old_sdate, $old_edate, $old_stime;


  public function __construct()
  {
  }

  public function GetEvents(EventsDataTable $dataTable)
  {
    return $dataTable->render('pages.events.index');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $no_of_events = Event::where('start_date', '>=', date('Y-m-d'))->count();
    $events = DB::table('events')->where('start_date', '>=', date('Y-m-d'))->get();
    return view('pages.events.index')
      ->with(compact('events', 'no_of_events'));
  }

  public function ShowEventForm()
  {

    return view('pages.events.events');
  }


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('pages.events.create');
  }

  public function getUserEmails()
  {
    $users = User::all();
    $userEmails = array();
    foreach ($users as $user) {
      array_push($userEmails, $user->email);
    }
    return $userEmails;
  }

  protected function GetUserNames($mailsArr)
  {
    foreach ($mailsArr as $email) {
      $UserNames = $this->GetUserNameFromDB($email);
    }
    return $UserNames;
  }


  protected function GetUserNameFromDB($email)
  {
    $name = User::where("email", $email)
      ->value("name");
    return $name;
  }

  protected function ValidateEvent(Request $request)
  {


    $request->validate([
      'event-title' => 'required',
      'event-message' => 'required',
      'eventStart-date' => 'required',
      'event-time' => 'required'
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

    $this->ValidateEvent($request);
    $event = new Event();

    $title = request('event-title');
    $startDate = request('eventStart-date');
    $time = request('event-time');
    $event_description = request('event-message');

    $isStartDategreater = date('m/d/Y') > date('m/d/Y', strtotime($startDate));

    if ($request->has('eventEnd-date') && $request->filled('eventEnd-date')) {
      $endDate = request('eventEnd-date');
      $isEndDategreater = date('m/d/Y') > date('m/d/Y', strtotime($endDate));
    } else {
      $endDate = null;
    }

    if ($isStartDategreater) {

      $msg = "You cannot register an event with start date as a date that is already past!";
      $msgErr = "" . $request->user()->name . " tried to record an event with a start date less than registration date " . now() . "";
      $dataArr = array(
        "code" => '404',
        "message" => $msgErr,
        "method" => "EventsController@store"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      return back()
        ->withInput()
        ->with('fail', $msg);
    } else {

      if ($request->filled('eventEnd-date') &&  $isEndDategreater == true) {
        $msg = "You cannot register an event with end date as a date that is already past!";
        $msgErr = "" . $request->user()->name . " tried to record an event with an end date less than registration date " . now() . "";
        $dataArr = array(
          "code" => '404',
          "message" => $msgErr,
          "method" => "EventsController@store"
        );
        LogAfterRequest::LogRequest($request, $dataArr);
        return back()
          ->withInput()
          ->with('fail', $msg);
      }

      $event_description = preg_replace("/^<p.*?>/", "", $event_description);
      $event_description = preg_replace("|</p>$|", "", $event_description);
      $time = date('H:i:s', strtotime($time));
      $registra = $request->user()->name;

      $event->title = $title;
      $event->start_date = $startDate;
      $event->end_date = $endDate;
      $event->start_time = $time;
      $event->description = $event_description;
      $event->event_registra = $registra;
      $registraEmail = $request->user()->email;
      $registraPhone = $request->user()->tel_no;
      $subject = "New Event";

      $event_save_status = $event->save();

      if ($event_save_status) {

        $users = User::all();

        ($endDate == "" || $endDate == null)
          ? $endDate_of_event = "the same day"
          : $endDate_of_event = $endDate;
        $user_emails = $this->getUserEmails();

        $data = array(
          'type' => 'notification',
          'title' => $title,
          'description' => $event_description,
          'start_date' => $startDate,
          'end_date' => $endDate_of_event,
          'time' => $time,
          'registra' => $registra,
          'registra_email' => $registraEmail,
          'registraMobileNo' => $registraPhone,
          'subject' => $subject,
          'email' => $user_emails,
          'recordedOn' => date("M d, Y h:i A"),
        );

        $action = "recorded an event with the title " . $title . "";
        LogsController::logger($request, $action, now());
        $dataArr = array(
          "code" => '200',
          "message" => $action,
          "method" => "EventsController@store"
        );
        LogAfterRequest::LogRequest($request, $dataArr);

        // $notified = Notification::send($users, new EventNotifier($data));
        // $this->EnqueueEvent($data); 

        return back()->with('success', $this->SuccessMessage($action));
      } else {

        $messageErr = 'Event has not been recorded!';
        $dataArr = array(
          "code" => '101',
          "message" => $messageErr,
          "method" => "EventsController@store"
        );
        LogAfterRequest::LogRequest($request, $dataArr);
        return back()
          ->withInput()
          ->with('fail', $messageErr);
      }
    }
  }


  public function EnqueueEvent($EventData)
  {
    MailEvent::dispatch($EventData);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $event = Event::find($id);
    return response()->json($event);
  }

  public function GetEventTitle($id)
  {
    $eventTitle = Event::where('id', $id)->value('title');
    return response()->json(['title' => $eventTitle]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $event = Event::find($id);
    return response()->json($event);
  }


  public function getEventdetails($id)
  {
    $data = DB::table('events')->where('id', '=', $id)->get();
    return $data;
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

    $method = "EventsController@update";
    // $this->ValidateEvent($request);

    $event = Event::find($id);

    $logged_in_user = $request->user()->name;
    $registra = $event->event_registra;

    //  if(Gate::allows('update-event',$registra)){

    $title = $request->input('event-title');
    $startDate = $request->input('eventStart-date');
    $endDate = $request->input('eventEnd-date');
    $time = $request->input('event-time');
    $event_description = $request->input('event-message');

    $event_description = preg_replace("/^<p.*?>/", "", $event_description);
    $event_description = preg_replace("|</p>$|", "", $event_description);
    $time = preg_replace('/\s+/', '', $time);



    $event_details = $this->getEventdetails($id);
    foreach ($event_details as $data) {
      $this->old_title = $data->title;
      $this->old_content = $data->description;
      $this->old_sdate = $data->start_date;
      $this->old_edate = $data->end_date;
      $this->old_stime = $data->start_time;
    }

    switch (true) {
      case ($title != $this->old_title):
        $changes[] = "changed " . $this->old_title . "";
        break;
      case ($startDate != $this->old_sdate):
        $changes[] = "changed " . $this->old_sdate . "";
        break;
      case ($endDate != $this->old_edate):
        $changes[] = "changed " . $this->old_edate . "";
        break;
      case ($time != $this->old_stime):
        $changes[] = "changed " . $this->old_stime . "";
        break;
    }

    $event->title = $title;
    $event->start_date = $startDate;
    $event->end_date = $endDate;
    $event->start_time = $time;
    $event->description = $event_description;
    $event->event_registra = $logged_in_user;
    $registraEmail = Auth::user()->email;
    $registraPhone = Auth::user()->contact;
    $subject = "Updated Event " . $this->old_title . "";

    $event_update_status = $event->save();

    if ($event_update_status) {

      $users = User::all();

      ($endDate == "") ?
        $endDate_of_event = "the same day" : $endDate_of_event = $endDate;
      $user_emails = $this->getUserEmails();

      $data = array(
        'title' => $title,
        'description' => $event_description,
        'start_date' => $startDate,
        'end_date' => $endDate_of_event,
        'time' => $time,
        'registra' => $logged_in_user,
        'registra_email' => $registraEmail,
        'registraMobileNo' => $registraPhone,
        'subject' => $subject,
        'email' => $user_emails,
      );

      $action = "updated an event with the title " . $title . "";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => $method
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      $notified = Notification::send($users, new EventNotifier($data));

      $this->EnqueueEvent($data);
      $sessionVariable = 'success';
      $responseInfo = $this->SuccessMessage($action);
    } else {
      $messageErr = 'Event has not been updated!';
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => $method
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);
    }

    // }
    //  else
    //  {
    //   $messageErr = 'You cannot edit this event since you are not the one who created it!';
    //   $dataArr = array("code" => '101',
    //   "message" => $messageErr,
    //   "method" => $method);
    //   LogAfterRequest::LogRequest($request, $dataArr);
    //   $sessionVariable = 'fail';
    //   $responseInfo = $this->FailedMessage($messageErr);

    // }

    $arr = $this->EventsReview();

    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_no' => $arr['totl'],
      ]);
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {

    $event = Event::find($id);
    $method = "EventsController@destroy";
    $registra = $event->event_registra;

    //  if(Gate::allows('delete-event',$registra)){

    $title = $event->title;
    $event_delete_status = $event->delete();

    if ($event_delete_status) {

      $action = "deleted an event with the title '" . $title . "'";
      LogsController::logger($request, $action, now());

      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => $method
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      $sessionVariable = 'success';
      $responseInfo = $this->SuccessMessage($action);
    } else {

      $messageErr = "Event not deleted!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => $method
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);
    }

    $arr = $this->EventsReview();

    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_no' => $arr['totl'],
      ]);


    //  }

  }

  protected function EventsReview()
  {
    $totl = Event::count();
    $data = array(
      'totl' => $totl,
    );
    return $data;
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

  protected function SuccessMessage($action)
  {
    $message = "You have successfully " . $action . "";
    return $message;
  }

  protected function FailedMessage($failmsg)
  {
    return $failmsg;
  }
} // end of the class
