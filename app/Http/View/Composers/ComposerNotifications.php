<?php


namespace App\Http\View\Composers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ComposerNotifications{

  public function compose(View $view){

  if(Auth::check()){

     $notifications = Auth::User()->unreadNotifications;


       $MailNotifications = DB::table('notifications')
                             ->where('notifiable_id', Auth::user()->id)
                             ->where('read_at', null)->get();

 $data = array();

  foreach ($notifications as $notification) {

    $data[] = $notification->data;

    if(!empty($data)){
        $view->with('notifications', $data);
        $view->with('EmaiNotifications', $data);

    }
  }

  foreach ($MailNotifications as $notification) {
    $notifydata = json_decode($notification->data, true);
    //dd($notifydata);
   if(isset($notifydata)){

    // foreach($notifydata as $key){
    //     $title = $key->title;
    //     $subject = $key->subject;
    //     $description = $key->message;
    //     $recordedOn = $key->created_at;
    // }

    $view->with('EmaiNotifications', $notifydata);
}

    }



  }


}

}
