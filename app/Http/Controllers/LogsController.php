<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Models\Logs;
use App\Models\Role;
use App\Helpers\Helper;
use App\DataTables\LogsDataTable;

class LogsController extends Controller
{



    public function __construct()
    {

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(){

    // $response = Gate::inspect('isCashier');
    //  if($response->allowed()){
    //     $logs = Logs::where('name', Auth::user()->name)
    //     ->get();
    //     $number_of_logs = Logs::where('name', Auth::user()->name)
    //     ->count();
    // }
    // else
    // {
        $logs = Logs::all();
        $number_of_logs = Logs::count();
    //}

    return view('pages.logs.index')->with(compact('logs','number_of_logs'));


    }

    public function GetLogs(LogsDataTable $dataTable)
    {
        return $dataTable->render('pages.logs.index');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $logs = Logs::find($id);
        return response()->json($logs);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        $log = Logs::find($id);
        $actionResponse = $log->delete();
        if($actionResponse)
        {
            $sessionVariable = 'success';
            $responseInfo = 'log has been deleted successfully';
        }else
        {
            $sessionVariable = 'fail';
            $responseInfo  = 'log has not been deleted';
       }

            return response()>json([
                $sessionVariable => $responseInfo,
                'totl' => $this->GetLogsStats(),
            ]);

}

   protected function GetLogsStats(){
       $totl = Logs::count();
       return $totl;
   }

    public function truncateLogs(){

        // $response = Gate::inspect('isLogMaster');
        //
        // if($response->allowed()){

        $res = Logs::truncate();
          if($res){
            $action = 'You have successfully deleted all logs';
            $sessionVariable = 'success';
            $responseInfo = $action;
           }
           else{
            $sessionVariable = 'fail';
            $responseInfo = 'logs have not been deleted';
           }
      // }
      // else{
      //   $sessionVariable = 'fail';
      //   $responseInfo = 'Permissions denied';
      // }

      $totl = $this->GetLogsStats();

      return response()
      ->json([$sessionVariable => $responseInfo,
              'totl' => $totl,
      ]);


    }


      public function readFileContents(){
        $filename = storage_path("logs/logs.log");
        $file_contents= array();

        if(file_exists($filename) && is_readable($filename)){
            $fileResource = fopen($filename, "r");
            if($fileResource){
                while(($line = fgets($fileResource)) !== false){
                  $file_contents[] = $line;
                }
            }
        }

    }



public static function logger(Request $request, $action, $date){

    $newLog = new Logs();
    $newLog->name =$name =  $request->user()->name;
    $newLog->role = $userPosition = LogsController::getRole($request->user()->user_role);
    $newLog->logged_action = $action;
    $newLog->ip_address = \Request::getClientIp();
    $newLog->date = $date;

    $newLog->save();
    Log::channel('poslogs')->notice("".$userPosition." ".$name." ".$action."");

}

protected static function getRole($id)
{
  $role = Role::where('role_id', $id)->value('role');
  return $role;
}


}
