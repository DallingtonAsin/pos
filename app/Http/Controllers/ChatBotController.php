<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use BotMan\BotMan\Messages\Incoming\Answer;
use App\Http\Controllers\LogAfterRequest;
use App\Models\ChatBot;


class ChatBotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chatStoredCommands = ChatBot::all();
        $number_of_Commands = DB::table("chatbox")->count();
        return view("pages.chatbot.index")
               ->with(compact('chatStoredCommands', 'number_of_Commands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("pages.chatbot.create");
    }

    protected function ValidateCommand(Request $request)
    {
        $request->validate([
            'command' => 'required|string',
            'response' => 'required|string',
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
        
        $this->ValidateCommand($request);
        $chatCommand = new ChatBot;
        $command = $request->input("command");
        $response = $request->input("response");

        $chatCommand->chat_command = $command;
        $chatCommand->chat_response = $response;

        $result = $chatCommand->save();
        if($result){

            $action = "recorded a new command ".$command." in the system";
            LogsController::logger($request, $action, now());
            $dataArr = array("code" => '200',
            "message" => $action,
            "method" => "ChatBotController@store");
            LogAfterRequest::LogRequest($request, $dataArr);

            return back()
                      ->with('success', $this->ActionMessage($action));

        }else{
            $messageErr = 'New command has not been recorded!';
            $dataArr = array("code" => '101',
            "message" => $messageErr,
            "method" => "ChatBotController@store");
            LogAfterRequest::LogRequest($request, $dataArr);
            return back()
                       ->with('fail', $messageErr);

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->ValidateCommand($request);
        $chatCommand = ChatBot::find($id);

        $oldCommand = $chatCommand->chat_command;
        $command = $request->input("command");
        $response = $request->input("response");

        $chatCommand->chat_command = $command;
        $chatCommand->chat_response = $response;

        $result = $chatCommand->save();
        if($result){

            $action = "updated command ".$oldCommand." in the system";
            LogsController::logger($request, $action, now());
            $dataArr = array("code" => '200',
            "message" => $action,
            "method" => "ChatBotController@update");
            LogAfterRequest::LogRequest($request, $dataArr);

            return back()
                      ->with('success', $this->ActionMessage($action));

        }
        else{
            $messageErr = 'Command update has failed!';
            $dataArr = array("code" => '101',
            "message" => $messageErr,
            "method" => "ChatBotController@update");
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

        $chatCommand = ChatBot::find($id);
        $command = $chatCommand->chat_command;

        $result = $chatCommand->delete();
        if($result){

            $action = "removed command ".$command." from the system";
            LogsController::logger($request, $action, now());
            $dataArr = array("code" => '200',
            "message" => $action,
            "method" => "ChatBotController@destroy");
            LogAfterRequest::LogRequest($request, $dataArr);

            return back()
                      ->with('success', $this->ActionMessage($action));

        }
        else{

            $messageErr = 'Command has not been removed!';
            $dataArr = array("code" => '101',
            "message" => $messageErr,
            "method" => "ChatBotController@destroy");
            LogAfterRequest::LogRequest($request, $dataArr);
            return back()
                       ->with('fail', $messageErr);

        }
    }

    public function truncateChatBotCommands(Request $request)
    {
      
        $method = "ChatBotController@truncateCommands";   
        $isTruncated = ChatBot::truncate();
        if($isTruncated){

       $action = "removed all chatbot commands from the system";
       LogsController::logger($request, $action, now());
       
       $dataArr = array("code" => '200',
       "message" => $action,
       "method" => $method);
       LogAfterRequest::LogRequest($request, $dataArr);

       return back()
                    ->with("success", $this->ActionMessage($action));
     }
     else
     {
       $error_message = "commands not removed from the system!";
       $dataArr = array("code" => '101',
       "message" => $error_message,
       "method" => $method);
       LogAfterRequest::LogRequest($request, $dataArr);
       return back()
                  ->with('fail', $error_message);
    }

  }


    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        /*$botman->hears('give me a file', function($botman) {

            $message = Message::create("This is my image")
                               ->image("https://images.app.goo.gl/fuUfbif1r5LKoyFp9");

            $botman->reply($message);

        });*/

        $botman->hears('{message}', function($botman, $message) {

            $commandArr = $this->getCommands();
           if ($message == 'hi') {
                $this->askName($botman);
            }
            else if(in_array($message, $commandArr))
            {
                $response = $this->getCommandResponse($message);
                $botman->reply($response);
            }

            else{
                   ;
                    $botman->reply("Sorry, i don't understand this command.<br>".$this->PrintCommands()."");

            }

        });

        $botman->receivesImages(function($botman, $images){

        });

        $botman->fallback(function($bot) {
            $bot->reply("Sorry, I did not understand these commands.
            Here is a list of commands I understand: ...<br>".$this->PrintCommands()."");
        });

        $botman->listen();
    }

    /**
     * Place your BotMan logic here.
     */
    public function askName($botman)
    {
        $botman->ask('Hello! What is your Name?', function(Answer $answer) {

            $name = $answer->getText();

            $this->say('Nice to meet you '.$name);
        });
    }


    protected function getCommands()
    {
        $chats = ChatBot::all();
        $commandsArray = array();
        foreach($chats as $item){
         array_push($commandsArray, $item->chat_command);

        }
        return $commandsArray;
}

  protected function PrintCommands()
  {
      $commands = $this->getCommands();
      $counter = 1;
      $commandStr = array();
      if(count($commands) > 0){
      for($x=0; $x<count($commands); $x++){
          array_push($commandStr, "".$counter.".".$commands[$x]."");
          $counter++;
      }
      return "The available
      commands are <br>".implode("<br>", $commandStr);
    }else{
        return "No available commands as of now";
    }
  }


    protected function getCommandResponse($command)
    {
        $response = ChatBot::where('chat_command', $command)
                            ->value("chat_response");
        return $response;
    }

    protected function ChatDictionary()
    {
        $arr = ChatBot::all();
        foreach($arr as $item){
           $dict = array(
               $item->chat_command => $item->chat_response
           );
        }
        return $dict;

    }

    protected function ActionMessage($action){
  $message = "You have successfully ".$action."";
  return $message;
}





}


